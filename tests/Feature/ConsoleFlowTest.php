<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Mail\ConsentRequestMail;
use App\Models\Company;
use App\Models\Reassignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ConsoleFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_deployer_deploys_a_company_with_its_console_account(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->superAdmin()->create())
            ->post(route('deployer.companies.store'), [
                'console_language' => 'es',
                'interface_color' => '#FF5500',
                'logo' => UploadedFile::fake()->image('logo.png', 250, 120),
                'name' => 'Proctor Gallagher',
                'contact_person' => 'Jordan Lee',
                'preferred_language' => 'en',
                'addresses' => ['1 Main Street', ''],
                'phones' => ['+1 555 0100', '+1 555 0101'],
                'emails' => ['hello@example.com'],
                'city' => 'Phoenix',
                'country' => 'United States',
                'time_zone' => 'America/Phoenix',
                'text_opt_in' => '1',
                'admin_name' => 'Jordan Lee',
                'admin_email' => 'jordan@example.com',
                'admin_password' => 'secret-password',
            ])
            ->assertRedirect(route('deployer.companies.show', 'proctor-gallagher'));

        $company = Company::firstWhere('slug', 'proctor-gallagher');
        $this->assertSame('#ff5500', $company->interface_color);
        $this->assertSame(['1 Main Street'], $company->addresses);
        $this->assertCount(2, $company->phones);
        Storage::disk('public')->assertExists($company->logo_path);
        $this->assertTrue($company->admins()->where('email', 'jordan@example.com')->exists());
    }

    public function test_logo_larger_than_250_by_120_is_rejected(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create())
            ->post(route('deployer.companies.store'), [
                'console_language' => 'en',
                'interface_color' => '#2563eb',
                'logo' => UploadedFile::fake()->image('logo.png', 400, 200),
                'name' => 'Too Big',
                'contact_person' => 'A',
                'preferred_language' => 'en',
                'time_zone' => 'UTC',
                'admin_name' => 'A',
                'admin_email' => 'a@example.com',
                'admin_password' => 'secret-password',
            ])
            ->assertSessionHasErrors('logo');
    }

    public function test_suspend_and_reactivate_a_company(): void
    {
        $company = Company::factory()->create();
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->patch(route('deployer.companies.status', $company));
        $this->assertFalse($company->fresh()->isActive());

        $this->actingAs($superAdmin)->patch(route('deployer.companies.status', $company));
        $this->assertTrue($company->fresh()->isActive());
    }

    public function test_company_adds_edits_and_removes_a_coach(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->companyAdmin($company)->create();

        $this->actingAs($admin)->post(route('company.coaches.store'), [
            'name' => 'Coach A',
            'email' => 'coach-a@example.com',
            'password' => 'secret-password',
        ])->assertRedirect();

        $coach = User::firstWhere('email', 'coach-a@example.com');
        $this->assertSame(Role::Coach, $coach->role);
        $this->assertSame($company->id, $coach->company_id);

        $this->actingAs($admin)->put(route('company.coaches.update', $coach), [
            'name' => 'Coach A Renamed',
            'email' => 'coach-a@example.com',
            'phone' => '+1 555 0199',
        ])->assertRedirect();
        $this->assertSame('Coach A Renamed', $coach->fresh()->name);

        User::factory()->clientOf($coach)->create();
        $this->actingAs($admin)->delete(route('company.coaches.destroy', $coach))
            ->assertRedirect(route('company.reassign.show', $coach));
        $this->assertNotSoftDeleted($coach);
    }

    public function test_reassign_moves_only_clients_who_consented(): void
    {
        Mail::fake();

        $company = Company::factory()->create();
        $admin = User::factory()->companyAdmin($company)->create();
        $leaving = User::factory()->coach($company)->create();
        $newCoach = User::factory()->coach($company)->create();
        $agrees = User::factory()->clientOf($leaving)->create();
        $waits = User::factory()->clientOf($leaving)->create();

        $this->actingAs($admin)->post(route('company.reassign.consent', $leaving), ['to_coach_id' => $newCoach->id])->assertRedirect();
        Mail::assertSent(ConsentRequestMail::class, 2);

        $consent = Reassignment::where('client_id', $agrees->id)->firstOrFail();
        $this->get(route('consent.show', $consent->token))->assertForbidden();

        $this->get($consent->consentUrl())->assertOk()->assertSee('I agree');
        $signedPost = URL::temporarySignedRoute('consent.store', now()->addHour(), ['reassignment' => $consent->token]);
        $this->post($signedPost)->assertRedirect();
        $this->assertSame(Reassignment::GIVEN, $consent->fresh()->status);

        $this->actingAs($admin)->post(route('company.reassign.store', $leaving), ['to_coach_id' => $newCoach->id])->assertRedirect();

        $this->assertSame($newCoach->id, $agrees->fresh()->coach_id);
        $this->assertSame($leaving->id, $waits->fresh()->coach_id);
        $this->assertSame(Reassignment::COMPLETED, $consent->fresh()->status);
    }

    public function test_coach_creates_a_client_with_a_private_note(): void
    {
        $coach = User::factory()->coach(Company::factory()->create())->create();

        $this->actingAs($coach)->post(route('coach.clients.store'), [
            'name' => 'New Client',
            'email' => 'new-client@example.com',
            'birthday' => '1990-05-04',
            'title' => 'Mx.',
            'notes' => 'First session went well.',
        ])->assertRedirect();

        $client = User::firstWhere('email', 'new-client@example.com');
        $this->assertSame(Role::Client, $client->role);
        $this->assertSame($coach->id, $client->coach_id);
        $this->assertSame($coach->company_id, $client->company_id);
        $this->assertSame('First session went well.', $coach->authoredNotes()->first()->body);
    }

    public function test_client_profile_requires_name_email_and_birthday(): void
    {
        $coach = User::factory()->coach(Company::factory()->create())->create();

        $this->actingAs($coach)->post(route('coach.clients.store'), [])
            ->assertSessionHasErrors(['name', 'email', 'birthday']);
    }

    public function test_coach_sends_an_email_message(): void
    {
        Mail::fake();

        $coach = User::factory()->coach(Company::factory()->create())->create();
        $client = User::factory()->clientOf($coach)->create();

        $this->actingAs($coach)->post(route('coach.messages.store'), [
            'client_id' => $client->id,
            'channel' => 'email',
            'subject' => 'Checking in',
            'body' => 'How are you?',
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseHas('messages', ['client_id' => $client->id, 'channel' => 'email']);
    }

    public function test_coach_cannot_message_another_coaches_client(): void
    {
        $company = Company::factory()->create();
        $coach = User::factory()->coach($company)->create();
        $client = User::factory()->clientOf(User::factory()->coach($company)->create())->create();

        $this->actingAs($coach)->post(route('coach.messages.store'), [
            'client_id' => $client->id,
            'channel' => 'text',
            'body' => 'Hi',
        ])->assertSessionHasErrors('client_id');
    }

    public function test_opening_a_client_clears_the_check_in_nudge(): void
    {
        $coach = User::factory()->coach(Company::factory()->create())->create();
        $client = User::factory()->clientOf($coach)->create(['coach_viewed_at' => null]);

        $this->actingAs($coach)->get(route('coach.clients.index'))->assertSee('Open profile');
        $this->actingAs($coach)->get(route('coach.clients.show', $client))->assertOk();

        $this->assertNotNull($client->fresh()->coach_viewed_at);
    }
}

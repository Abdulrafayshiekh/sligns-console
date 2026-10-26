<?php

namespace Tests\Feature;

use App\Enums\SuiteApp;
use App\Models\AppActivity;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_role_only_reaches_its_own_console(): void
    {
        $company = Company::factory()->create();
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->companyAdmin($company)->create();
        $coach = User::factory()->coach($company)->create();

        $this->actingAs($admin)->get('/deployer/home')->assertForbidden();
        $this->actingAs($admin)->get('/coach/clients')->assertForbidden();
        $this->actingAs($coach)->get('/company/dashboard')->assertForbidden();
        $this->actingAs($coach)->get('/deployer/home')->assertForbidden();
        $this->actingAs($superAdmin)->get('/company/dashboard')->assertForbidden();
        $this->actingAs($superAdmin)->get('/coach/clients')->assertForbidden();
    }

    public function test_coach_cannot_open_another_coaches_client(): void
    {
        $company = Company::factory()->create();
        $coach = User::factory()->coach($company)->create();
        $other = User::factory()->coach($company)->create();
        $client = User::factory()->clientOf($other)->create();

        $this->actingAs($coach)->get(route('coach.clients.show', $client))->assertForbidden();
        $this->actingAs($coach)->get(route('coach.clients.app', [$client, 'momentum']))->assertForbidden();
        $this->actingAs($coach)->post(route('coach.notes.store', $client), ['body' => 'x'])->assertForbidden();
        $this->actingAs($other)->get(route('coach.clients.show', $client))->assertOk();
    }

    public function test_company_cannot_manage_coaches_of_another_company(): void
    {
        $admin = User::factory()->companyAdmin(Company::factory()->create())->create();
        $foreignCoach = User::factory()->coach(Company::factory()->create())->create();

        $this->actingAs($admin)->get(route('company.reassign.show', $foreignCoach))->assertForbidden();
        $this->actingAs($admin)->delete(route('company.coaches.destroy', $foreignCoach))->assertForbidden();
    }

    public function test_super_deployer_never_sees_client_identities(): void
    {
        $company = Company::factory()->create();
        $coach = User::factory()->coach($company)->create();
        $client = User::factory()->clientOf($coach)->create(['name' => 'Very Private Person']);

        $this->actingAs(User::factory()->superAdmin()->create())
            ->get(route('deployer.companies.show', $company))
            ->assertOk()
            ->assertSee('Clients')
            ->assertDontSee('Very Private Person')
            ->assertDontSee($client->email);
    }

    public function test_company_dashboard_shows_aggregates_not_client_names(): void
    {
        $company = Company::factory()->create();
        $coach = User::factory()->coach($company)->create();
        $client = User::factory()->clientOf($coach)->create(['name' => 'Very Private Person', 'country' => 'Pakistan']);
        AppActivity::create(['client_id' => $client->id, 'app' => SuiteApp::Momentum, 'activity_date' => today(), 'value' => 7]);

        $this->actingAs(User::factory()->companyAdmin($company)->create())
            ->get(route('company.dashboard'))
            ->assertOk()
            ->assertSee('Pakistan')
            ->assertDontSee('Very Private Person');
    }

    public function test_private_notes_are_visible_only_to_their_author(): void
    {
        $company = Company::factory()->create();
        $coach = User::factory()->coach($company)->create();
        $client = User::factory()->clientOf($coach)->create();

        $this->actingAs($coach)->post(route('coach.notes.store', $client), ['body' => 'Secret coaching note'])->assertRedirect();
        $this->actingAs($coach)->get(route('coach.clients.show', $client))->assertSee('Secret coaching note');

        $newCoach = User::factory()->coach($company)->create();
        $client->update(['coach_id' => $newCoach->id]);

        $this->actingAs($newCoach)->get(route('coach.clients.show', $client))->assertOk()->assertDontSee('Secret coaching note');
    }

    public function test_script_content_shows_only_when_shared(): void
    {
        $company = Company::factory()->create();
        $coach = User::factory()->coach($company)->create();
        $client = User::factory()->clientOf($coach)->create();

        AppActivity::create(['client_id' => $client->id, 'app' => SuiteApp::ScriptBuilder, 'activity_date' => today(), 'label' => 'Career', 'quantity' => 1, 'content' => 'Shared script text', 'shared' => true]);
        AppActivity::create(['client_id' => $client->id, 'app' => SuiteApp::ScriptBuilder, 'activity_date' => today(), 'label' => 'Wealth', 'quantity' => 1, 'content' => 'Hidden script text', 'shared' => false]);

        $this->actingAs($coach)->get(route('coach.clients.app', [$client, 'script-builder']))
            ->assertOk()
            ->assertSee('Shared script text')
            ->assertDontSee('Hidden script text');
    }
}

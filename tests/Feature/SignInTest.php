<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SignInTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_deployer_signs_in_with_master_key(): void
    {
        User::factory()->superAdmin()->create(['email' => 'admin@aligna.ai', 'password' => 'master-key']);

        $this->post('/deployer/sign-in', ['email' => 'admin@aligna.ai', 'master_key' => 'master-key'])
            ->assertRedirect(route('deployer.home'));

        $this->assertAuthenticated();
    }

    public function test_coach_cannot_use_the_deployer_sign_in(): void
    {
        $coach = User::factory()->coach(Company::factory()->create())->create();

        $this->post('/deployer/sign-in', ['email' => $coach->email, 'master_key' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_company_and_coach_land_on_their_own_console(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->companyAdmin($company)->create();
        $coach = User::factory()->coach($company)->create();

        $this->post('/sign-in', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('company.dashboard'));
        $this->post('/sign-out');

        $this->post('/sign-in', ['email' => $coach->email, 'password' => 'password'])
            ->assertRedirect(route('coach.clients.index'));
    }

    public function test_clients_cannot_sign_in_to_the_console(): void
    {
        $coach = User::factory()->coach(Company::factory()->create())->create();
        $client = User::factory()->clientOf($coach)->create();

        $this->post('/sign-in', ['email' => $client->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_suspended_company_is_locked_out(): void
    {
        $company = Company::factory()->suspended()->create();
        $coach = User::factory()->coach($company)->create();

        $this->post('/sign-in', ['email' => $coach->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_signed_in_users_are_signed_out_when_their_company_is_suspended(): void
    {
        $company = Company::factory()->create();
        $coach = User::factory()->coach($company)->create();

        $this->actingAs($coach)->get('/coach/clients')->assertOk();

        $company->update(['status' => Company::SUSPENDED]);

        $this->actingAs($coach->fresh())->get('/coach/clients')->assertRedirect(route('sign-in'));
        $this->assertGuest();
    }
}

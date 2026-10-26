<?php

namespace Tests\Feature;

use App\Enums\Theme;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_sign_in_page_uses_the_aligna_theme_by_default(): void
    {
        $this->get('/sign-in')
            ->assertOk()
            ->assertSee('data-theme="aligna"', false)
            ->assertSee('value="night"', false)
            ->assertDontSee('value="company"', false);
    }

    public function test_guest_choice_is_kept_in_a_cookie(): void
    {
        $this->from('/sign-in')
            ->put('/theme', ['theme' => 'night'])
            ->assertRedirect('/sign-in')
            ->assertCookie('console_theme', 'night');

        $this->withCookie('console_theme', 'night')
            ->get('/sign-in')
            ->assertSee('data-theme="night"', false)
            ->assertSee('data-bs-theme="dark"', false);
    }

    public function test_signed_in_choice_is_saved_on_the_account(): void
    {
        $company = Company::factory()->create(['interface_color' => '#1d4ed8']);
        $coach = User::factory()->coach($company)->create();

        $this->actingAs($coach)
            ->putJson('/theme', ['theme' => 'company'])
            ->assertNoContent();

        $this->assertSame(Theme::Company, $coach->fresh()->theme);

        $this->get('/coach/clients')
            ->assertSee('data-theme="company"', false)
            ->assertSee('--company-brand: #1d4ed8', false);
    }

    public function test_company_theme_falls_back_without_a_company(): void
    {
        $admin = User::factory()->superAdmin()->create(['theme' => Theme::Company]);

        $this->actingAs($admin)
            ->get('/deployer/home')
            ->assertSee('data-theme="aligna"', false)
            ->assertDontSee('value="company"', false);
    }

    public function test_unknown_theme_is_rejected(): void
    {
        $this->putJson('/theme', ['theme' => 'neon'])->assertUnprocessable();
    }
}

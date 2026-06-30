<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRoleViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_operator_only_sees_finance_dashboard_actions(): void
    {
        $user = User::factory()->create([
            'username' => 'finance-test',
            'role' => User::ROLE_KEUANGAN,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Catat kas');
        $response->assertSee('Kas dan iuran');
        $response->assertDontSee('Input kegiatan');
        $response->assertDontSee('Member perlu perhatian');
    }

    public function test_mentor_sees_coaching_dashboard_actions_without_finance_action(): void
    {
        $user = User::factory()->create([
            'username' => 'mentor-test',
            'role' => User::ROLE_MENTOR,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Input kegiatan');
        $response->assertSee('Member perlu perhatian');
        $response->assertDontSee('Catat kas');
        $response->assertDontSee('Kas dan iuran');
    }
}

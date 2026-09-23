<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\User;
use App\Models\WebsiteReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_sent_to_the_admin_login(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
        $this->get('/admin/login')->assertOk()->assertSee('Control room access');
    }

    public function test_customer_credentials_cannot_access_the_admin_console(): void
    {
        $customer = User::factory()->create([
            'email' => 'customer@example.com',
            'password' => 'Customer1234',
        ]);

        $this->post('/admin/login', [
            'email' => $customer->email,
            'password' => 'Customer1234',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_administrator_can_sign_in_and_see_operational_data(): void
    {
        $admin = User::factory()->create([
            'name' => 'Anuj Shukla',
            'email' => 'admin@webignitors.in',
            'password' => 'SecureAdmin1234',
            'is_admin' => true,
        ]);
        $customer = User::factory()->create();

        Inquiry::query()->create([
            'name' => 'Potential Client',
            'email' => 'client@example.com',
            'service' => 'web-development',
            'budget' => '₹1L–₹3L',
            'message' => 'We need a complex commerce platform.',
        ]);
        $report = WebsiteReport::query()->create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $customer->id,
            'requested_url' => 'https://example.com/',
            'domain' => 'example.com',
            'status' => 'queued',
            'current_stage' => 'Waiting for the audit worker',
            'progress' => 2,
        ]);

        $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'SecureAdmin1234',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->get('/admin')
            ->assertOk()
            ->assertSee('Anuj')
            ->assertSee('example.com')
            ->assertSee('Potential Client')
            ->assertSee('The audit worker needs attention.');

        $this->get(route('reports.show', $report))->assertOk();
    }

    public function test_admin_create_command_creates_a_verified_administrator(): void
    {
        $this->artisan('admin:create', [
            'email' => 'owner@webignitors.in',
            'name' => 'Anuj Shukla',
        ])
            ->expectsQuestion('Choose a secure administrator password', 'StrongAdmin1234')
            ->expectsQuestion('Confirm the administrator password', 'StrongAdmin1234')
            ->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => 'owner@webignitors.in',
            'is_admin' => true,
        ]);
        $this->assertNotNull(User::query()->where('email', 'owner@webignitors.in')->value('email_verified_at'));
    }
}

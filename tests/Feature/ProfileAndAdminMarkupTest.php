<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileAndAdminMarkupTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_preview_route_renders_for_guests(): void
    {
        $this->get('/profil-preview')
            ->assertOk()
            ->assertSee('Preview User');
    }

    public function test_profile_page_does_not_render_merge_artifacts(): void
    {
        $user = User::factory()->create(['role' => 'donatur']);

        $response = $this->actingAs($user)->get(route('profil.edit'));

        $response->assertOk();
        $content = $response->getContent();

        $this->assertStringNotContainsString('color: white;">>', $content);
        $this->assertDoesNotMatchRegularExpression(
            '/<div class="absolute bottom-6 left-8 flex items-center gap-4">\s*<div class="absolute inset-0 bg-black\/30">/s',
            $content
        );
    }

    public function test_admin_user_role_modal_has_valid_form_nesting(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
        $managedUser = User::factory()->create(['role' => 'donatur', 'status' => 'aktif']);

        $response = $this->actingAs($admin)->get(route('admin.users.show', $managedUser));

        $response->assertOk();
        $content = $response->getContent();
        $roleAction = preg_quote(route('admin.users.role', $managedUser), '/');

        $this->assertDoesNotMatchRegularExpression(
            '/<div class="flex items-center justify-between rounded-t-2xl bg-\[#0B3B75\] px-6 py-4">\s*<form action="' . $roleAction . '"/s',
            $content
        );
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_profile_with_flash_message()
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);
        
        $this->actingAs($user);

        $response = $this->patch(route('profile.update'), [
            'name' => 'Updated Name',
            'email' => 'updated@example.test',
            'current_password' => 'old-password',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'profile-updated');

        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals('updated@example.test', $user->email);
    }

    public function test_user_can_update_profile_and_password()
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        $this->actingAs($user);

        $response = $this->patch(route('profile.update'), [
            'name' => 'New Name',
            'email' => 'newemail@example.test',
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'profile-updated');

        $user->refresh();

        $this->assertEquals('New Name', $user->name);
        $this->assertEquals('newemail@example.test', $user->email);
        $this->assertTrue(Hash::check('new-password', $user->password));
    }

    public function test_user_cannot_change_email_without_current_password()
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);
        
        $this->actingAs($user);

        $response = $this->patch(route('profile.update'), [
            'name' => 'Updated Name',
            'email' => 'newemail@example.test',
        ]);

        $response->assertSessionHasErrors('current_password');
        $user->refresh();
        $this->assertNotEquals('newemail@example.test', $user->email);
    }

    public function test_user_cannot_change_password_without_current_password()
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);
        
        $this->actingAs($user);

        $response = $this->patch(route('profile.update'), [
            'name' => 'Updated Name',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    public function test_user_can_update_profile_logout_and_login_with_new_credentials()
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);
        
        $this->actingAs($user);

        $response = $this->patch(route('profile.update'), [
            'name' => 'Persistent User',
            'email' => 'persistent@example.test',
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'profile-updated');

        $user->refresh();
        $this->assertEquals('Persistent User', $user->name);
        $this->assertEquals('persistent@example.test', $user->email);
        $this->assertTrue(Hash::check('new-password', $user->password));

        $this->post(route('logout'));
        $this->assertGuest();

        $loginResponse = $this->post(route('login'), [
            'email' => 'persistent@example.test',
            'password' => 'new-password',
        ]);

        $loginResponse->assertStatus(302);
        $this->assertAuthenticatedAs($user);
    }
}

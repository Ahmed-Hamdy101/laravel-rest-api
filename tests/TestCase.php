<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\Passport;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * Create and authenticate an admin user, return token.
     */
    protected function makeAdmin(): User
    {
        $role = Role::create(['name' => 'admin']);
        return User::factory()->create(['role_id' => $role->id]);
    }

    /**
     * Create and authenticate an editor user.
     */
    protected function makeEditor(): User
    {
        $role = Role::create(['name' => 'editor']);
        return User::factory()->create(['role_id' => $role->id]);
    }

    /**
     * Create a plain authenticated user with no special role.
     */
    protected function makeUser(): User
    {
        $role = Role::create(['name' => 'user']);
        return User::factory()->create(['role_id' => $role->id]);
    }

    /**
     * Return headers for an authenticated user using Passport.
     */
    protected function authHeaders(User $user): array
    {
        Passport::actingAs($user);
        return ['Accept' => 'application/json'];
    }
}

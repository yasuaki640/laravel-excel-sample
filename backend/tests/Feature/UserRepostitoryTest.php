<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UserRepostitoryTest
 * @package Tests\Feature
 */
class UserRepostitoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_同一のemailが存在すれば該当レコードをupdate()
    {
        $user = User::factory()->create([
            'name' => 'Robin',
            'email' => 'robin@robin.com',
            'sex' => User::SEX_MALE
        ]);

        User::upsert([
            'id' => $user->id,
            'name' => 'Nico Robin',
            'email' => 'robin@robin.com',
            'sex' => User::SEX_FEMALE,
            'password' => \Hash::make('password')
        ], ['id', 'email']);

        $this
            ->assertDatabaseMissing('users', [
                'name' => 'Robin',
                'email' => 'robin@robin.com',
                'sex' => User::SEX_MALE
            ])
            ->assertDatabaseHas('users', [
                'name' => 'Nico Robin',
                'email' => 'robin@robin.com',
                'sex' => User::SEX_FEMALE
            ]);
    }
}

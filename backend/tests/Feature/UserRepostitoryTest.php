<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UserRepostitoryTest
 * @package Tests\Feature
 */
class UserRepostitoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulkInsertできる()
    {
        Carbon::setTestNow('2017-01-02 09:59:59');

        $data = [
            ['name' => 'Ruffy', 'email' => 'ruffy@ruffy.com', 'sex' => User::SEX_MALE, 'password' => \Hash::make('password')],
            ['name' => 'Tonny', 'email' => 'tonny@tonny.com', 'sex' => User::SEX_MALE, 'password' => \Hash::make('password')],
            ['name' => 'Robin', 'email' => 'robin@robin.com', 'sex' => User::SEX_FEMALE, 'password' => \Hash::make('password')],
        ];

        User::upsert($data, ['id', 'email'], ['email']);

        $this
            ->assertDatabaseHas('users',
                ['name' => 'Ruffy', 'email' => 'ruffy@ruffy.com', 'sex' => User::SEX_MALE]
            )->assertDatabaseHas('users',
                ['name' => 'Tonny', 'email' => 'tonny@tonny.com', 'sex' => User::SEX_MALE],
            )->assertDatabaseHas('users',
                ['name' => 'Robin', 'email' => 'robin@robin.com', 'sex' => User::SEX_FEMALE],
            );
    }

    public function test_同一のemailが存在すれば該当レコードをupdate()
    {
        $user = User::factory()->create([
            'name' => 'Robin',
            'email' => 'robin@robin.com',
            'sex' => User::SEX_MALE
        ]);

        $updatedParams = [
            'id' => $user->id,
            'name' => 'Nico Robin',
            'email' => 'robin@robin.com',
            'sex' => User::SEX_FEMALE,
            'password' => \Hash::make('password')
        ];

        User::upsert($updatedParams, ['id', 'email']);

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

    public function test_同一のemailが存在すれば該当レコードをupdate_email変更()
    {
        $user = User::factory()->create([
            'name' => 'Robin',
            'email' => 'robin@robin.com',
            'sex' => User::SEX_MALE
        ]);

        $updatedParams = [
            'id' => $user->id,
            'name' => 'Nico Robin',
            'email' => 'nico@nico.com',
            'sex' => User::SEX_FEMALE,
            'password' => \Hash::make('password')
        ];

        User::upsert($updatedParams, ['id', 'email']);

        $this
            ->assertDatabaseMissing('users', [
                'name' => 'Robin',
                'email' => 'robin@robin.com',
                'sex' => User::SEX_MALE
            ])
            ->assertDatabaseHas('users', [
                'name' => 'Nico Robin',
                'email' => 'nico@nico.com',
                'sex' => User::SEX_FEMALE
            ]);
    }

    public function test_同一のemailが存在すれば該当レコードをupdate_updateカラムを絞る()
    {
        $user = User::factory()->create([
            'name' => 'Robin',
            'email' => 'robin@robin.com',
            'sex' => User::SEX_MALE
        ]);

        $updatedParams = [
            'id' => $user->id,
            'name' => 'Sanji',
            'email' => 'sanji@sanji.com',
            'sex' => User::SEX_MALE,
            'password' => \Hash::make('password')
        ];

        User::upsert($updatedParams, ['id', 'email']);

        $this
            ->assertDatabaseMissing('users', [
                'name' => 'Robin',
                'email' => 'robin@robin.com',
                'sex' => User::SEX_MALE
            ])
            ->assertDatabaseHas('users', [
                'name' => 'sanji',
                'email' => 'sanji@sanji.com',
                'sex' => User::SEX_MALE
            ]);
    }
}

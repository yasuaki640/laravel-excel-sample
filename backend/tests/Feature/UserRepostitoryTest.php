<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Database\QueryException;
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

        User::upsert($data, ['id', 'email']);

        $this
            ->assertDatabaseHas('users',
                ['name' => 'Ruffy', 'email' => 'ruffy@ruffy.com', 'sex' => User::SEX_MALE, 'created_at' => now(), 'updated_at' => now()]
            )->assertDatabaseHas('users',
                ['name' => 'Tonny', 'email' => 'tonny@tonny.com', 'sex' => User::SEX_MALE, 'created_at' => now(), 'updated_at' => now()]
            )->assertDatabaseHas('users',
                ['name' => 'Robin', 'email' => 'robin@robin.com', 'sex' => User::SEX_FEMALE, 'created_at' => now(), 'updated_at' => now()]
            );
    }

    public function test_bulkInsertできる_timestampに値が入る()
    {
        Carbon::setTestNow('2017-01-02 09:59:59');

        User::upsert([
            'name' => 'Ruffy',
            'email' => 'ruffy@ruffy.com',
            'sex' => User::SEX_MALE,
            'password' => \Hash::make('password')
        ], ['id', 'email']);

        $this
            ->assertDatabaseHas('users', [
                'name' => 'Ruffy',
                'email' => 'ruffy@ruffy.com',
                'sex' => User::SEX_MALE,
                'created_at' => now(),
                'updated_at' => now()
            ]);
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
            'sex' => User::SEX_FEMALE
        ]);

        $updatedParams = [
            'id' => $user->id,
            'name' => 'Sanji',
            'email' => 'sanji@sanji.com',
            'sex' => User::SEX_MALE,
            'password' => \Hash::make('password')
        ];

        User::upsert($updatedParams, ['id', 'email'], ['name']);

        $this
            ->assertDatabaseMissing('users', [
                'name' => 'Robin',
                'email' => 'robin@robin.com',
                'sex' => User::SEX_FEMALE
            ])
            ->assertDatabaseHas('users', [
                'name' => 'Sanji',
                'email' => 'robin@robin.com',
                'sex' => User::SEX_FEMALE,
                'updated_at' => now()
            ]);
    }

    public function test_DBファサードのinsertはtimestampにnullが入る()
    {
        DB::table('users')->insert(['name' => 'Robin', 'email' => 'robin@robin.com', 'sex' => User::SEX_FEMALE, 'password' => \Hash::make('password')],);

        $this->assertDatabaseHas('users', [
            'name' => 'Robin',
            'email' => 'robin@robin.com',
            'sex' => User::SEX_FEMALE,
            'created_at' => null,
            'updated_at' => null
        ]);
    }

    public function test_DBファサードのinsertはuniqueがかぶればエラーになる()
    {
        $this->expectException(QueryException::class);

        User::factory()->create(['name' => 'Robin', 'email' => 'robin@robin.com', 'sex' => User::SEX_MALE]);

        DB::table('users')->insert(['name' => 'Robin', 'email' => 'robin@robin.com', 'sex' => User::SEX_FEMALE, 'password' => \Hash::make('password')],);

        $this->assertDatabaseHas('users', [
            'name' => 'Robin',
            'email' => 'robin@robin.com',
            'sex' => User::SEX_FEMALE,
            'created_at' => null,
            'updated_at' => null
        ]);
    }
}

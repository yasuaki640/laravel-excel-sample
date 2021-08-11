<?php

namespace Tests\Feature;

use App\Exports\UsersExport;
use App\Http\Controllers\UserController;
use App\Jobs\NotifyUserOfCompletedExport;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Facades\Excel;
use Storage;
use Tests\TestCase;

class UserControllerTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_showDownloadForm()
    {
        $response = $this->get(route('users.excel.export.download-form'));

        $response
            ->assertOk()
            ->assertViewIs('excel.index');
    }

    /**
     * @return void
     */
    public function test_download()
    {
        Excel::fake();

        User::factory()->count(10)->create();

        $response = $this->get(route('users.excel.export.download'));

        $response->assertOk();

        Excel::assertDownloaded(UsersExport::FILE_NAME, function (UsersExport $export) {
            return $export->collection()->count() === 10;
        });
    }

    /**
     * @return void
     */
    public function test_download_fail_download()
    {
        Excel::fake();

        // Mock excel facade and make it throw an exception
        Excel::shouldReceive('download')
            ->andThrow(new \PhpOffice\PhpSpreadsheet\Exception());

        $response = $this->get(route('users.excel.export.download'));

        $response
            ->assertSessionHasErrors()
            ->assertRedirect(route('users.excel.export.download-form'));
    }

    /**
     * @return void
     */
    public function test_queueExport_success()
    {
        Excel::fake();
        Storage::fake(UserController::STORAGE_S3);

        $users = User::factory()
            ->count(10)
            ->create();

        $response = $this->actingAs($users->first())
            ->get(route('users.excel.export.queue'));

        $response
            ->assertOk()
            ->assertViewHas('message');

        Excel::assertQueued(
            UsersExport::FILE_NAME,
            UserController::STORAGE_S3,
            function (FromCollection $export) {
                return $export->collection()->count() === 10;
            }
        );

        Excel::assertQueuedWithChain([
            new NotifyUserOfCompletedExport($users->first(), UsersExport::FILE_NAME)
        ]);
    }

    /**
     * @return void
     */
    public function test_queueExport_fail_queue()
    {
        Excel::fake();

        // Mock excel facade and make it throw an exception
        Excel::shouldReceive('queue')
            ->andThrow(new \Exception());

        $response = $this->get(route('users.excel.export.queue'));

        $response
            ->assertSessionHasErrors()
            ->assertRedirect(route('users.excel.export.download-form'));
    }

    /**
     * @return void
     */
    public function test_import_fail_import()
    {
        Excel::fake();

        // Mock excel facade and make it throw an exception
        Excel::shouldReceive('import')
            ->andThrow(new Exception());

        $response = $this->post(route('users.excel.import.upload'), [
            'users' => new UploadedFile(
                './tests/Feature/data/import_success.xlsx',
                'import_success.xlsx',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                null,
                true
            )
        ]);

        $response
            ->assertSessionHasErrors()
            ->assertRedirect(route('users.excel.import.import-form'));
    }

    /**
     * [Note] Do not use Excel::fake() to persist test data on DB
     *
     * @return void
     */
    public function test_import_success()
    {
        $response = $this->post(route('users.excel.import.upload'), [
            'users' => new UploadedFile(
                './tests/Feature/data/import_success.xlsx',
                'import_success.xlsx',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                null,
                true
            )
        ]);

        $response
            ->assertOk()
            ->assertViewHas('message');

        $this
            ->assertDatabaseHas('users', [
                'name' => 'Dr. Keaton Beahan DVM',
                'email' => 'myrtice.langosh@example.com',
            ])
            ->assertDatabaseHas('users', [
                'name' => 'Amelia Auer DDS',
                'email' => 'maximillian76@example.com',
            ])
            ->assertDatabaseHas('users', [
                'name' => 'Mariane Satterfield',
                'email' => 'cassin.brendon@example.net',
            ]);
    }

    /**
     * @return void
     */
    public function test_showUploadForm()
    {
        $response = $this->get(route('users.excel.import.import-form'));

        $response
            ->assertOk()
            ->assertViewIs('excel.import');
    }

    /**
     * @return void
     */
    public function test_queueImport_success()
    {
        Excel::fake();
        Storage::fake(UserController::STORAGE_S3);

        $response = $this->post(route('users.excel.import.queue'), [
            'users' => $file = UploadedFile::fake()->create(
                'queue_import_success.xlsx',
                100,
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            )
        ]);

        $response
            ->assertOk()
            ->assertViewHas('message');

        Excel::assertQueued($file->getBasename());
    }
}

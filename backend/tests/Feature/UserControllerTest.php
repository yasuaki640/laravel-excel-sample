<?php

namespace Tests\Feature;

use App\Exports\UsersExport;
use App\Http\Controllers\UserController;
use App\Jobs\NotifyUserOfCompletedExport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $response = $this->get(route('users.excel.download-form'));

        $response->assertOk();
        $response->assertViewIs('excel.index');
    }

    /**
     * @return void
     */
    public function test_download()
    {
        Excel::fake();

        User::factory()->count(10)->create();

        $response = $this->get(route('users.excel.download'));

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

        $response = $this->get(route('users.excel.download'));

        $response->assertSessionHasErrors();
        $response->assertRedirect(route('users.excel.download-form'));
    }

    /**
     * @return void
     */
    public function test_queue_success()
    {
        Excel::fake();
        Storage::fake(UserController::STORAGE_S3);

        $users = User::factory()
            ->count(10)
            ->create();

        $response = $this->actingAs($users->first())
            ->get(route('users.excel.queue'));

        $response->assertOk();
        $response->assertViewHas('message');

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
    public function test_queue_fail_queue()
    {
        Excel::fake();

        // Mock excel facade and make it throw an exception
        Excel::shouldReceive('queue')
            ->andThrow(new \Exception());

        $response = $this->get(route('users.excel.queue'));

        $response->assertSessionHasErrors();
        $response->assertRedirect(route('users.excel.download-form'));
    }
}

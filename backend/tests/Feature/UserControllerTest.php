<?php

namespace Tests\Feature;

use App\Exports\UsersExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
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

        $response = $this->get(route('users.excel.download'));

        $response->assertOk();
        Excel::assertDownloaded(UsersExport::FILE_NAME);
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
    }
}

<?php

namespace Tests\Feature;

use App\Exports\UsersExport;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
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
        $response = $this->get(route('users.excel.download'));

        $response->assertOk();
        $response->assertDownload(UsersExport::FILE_NAME);
    }
}

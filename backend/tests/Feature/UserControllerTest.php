<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * @return void
     */
    public function test_showDownloadForm()
    {
        $response = $this->get(route('excel.download-form'));

        $response->assertOk();
        $response->assertViewIs('excel.index');
    }

    /**
     * @return void
     */
    public function test_download()
    {
        $response = $this->get(route('excel.download'));

        $response->assertOk();
        $response->assertDownload('UserController.php');
    }
}

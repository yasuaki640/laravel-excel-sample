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
    }
}

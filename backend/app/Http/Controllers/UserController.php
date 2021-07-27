<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Show download excel files form
     *
     * @return View
     */
    public function showDownloadForm(): View
    {
        return view('excel.index');
    }
}

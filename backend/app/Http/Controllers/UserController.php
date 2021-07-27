<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class UserController extends Controller
{
    public function showDownloadForm(): View
    {
        return view('excel.index');
    }
}

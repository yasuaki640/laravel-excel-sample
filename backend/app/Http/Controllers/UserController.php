<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

    /**
     * @return BinaryFileResponse
     */
    public function download(): BinaryFileResponse
    {
        return response()->download('./app/Http/Controllers/UserController.php');
    }
}

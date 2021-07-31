<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Facades\Excel;
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
        return Excel::download(new UsersExport, 'users.xlsx');
    }
}

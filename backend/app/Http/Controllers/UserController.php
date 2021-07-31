<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
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
     * Download a excel file of users export
     *
     * @return BinaryFileResponse|RedirectResponse
     * @throws Exception
     */
    public function download(): BinaryFileResponse|RedirectResponse
    {
        try {
            return Excel::download(new UsersExport, UsersExport::FILE_NAME);

        } catch (Exception $e) {
            logger()->error($e);
            return redirect(route('users.excel.download-form'))
                ->withErrors($e->getMessage());
        }
    }
    }
}

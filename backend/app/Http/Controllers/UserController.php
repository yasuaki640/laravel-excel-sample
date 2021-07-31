<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Jobs\NotifyUserOfCompletedExport;
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
    const STORAGE_S3 = 's3';

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

    public function queue(): View
    {
        Excel::queue(new UsersExport, UsersExport::FILE_NAME, self::STORAGE_S3)->chain([
            new NotifyUserOfCompletedExport(request()->user(), UsersExport::FILE_NAME)
        ]);

        $message = 'Successfully queued an export job';
        return \view('excel.index', compact('message'));
    }
}

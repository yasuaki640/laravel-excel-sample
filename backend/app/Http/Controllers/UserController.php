<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Http\Requests\QueueImportPost;
use App\Http\Requests\User\UploadPost;
use App\Imports\UsersImport;
use App\Jobs\NotifyUserOfCompletedExport;
use App\Jobs\NotifyUserOfCompletedImport;
use App\Models\User;
use App\Notifications\ImportCompleted;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

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
            return redirect(route('users.excel.export.download-form'))
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Add users export jobs on queue
     *
     * @return View|RedirectResponse
     * @throws Throwable
     */
    public function queueExport(): View|RedirectResponse
    {
        try {
            DB::beginTransaction();
            Excel::queue(new UsersExport, UsersExport::FILE_NAME, self::STORAGE_S3)->chain([
                new NotifyUserOfCompletedExport(
                    request()->user() ?? User::factory()->create(),
                    UsersExport::FILE_NAME
                )
            ]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            logger()->error($e);
            return redirect(route('users.excel.export.download-form'))
                ->withErrors($e->getMessage());
        }

        $message = 'Successfully queued an export job';
        return \view('excel.index', compact('message'));
    }

    public function showUploadForm(): View
    {
        return view('excel.import');
    }

    /**
     * @param UploadPost $request
     * @return View|RedirectResponse
     */
    public function upload(UploadPost $request): View|RedirectResponse
    {
        try {
            Excel::import(new UsersImport, $request->file('users'));
            $message = 'Successfully imported an excel file';
            return \view('excel.import', compact('message'));

        } catch (Exception $e) {
            logger()->error($e);
            return redirect(route('users.excel.import.import-form'))
                ->withErrors($e->getMessage());
        }
    }

    /**
     * @param QueueImportPost $request
     * @return View|RedirectResponse
     */
    public function queueImport(QueueImportPost $request): View|RedirectResponse
    {
        try {
            Excel::queueImport(
                new UsersImport,
                $request->file('users'),
                self::STORAGE_S3
            )->chain([
                new NotifyUserOfCompletedImport(
                    request()->user() ?? User::factory()->create(),
                    $request->file('users')->getClientOriginalName()
                )
            ]);

            $message = 'Successfully queued job of import an excel file';
            return \view('excel.import', compact('message'));

        } catch (Exception $e) {
            logger()->error($e);
            return redirect(route('users.excel.import.import-form'))
                ->withErrors($e->getMessage());
        }
    }
}

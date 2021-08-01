<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::prefix('users')->name('users.')->group(function () {
    Route::prefix('excel')->name('excel.')->group(function () {
        Route::prefix('export')->name('export.')->group(function () {
            Route::get('', [\App\Http\Controllers\UserController::class, 'showDownloadForm'])->name('download-form');
            Route::get('download', [\App\Http\Controllers\UserController::class, 'download'])->name('download');
            Route::get('queue', [\App\Http\Controllers\UserController::class, 'queue'])->name('queue');
        });
        Route::prefix('import')->name('import.')->group(function () {
            Route::get('', [\App\Http\Controllers\UserController::class, 'upload'])->name('upload');
        });
    });
});

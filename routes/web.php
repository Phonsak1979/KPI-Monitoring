<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\SyncController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Resource routes (ทุก role เข้าถึง index/show ได้, admin middleware ตรวจสอบใน Controller)
Route::middleware('auth')->group(function () {
    Route::resource('departments', DepartmentController::class);
    Route::resource('districts', DistrictController::class);
    Route::resource('hospitals', HospitalController::class);
    Route::resource('rankings', RankingController::class);
});

// Sync routes (admin only)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/sync', [SyncController::class, 'index'])->name('sync.index');
    Route::get('/sync/list', [SyncController::class, 'getSyncList'])->name('sync.list');
    Route::post('/sync/all', [SyncController::class, 'syncAll'])->name('sync.all');
    Route::post('/sync/{ranking}', [SyncController::class, 'sync'])->name('sync.update');
});


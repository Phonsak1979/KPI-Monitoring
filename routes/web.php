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

Route::resource('departments', DepartmentController::class)->middleware('auth');

Route::resource('districts', DistrictController::class)->middleware('auth');

Route::resource('hospitals', HospitalController::class)->middleware('auth');

Route::resource('rankings', RankingController::class)->middleware('auth');

Route::get('/sync', [SyncController::class, 'index'])->name('sync.index')->middleware('auth');
Route::get('/sync/list', [SyncController::class, 'getSyncList'])->name('sync.list')->middleware('auth');
Route::post('/sync/all', [SyncController::class, 'syncAll'])->name('sync.all')->middleware('auth');
Route::post('/sync/{ranking}', [SyncController::class, 'sync'])->name('sync.update')->middleware('auth');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;

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

Route::get('/serviceareatop', function () {
    return view('ServiceArea.page-serviceareamaptop');
})->name('ServiceArea.page-serviceareamap');

Route::get('/serviceareatop/serviceareadetail', function () {
    return view('ServiceArea.page-serviceareamap');
});

//アップロード
Route::get('/upload', [UploadController::class, 'index'])->name('upload.index');
Route::post('/mesh-csv-upload', [UploadController::class, 'meshcsv'])->name('upload.meshcsv');
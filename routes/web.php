<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrudController;
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
Route::get('/project-generator', [CrudController::class, 'showForm']);
Route::post('/project-generator', [CrudController::class, 'generateProject']);
Route::get('/generated-files/{moduleName}', [CrudController::class, 'listGeneratedFiles'])->name('generated-files');
       
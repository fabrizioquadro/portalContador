<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\XmlController;
use App\Http\Controllers\ExportarController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\BackupController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/adicionaXml', [XmlController::class, 'adicionaXml']);
Route::post('/adicionaInventario', [InventarioController::class, 'adicionaInventario']);
Route::post('/adicionaBackup', [BackupController::class, 'adicionaBackup']);
Route::post('/downloadsXml', [ExportarController::class, 'downloadsXml'])->name('downloadsXml');
Route::post('/gerarPdf', [ExportarController::class, 'exportarPdf'])->name('gerarPdf');

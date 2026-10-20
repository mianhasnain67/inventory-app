<?php

use App\Http\Controllers\ITAssetController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ITAssetController::class, 'dashboard'])->name('it.dashboard');
Route::post('/assets', [ITAssetController::class, 'storeAsset'])->name('assets.store');
Route::post('/assets/{id}/assign', [ITAssetController::class, 'assignAsset'])->name('assets.assign');
Route::post('/assets/{id}/return', [ITAssetController::class, 'returnAsset'])->name('assets.return');
Route::delete('/assets/{id}', [ITAssetController::class, 'destroyAsset'])->name('assets.destroy');

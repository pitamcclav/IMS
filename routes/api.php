<?php

use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/upload', [InventoryController::class, 'upload']);
    Route::post('/colour', 'App\Http\Controllers\InventoryController@storeColor');
    Route::post('/size', 'App\Http\Controllers\InventoryController@storeSize');
    Route::post('/item', 'App\Http\Controllers\ItemController@store');
    Route::post('/supplier', 'App\Http\Controllers\SupplierController@store');
});
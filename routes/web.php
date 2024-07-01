<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\OrderLimitController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

// Manager Routes
Route::get('/', [ManagerController::class, 'dashboard'])->name('manager.dashboard');

Route::resource('inventory', InventoryController::class);
Route::resource('requests', RequestController::class);
Route::resource('category', CategoryController::class);
Route::resource('item', ItemController::class);
Route::resource('supplier', SupplierController::class);
Route::resource('orderLimit', OrderLimitController::class);
Route::resource('report', ReportController::class);

// Additional route for updating request status
Route::patch('/requests/{request}/update-status', [RequestController::class, 'updateStatus'])->name('requests.updateStatus');
Route::post('/api/colour', [InventoryController::class, 'storeColor']);
Route::post('/api/size', [InventoryController::class, 'storeSize']);

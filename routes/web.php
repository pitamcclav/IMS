<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\OrderLimitController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::middleware(['auth:staff'])->group(function () {
    Route::get('/manager/dashboard', [UsersController::class, 'manager'])->name('manager.dashboard');
    Route::get('/staff/dashboard', [UsersController::class, 'staff'])->name('staff.dashboard');
    Route::get('/admin/dashboard', [UsersController::class, 'admin'])->name('admin.dashboard');

    Route::resource('inventory', InventoryController::class);
    Route::resource('requests', RequestController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('item', ItemController::class);
    Route::resource('supplier', SupplierController::class);
    Route::resource('orderLimit', OrderLimitController::class);
    Route::resource('report', ReportController::class);

    Route::patch('/requests/{request}/update-status', [RequestController::class, 'updateStatus'])->name('requests.updateStatus');
    Route::post('/api/colour', [InventoryController::class, 'storeColor']);
    Route::post('/api/size', [InventoryController::class, 'storeSize']);

    Route::get('/fetch-colours/{itemId}', [InventoryController::class, 'fetchColours']);
    Route::get('/fetch-sizes/{itemId}/{colourId}', [InventoryController::class, 'fetchSizes']);
});
//// Manager Routes
//Route::get('/', [UsersController::class, 'dashboard'])->name('manager.dashboard');
//
//Route::resource('inventory', InventoryController::class);
//Route::resource('requests', RequestController::class);
//Route::resource('category', CategoryController::class);
//Route::resource('item', ItemController::class);
//Route::resource('supplier', SupplierController::class);
//Route::resource('orderLimit', OrderLimitController::class);
//Route::resource('report', ReportController::class);
//
//// Additional route for updating request status
//Route::patch('/requests/{request}/update-status', [RequestController::class, 'updateStatus'])->name('requests.updateStatus');
//Route::post('/api/colour', [InventoryController::class, 'storeColor']);
//Route::post('/api/size', [InventoryController::class, 'storeSize']);
//
//Route::get('/fetch-colours/{itemId}', [InventoryController::class, 'fetchColours']);
//Route::get('/fetch-sizes/{itemId}/{colourId}', [InventoryController::class, 'fetchSizes']);



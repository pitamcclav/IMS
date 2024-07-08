<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\OrderLimitController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\SupplierController;
use App\Http\Middleware\AdminManagerMiddleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ManagerMiddleware;
use App\Http\Middleware\StaffMiddleware;
use Illuminate\Support\Facades\Route;

//Auth routes
Route::get('/', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

//Users routes
Route::middleware(['auth:staff'])->group(function () {

    //Common routes
    Route::resource('requests', RequestController::class);
    //Fetch colours and sizes
    Route::get('/fetch-colours/{itemId}', [InventoryController::class, 'fetchColours']);
    Route::get('/fetch-sizes/{itemId}/{colourId}', [InventoryController::class, 'fetchSizes']);

    //Admin specific routes
    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::resource('users', AdminController::class);
        Route::get('/admin/dashboard', [UsersController::class, 'admin'])->name('admin.dashboard');
        Route::get('/stores', [AdminController::class, 'stores'])->name('stores');
        Route::post('/stores/add', [AdminController::class, 'addStore'])->name('stores.add');
        Route::get('/stores/edit/{id}', [AdminController::class, 'editStore'])->name('stores.edit');
        Route::put('/stores/update/{id}', [AdminController::class, 'updateStore'])->name('stores.update');
        Route::delete('/stores/{id}', [AdminController::class, 'deleteStore'])->name('stores.delete');

    });

    //Manager specific routes
    Route::middleware([ManagerMiddleware::class])->group(function () {
        Route::get('/manager/dashboard', [UsersController::class, 'manager'])->name('manager.dashboard');
        Route::resource('category', CategoryController::class);
        //Update request status
        Route::patch('/requests/{request}/update-status', [RequestController::class, 'updateStatus'])->name('requests.updateStatus');
    });

    //Staff specific routes
    Route::middleware([StaffMiddleware::class])->group(function () {
        Route::get('/staff/dashboard', [UsersController::class, 'staff'])->name('staff.dashboard');
    });


    //Admin and Manager specific routes
    Route::middleware([AdminManagerMiddleware::class])->group(function () {
        Route::resource('inventory', InventoryController::class);
        Route::resource('item', ItemController::class);
        Route::resource('supplier', SupplierController::class);
        Route::resource('orderLimit', OrderLimitController::class);
        Route::resource('report', ReportController::class);

        //Store colours and sizes
        Route::post('/api/colour', [InventoryController::class, 'storeColor']);
        Route::post('/api/size', [InventoryController::class, 'storeSize']);


    });




});




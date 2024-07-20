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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
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
    Route::get('/fetch-colours/{itemId}', [RequestController::class, 'fetchColours']);
    Route::get('/fetch-sizes/{itemId}/{colourId}', [RequestController::class, 'fetchSizes']);
    Route::get('/fetch-items/{storeId}', [RequestController::class, 'fetchItems']);


    //Admin specific routes
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', AdminController::class);
        Route::get('/admin/dashboard', [UsersController::class, 'admin'])->name('admin.dashboard');
        Route::get('/stores', [AdminController::class, 'stores'])->name('stores');
        Route::post('/stores/add', [AdminController::class, 'addStore'])->name('stores.add');
        Route::get('/stores/edit/{id}', [AdminController::class, 'editStore'])->name('stores.edit');
        Route::put('/stores/update/{id}', [AdminController::class, 'updateStore'])->name('stores.update');
        Route::delete('/stores/{id}', [AdminController::class, 'deleteStore'])->name('stores.delete');
        Route::post('/assign-roles', [AdminController::class, 'assignRoles'])->name('roles.assign');
        Route::post('/revoke-roles/{staffId}', [AdminController::class, 'revokeRoles'])->name('roles.revoke');

    });

    //Manager specific routes
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/manager/dashboard', [UsersController::class, 'manager'])->name('manager.dashboard');

        //Update request status
        Route::patch('/requests/{request}/update-status', [RequestController::class, 'updateStatus'])->name('requests.updateStatus');
    });

    //Staff specific routes
    Route::middleware(['role:staff'])->group(function () {
        Route::get('/staff/dashboard', [UsersController::class, 'staff'])->name('staff.dashboard');
    });


    //Admin and Manager specific routes
    Route::middleware(['role:manager|admin'])->group(function () {
        Route::resource('inventory', InventoryController::class);
        Route::resource('item', ItemController::class);
        Route::resource('supplier', SupplierController::class);
        Route::resource('orderLimit', OrderLimitController::class);
        Route::resource('report', ReportController::class);
        Route::resource('category', CategoryController::class);

        //Store colours and sizes
        Route::post('/api/colour', [InventoryController::class, 'storeColor']);
        Route::post('/api/size', [InventoryController::class, 'storeSize']);

        Route::get('/storage/{filename}', function ($filename) {
            $path = storage_path('app/public/' . $filename);

            if (!File::exists($path)) {
                abort(404);
            }

            $file = File::get($path);
            $type = File::mimeType($path);

            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);

            return $response;
        });



    });




});




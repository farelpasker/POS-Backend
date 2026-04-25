<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\MerchantProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("login", [AuthController::class, 'login']);
Route::post("register", [AuthController::class, 'register']);
Route::post("token-login", [AuthController::class, 'tokenLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post("logout", [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
});

Route::middleware(['auth:sanctum', 'role:manager'])->group(function () {
    Route::apiResource('users',UserController::class);
    Route::apiResource('roles',RoleController::class);

    Route::get('users/{user_id}/roles', [UserRoleController::class, 'listUserRoles']);
    Route::post('users/roles', [UserRoleController::class, 'assignRole']);
    Route::delete('users/roles/remove', [UserRoleController::class, 'removeRole']);

    Route::apiResource('categories',CategoryController::class);
    Route::apiResource('products',ProductController::class);

    Route::apiResource('warehouses',WarehouseController::class);
    Route::apiResource('merchants',MerchantController::class);

    Route::prefix('warehouses/{warehouse_id}')->group(function () {
        Route::post('/products/attach', [WarehouseProductController::class, 'attach']);
        Route::put('/products/{product_id}', [WarehouseProductController::class, 'update']);
        Route::delete('/products/{product_id}', [WarehouseProductController::class, 'detach']);
    });

    Route::prefix('merchants/{merchant_id}')->group(function () {
        Route::post('/products', [MerchantProductController::class,'store']);
        Route::put('/products/{product_id}', [MerchantProductController::class,'update']);
        Route::delete('/products/{product_id}', [MerchantProductController::class,'destroy']);
    });    
});

Route::middleware(['auth:sanctum', 'role:manager|keeper'])->group(function () {
    Route::get('categories',[CategoryController::class,'index']);
    Route::get('categories/{categoryId}',[CategoryController::class,'show']);

    Route::get('products',[ProductController::class,'index']);
    Route::get('products/{productId}', [ProductController::class, 'show']);

    Route::get('warehouses',[WarehouseController::class,'index']);
    Route::get('warehouses/{warehouseId}', [WarehouseController::class, 'show']);
    
    Route::post('transactions',[TransactionController::class,'store']);
    Route::get('transactions',[TransactionController::class,'index']);
    Route::get('transactions/{transactionId}',[TransactionController::class,'show']);
    Route::get('my-merchant',[MerchantController::class,'getByKeeperId']);
    Route::get('/my-merchant/transaction',[TransactionController::class,'getTransactionByMerchant']);   
});
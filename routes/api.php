<?php

use App\Http\Controllers\AuthAdminController;
use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookCategoryController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\GoodsReceivedNoteController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\GetDataController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
|----------------------------------------------------------------
|Public Route API
|----------------------------------------------------------------
*/
//Admin route
Route::post('/admin/login', [AuthAdminController::class, 'login']); 
//User route
Route::post('user/register', [AuthUserController::class, 'register']);
Route::post('user/login', [AuthUserController::class, 'login']);
Route::post('user/forgot-password', [UserController::class, 'forgotPassword']);
Route::put('user/recover-password/{token}', [UserController::class, 'recoverPassword']);

//Book route get data
Route::get('/books-list', [GetDataController::class, 'index']);

/*
|----------------------------------------------------------------
|Admin Protected Route
|----------------------------------------------------------------
*/
Route::group(['middleware' => ['auth:admins']], function () {
    Route::post('admin/logout', [AuthAdminController::class, 'logout']);
    Route::get('admin/users-list', [AdminController::class, 'usersList']);
    Route::put('admin/update-status-user/{id}', [AdminController::class, 'updateStatus']);
    // Manage Author
    Route::resource('authors', AuthorController::class);
    Route::get('/authors/search/{name}', [AuthorController::class, 'search']);
    // Manage Category
    Route::resource('categories', CategoryController::class);
    Route::get('/categories/search/{name}', [CategoryController::class, 'search']);
    // Manage Publisher
    Route::resource('publishers', PublisherController::class);
    Route::get('/publishers/search/{name}', [PublisherController::class, 'search']);
     // Manage Supplier
    Route::resource('suppliers', SupplierController::class);
    Route::get('/suppliers/search/{name}', [SupplierController::class, 'search']);
     // Manage Book
     Route::get('/books', [BookController::class, 'index']);
     Route::get('/books/{id}', [BookController::class, 'show']);
     Route::post('/books', [BookController::class, 'store']);
     Route::post('/books/{id}', [BookController::class, 'update']);
     Route::delete('/books/{id}', [BookController::class, 'destroy']);
    Route::get('/books/search/{name}', [BookController::class, 'search']);
     // Manage GRN not update
    Route::resource('goods-received-notes', GoodsReceivedNoteController::class);
      // Manage Discount
    Route::resource('discounts', DiscountController::class);
    Route::get('/discounts/search/{name}', [DiscountController::class, 'search']);
       // Manage Inventory
    Route::get('/inventories/search/{name}', [InventoryController::class, 'search']);

});
/*
|----------------------------------------------------------------
|User Protected Route
|----------------------------------------------------------------
*/
Route::group(['middleware' => ['auth:users']], function () {
    Route::post('user/logout', [AuthUserController::class, 'logout']);
    Route::get('user/profile', [UserController::class, 'profile']);
    Route::put('user/update-profile', [UserController::class, 'updateProfile']);
    Route::put('user/change-password', [UserController::class, 'changePassword']);
});






Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

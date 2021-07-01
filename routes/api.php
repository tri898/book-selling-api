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
|Public Route
|----------------------------------------------------------------
*/
//admin login route
Route::post('/admin/login', [AuthAdminController::class, 'login']); 
//user route
Route::post('user/register', [AuthUserController::class, 'register']);
Route::post('user/login', [AuthUserController::class, 'login']);
Route::post('user/forgot-password', [UserController::class, 'forgotPassword']);
Route::put('user/recover-password/{token}', [UserController::class, 'recoverPassword']);
// Author get data
Route::get('/authors', [AuthorController::class, 'index']);
Route::get('/authors/{id}', [AuthorController::class, 'show']);
Route::get('/authors/search/{name}', [AuthorController::class, 'search']);
// Category get data
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/categories/search/{name}', [CategoryController::class, 'search']);
// Book category get data
Route::get('/book-categories', [BookCategoryController::class, 'index']);
Route::get('/book-categories/{id}', [BookCategoryController::class, 'show']);
Route::get('/book-categories/search/{name}', [BookCategoryController::class, 'search']);
// Publisher get data
Route::get('/publishers', [PublisherController::class, 'index']);
Route::get('/publishers/{id}', [PublisherController::class, 'show']);
Route::get('/publishers/search/{name}', [PublisherController::class, 'search']);
// Supplier get data
Route::get('/suppliers', [SupplierController::class, 'index']);
Route::get('/suppliers/{id}', [SupplierController::class, 'show']);
Route::get('/suppliers/search/{name}', [SupplierController::class, 'search']);
// Book get data
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);
Route::get('/books/search/{name}', [BookController::class, 'search']);
// GRN get data
Route::get('/received-notes', [GoodsReceivedNoteController::class, 'index']);
Route::get('/received-notes/{id}', [GoodsReceivedNoteController::class, 'show']);
Route::get('/received-notes/search/{name}', [GoodsReceivedNoteController::class, 'search']);
// Discount get data
Route::get('/discounts', [DiscountController::class, 'index']);
Route::get('/discounts/{id}', [DiscountController::class, 'show']);
Route::get('/discounts/search/{name}', [DiscountController::class, 'search']);
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
    Route::post('/authors', [AuthorController::class, 'store']);
    Route::put('/authors/{id}', [AuthorController::class, 'update']);
    Route::delete('/authors/{id}', [AuthorController::class, 'destroy']);
    // Manage Category
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    // Manage Book Category
    Route::post('/book-categories', [BookCategoryController::class, 'store']);
    Route::put('/book-categories/{id}', [BookCategoryController::class, 'update']);
    Route::delete('/book-categories/{id}', [BookCategoryController::class, 'destroy']);
    // Manage Publisher
    Route::post('/publishers', [PublisherController::class, 'store']);
    Route::put('/publishers/{id}', [PublisherController::class, 'update']);
    Route::delete('/publishers/{id}', [PublisherController::class, 'destroy']);
     // Manage Supplier
     Route::post('/suppliers', [SupplierController::class, 'store']);
     Route::put('/suppliers/{id}', [SupplierController::class, 'update']);
     Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);
     // Manage Book
     Route::post('/books', [BookController::class, 'store']);
     Route::put('/books/{id}', [BookController::class, 'update']);
     Route::delete('/books/{id}', [BookController::class, 'destroy']);
     // Manage GRN
     Route::post('/received-notes', [GoodsReceivedNoteController::class, 'store']);
     Route::put('/received-notes/{id}', [GoodsReceivedNoteController::class, 'update']);
     Route::delete('/received-notes/{id}', [GoodsReceivedNoteController::class, 'destroy']);
      // Manage Discount
      Route::post('/discounts', [DiscountController::class, 'store']);
      Route::put('/discounts/{id}', [DiscountController::class, 'update']);
      Route::delete('/discounts/{id}', [DiscountController::class, 'destroy']);
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

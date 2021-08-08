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
use App\Http\Controllers\OrderController;
use App\Http\Controllers\GetDataController;
use App\Http\Controllers\GetBookController;

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
/*
Get data for main page
*/ 
//Get 9 new books
Route::get('/new-books', [GetBookController::class, 'getNewBook']);
//Get 9  selling books
Route::get('/selling-books', [GetBookController::class, 'getBestSellingBook']);
//Get book detail
Route::get('/book-details/{id}', [GetBookController::class, 'getBookDetails']);
//Admin route
Route::post('/admin/login', [AuthAdminController::class, 'login']); 

//User route
Route::post('user/register', [AuthUserController::class, 'register']);
Route::post('user/login', [AuthUserController::class, 'login']);
Route::post('user/forgot-password', [UserController::class, 'forgotPassword']);
Route::put('user/recover-password/{token}', [UserController::class, 'recoverPassword']);



/*
|----------------------------------------------------------------
|Admin Protected Route
|----------------------------------------------------------------
*/
Route::group(['middleware' => ['auth:admins']], function () {
    Route::post('admin/logout', [AuthAdminController::class, 'logout']);
    //Get data (cate, author,pub,supp)
    Route::get('/data-list', [GetDataController::class, 'index']);
    // Manage User
    Route::get('admin/users-list', [AdminController::class, 'usersList']);
    Route::put('admin/update-status-user/{id}', [AdminController::class, 'updateStatus']);
    // Manage Author
    Route::resource('authors', AuthorController::class);
    // Manage Category
    Route::resource('categories', CategoryController::class);
    // Manage Publisher
    Route::resource('publishers', PublisherController::class);
    // Manage Supplier
    Route::resource('suppliers', SupplierController::class);
    // Manage Book (include book cate, image, inventory)
    Route::get('books', [BookController::class, 'index']);
    Route::get('/books/{id}', [BookController::class, 'show']);
    Route::post('/books', [BookController::class, 'store']);
    Route::post('/books/{id}', [BookController::class, 'update']);
    Route::delete('/books/{id}', [BookController::class, 'destroy']);
    // Manage GRN not update
    Route::resource('goods-received-notes', GoodsReceivedNoteController::class);
    // Manage Discount
    Route::resource('discounts', DiscountController::class);
    //  Manage Order
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::put('orders/{id}', [OrderController::class, 'updateStatus']);
    
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
    //order book
    Route::post('user/orders', [OrderController::class, 'store']);
    Route::get('user/orders', [OrderController::class, 'getOrders']);
    Route::get('user/orders/{id}', [OrderController::class, 'showOrder']);
    // cancel order if status "Chờ xử lý"(delete permanent)
    Route::delete('user/orders/{id}', [OrderController::class, 'destroy']);
    
});






Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

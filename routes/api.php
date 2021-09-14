<?php

use App\Http\Controllers\Auth\{
    AdminController as AdminAuthController,
    UserController as UserAuthController
};
use App\Http\Controllers\Management\{
    UserController as UserManagementController,
    AuthorController,
    CategoryController,
    BookCategoryController,
    PublisherController,
    SupplierController,
    BookController,
    GoodsReceivedNoteController,
    DiscountController,
    ImageController,
    InventoryController,
    OrderController as AdminOrderController,
    SelectiveDataController
};
use App\Http\Controllers\User\{
    ProfileController,
    PasswordController,
    OrderController as UserOrderController
};
use App\Http\Controllers\Data\BookController as BookDataController;
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
  Get data book for main page
  no authentication required
*/ 
// Get 9 new books
Route::get('books/new', [BookDataController::class, 'getNewBook']);
// Get 9  selling books
Route::get('books/selling', [BookDataController::class, 'getSellingBook']);
// Get books of category
Route::get('books/category/{id}', [BookDataController::class, 'getBookOfCategory']);
// Get books of author
Route::get('books/author/{id}', [BookDataController::class, 'getBookOfAuthor']);
// Get books details
Route::get('books/details/{id}', [BookDataController::class, 'getBookDetails']);

// Admin route
Route::post('/admin/login', [AdminAuthController::class, 'login']); 

// User route
Route::post('user/register', [UserAuthController::class, 'register']);
Route::post('user/login', [UserAuthController::class, 'login']);
Route::post('user/forgot-password', [PasswordController::class, 'forgotPassword'])->name('password.forgot');
Route::put('user/recover-password/{token}', [PasswordController::class, 'recoverPassword'])->name('password.recover');



/*
|----------------------------------------------------------------
|Admin Protected Route
|----------------------------------------------------------------
*/
Route::group(['middleware' => ['auth:admins']], function () {
    Route::post('admin/logout', [AdminAuthController::class, 'logout']);    //logout admin route 
    // Manage User(get user list, manage status user)
    Route::get('admin/users-list', [UserManagementController::class, 'getUserList']);
    Route::put('admin/update-status-user/{id}', [UserManagementController::class, 'updateUserStatus']);
    // Get selective data(cate, author, pub, supp)
    Route::get('/data-list', [SelectiveDataController::class, 'index']);
    // Manage Author
    Route::resource('authors', AuthorController::class);
    // Manage Category
    Route::resource('categories', CategoryController::class);
    // Manage Publisher
    Route::resource('publishers', PublisherController::class);
    // Manage Supplier
    Route::resource('suppliers', SupplierController::class);
    // Manage Book (include book cate, image, inventory)
    Route::get('books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::post('/books/{id}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{id}', [BookController::class, 'destroy'])->name('books.destroy');
    // Manage GRN no update
    Route::resource('goods-received-notes', GoodsReceivedNoteController::class);
    // Manage Discount
    Route::resource('discounts', DiscountController::class);
    // Manage Order (order list, order details, update status order)
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{id}', [AdminOrderController::class, 'updateOrderStatus'])->name('orders.update');
    
});
/*
|----------------------------------------------------------------
|User Protected Route
|----------------------------------------------------------------
*/
Route::group(['middleware' => ['auth:users']], function () {
    // Personal user route(logout, get and update info, change password)
    Route::post('user/logout', [UserAuthController::class, 'logout']);
    Route::get('user/profile', [ProfileController::class, 'getPersonalData'])->name('profile.index');
    Route::put('user/update-profile', [ProfileController::class, 'updatePersonalData'])->name('profile.update');
    Route::put('user/change-password', [PasswordController::class, 'changePassword'])->name('password.change');
    // Order book route(create order, get all order, get details, cancel order)
    Route::post('user/orders', [UserOrderController::class, 'store'])->name('user-orders.store');
    Route::get('user/orders', [UserOrderController::class, 'index'])->name('user-orders.index');
    Route::get('user/orders/{id}', [UserOrderController::class, 'show'])->name('user-orders.show');
    Route::delete('user/orders/{id}', [UserOrderController::class, 'destroy'])->name('user-orders.destroy'); // cancel order if status "Chờ xử lý"(delete permanent)
    
});






Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

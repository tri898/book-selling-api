<?php

use App\Http\Controllers\Auth\{
    AdminController as AdminAuthController,
    UserController as UserAuthController
};
use App\Http\Controllers\Management\{
    UserController as UserManagementController,
    AuthorController,
    CategoryController,
    PublisherController,
    SupplierController,
    BookController,
    GoodsReceivedNoteController,
    DiscountController,
    OrderController as AdminOrderController,
    DashboardController,
    SelectiveDataController
};
use App\Http\Controllers\User\{
    ProfileController,
    PasswordController,
    OrderController as UserOrderController,
    ReviewController
};
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Data\{
    BookController as BookDataController,
    ReviewController as BookReviewController,
};
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
// Get new books
Route::get('books/new', [BookDataController::class, 'getNewBook']);
// Get selling books
Route::get('books/selling', [BookDataController::class, 'getSellingBook']);
// Get books of category
Route::get('books/category/{id}', [BookDataController::class, 'getBookOfCategory']);
// Get books of author
Route::get('books/author/{id}', [BookDataController::class, 'getBookOfAuthor']);
// Get books details
Route::get('books/details/{id}', [BookDataController::class, 'getBookDetails']);
// Get review book
Route::get('books/reviews/{id}', [BookReviewController::class, 'getBookReview']);
// Get rating book
Route::get('books/ratings/{id}', [BookReviewController::class, 'getBookRating']);

// Admin route
Route::post('/admin/login', [AdminAuthController::class, 'login']); 

// User route
Route::post('user/register', [UserAuthController::class, 'register']);
Route::post('user/login', [UserAuthController::class, 'login']);
Route::post('user/password/forgot', [PasswordController::class, 'forgotPassword'])->name('password.forgot');
Route::put('user/password/recover/{token}', [PasswordController::class, 'recoverPassword'])->name('password.recover');



/*
|----------------------------------------------------------------
|Admin Protected Route
|----------------------------------------------------------------
*/
Route::group(['middleware' => ['auth:admins']], function () {
    Route::post('admin/logout', [AdminAuthController::class, 'logout']);    //logout admin route 
    // Manage User(get user list, manage status user)
    Route::get('admin/users/list', [UserManagementController::class, 'getUserList']);
    Route::put('admin/users/status/{id}', [UserManagementController::class, 'updateUserStatus']);
    // Get selective data(cate, author, pub, supp)
    Route::get('data/select', [SelectiveDataController::class, 'index']);
    // Manage Author
    Route::resource('authors', AuthorController::class);
    // Manage Category
    Route::resource('categories', CategoryController::class);
    // Manage Publisher
    Route::resource('publishers', PublisherController::class);
    // Manage Supplier
    Route::resource('suppliers', SupplierController::class);
    // Manage Book (include book cate, image, inventory)
    Route::resource('books', BookController::class);
    // Manage GRN no update
    Route::resource('goods-received-notes', GoodsReceivedNoteController::class)->except('update');
    // Manage Discount
    Route::resource('discounts', DiscountController::class);
    // Manage Order (all order list, order details, update status order)
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{id}', [AdminOrderController::class, 'updateOrderStatus'])->name('orders.update');
    // Dashboard
    Route::get('dashboards/books/selling', [DashboardController::class, 'getSellingBook']);
    Route::get('dashboards/orders/total', [DashboardController::class, 'getTotalOrdersInMonth']);
    Route::get('dashboards/users/total', [DashboardController::class, 'getTotalUsersInMonth']);

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
    Route::put('user/profile/update', [ProfileController::class, 'updatePersonalData'])->name('profile.update');
    Route::put('user/password/change', [PasswordController::class, 'changePassword'])->name('password.change');
    // Order book route(create order, get all order, get details, cancel order)
    Route::post('user/orders', [UserOrderController::class, 'store'])->name('user-orders.store');
    Route::get('user/orders', [UserOrderController::class, 'index'])->name('user-orders.index');
    Route::get('user/orders/{id}', [UserOrderController::class, 'show'])->name('user-orders.show');
    Route::delete('user/orders/{id}', [UserOrderController::class, 'destroy'])->name('user-orders.destroy'); // cancel order if status "Chờ xử lý"(delete permanent)
    // Review route(create,edit,show)
    Route::post('user/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('user/reviews/{id}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::get('user/reviews/{id}', [ReviewController::class, 'show'])->name('reviews.show');
});

    Route::post('image/upload', [ImageController::class, 'store'])->name('images.store');
    



// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

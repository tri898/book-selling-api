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

// Retrieve data for website routes
Route::prefix('books')->group(function () {
    // Get new books
    Route::get('new', [BookDataController::class, 'getNewBook']);
    // Get selling books
    Route::get('selling', [BookDataController::class, 'getSellingBook']);
    // Get books of category
    Route::get('category/{id}', [BookDataController::class, 'getBookOfCategory']);
    // Get books of author
    Route::get('author/{id}', [BookDataController::class, 'getBookOfAuthor']);
    // Get book details
    Route::get('details/{id}', [BookDataController::class, 'getBookDetails']);
    // Get book review
    Route::get('reviews/{id}', [BookReviewController::class, 'getBookReview']);
    // Get book rating
    Route::get('ratings/{id}', [BookReviewController::class, 'getBookRating']);
});


// Admin route
Route::post('/admin/login', [AdminAuthController::class, 'login']); 

// User route
Route::prefix('user')->group(function () {
    Route::post('register', [UserAuthController::class, 'register']);
    Route::post('login', [UserAuthController::class, 'login']);
    Route::post('password/forgot', [PasswordController::class, 'forgotPassword'])->name('password.forgot');
    Route::put('password/recover/{token}', [PasswordController::class, 'recoverPassword'])->name('password.recover');

});



/*
|----------------------------------------------------------------
|Admin Protected Route
|----------------------------------------------------------------
*/
Route::group(['middleware' => ['auth:admins']], function () {

    Route::prefix('admin')->group(function () {
        // logout admin route
        Route::post('logout', [AdminAuthController::class, 'logout']);     
        // Manage User(get user list, manage status user)
        Route::get('users/list', [UserManagementController::class, 'getUserList']);
        Route::put('users/status/{id}', [UserManagementController::class, 'updateUserStatus']);
    });
   
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
    Route::resource('goods-received-notes', GoodsReceivedNoteController::class);
    Route::get('goods-received-notes/status/{id}', [GoodsReceivedNoteController::class, 'statusShow']);
    // Manage Discount
    Route::resource('discounts', DiscountController::class);
    // Manage Order (all order list, order details, update status order)
    Route::prefix('orders')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('{id}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('status/{id}', [AdminOrderController::class, 'statusShow'])->name('orders.status-show');
        Route::put('{id}', [AdminOrderController::class, 'updateOrderStatus'])->name('orders.update');
    });   
    // Dashboard
    Route::prefix('dashboards')->group(function () {
        Route::get('books/selling', [DashboardController::class, 'getSellingBook']);
        Route::get('orders/total', [DashboardController::class, 'getTotalOrdersInMonth']);
        Route::get('users/total', [DashboardController::class, 'getTotalUsersInMonth']);
    });
});
/*
|----------------------------------------------------------------
|User Protected Route
|----------------------------------------------------------------
*/
Route::group(['middleware' => ['auth:users'], 'prefix' => 'user' ], function () {
    // Personal user route(logout, get and update info, change password)
    Route::post('logout', [UserAuthController::class, 'logout']);
    Route::get('profile', [ProfileController::class, 'getPersonalData'])->name('profile.index');
    Route::put('profile/update', [ProfileController::class, 'updatePersonalData'])->name('profile.update');
    Route::put('password/change', [PasswordController::class, 'changePassword'])->name('password.change');
    // Order book route(create order, get all order, get details, cancel order)
    Route::post('orders', [UserOrderController::class, 'store'])->name('user-orders.store');
    Route::get('orders', [UserOrderController::class, 'index'])->name('user-orders.index');
    Route::get('orders/{id}', [UserOrderController::class, 'show'])->name('user-orders.show');
    Route::get('orders/status/{id}', [UserOrderController::class, 'statusShow'])->name('user-orders.status-show');
    Route::delete('orders/{id}', [UserOrderController::class, 'destroy'])->name('user-orders.destroy'); 
    // Review route(create,edit,show)
    Route::post('reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('reviews/{id}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::get('reviews/{id}', [ReviewController::class, 'show'])->name('reviews.show');
});

    Route::post('image/upload', [ImageController::class, 'store'])->name('images.store');
    



// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

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
    SliderController,
    OrderController as AdminOrderController,
    DashboardController,
    SelectiveDataController
};
use App\Http\Controllers\Shipper\{
    OrderController as ShipperOrderController
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
    SliderController as BookSliderController,
    SelectiveDataController as BookSelectiveDataController
};
use App\Http\Middleware\CheckRole;
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

// Retrieve book data for website routes
Route::prefix('books')->group(function () {
    // Get all books
    Route::get('all', [BookDataController::class, 'getAllBook']);
    // Get new books
    Route::get('new', [BookDataController::class, 'getNewBook']);
    // Get selling books
    Route::get('selling', [BookDataController::class, 'getSellingBook']);
    // Get random books
    Route::get('random', [BookDataController::class, 'getRandomBook']);
    // Books search
    Route::get('search', [BookDataController::class, 'bookSearch']);
    // The most discounted book
    Route::get('most-discount', [BookDataController::class, 'getTheMostDiscountedBook']);
    // The most highlight author
    Route::get('highlight-author', [BookDataController::class, 'getHighlightAuthor']);
    // Get books of category
    Route::get('category/{id}', [BookDataController::class, 'getBookOfCategory']);
    // Get books of author
    Route::get('author/{id}', [BookDataController::class, 'getBookOfAuthor']);
    // Get book details
    Route::get('{id}/details', [BookDataController::class, 'getBookDetails']);
    // Get book review
    Route::get('{id}/reviews', [BookReviewController::class, 'getBookReview']);
    // Get book rating
    Route::get('{id}/ratings', [BookReviewController::class, 'getBookRating']);
    Route::get('select-list', [BookSelectiveDataController::class, 'index']);
    Route::get('sliders', [BookSliderController::class, 'index']);
});


// Admin login route
Route::post('/manage/login', [AdminAuthController::class, 'login']);
// Shipper login route
Route::post('/shipper/login', [AdminAuthController::class, 'login']); 

// User route
Route::prefix('users')->group(function () {
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
Route::group(['middleware' => ['auth:admins',CheckRole::class]], function () {
    Route::prefix('manage')->group(function () {
        // logout admin route
        Route::post('logout', [AdminAuthController::class, 'logout']);     
        // Manage User(get user list, manage status user)
        Route::get('users', [UserManagementController::class, 'getUserList']);
        Route::put('users/{id}', [UserManagementController::class, 'updateUserStatus']);
        // Get selective data(cate, author, pub, supp)
        Route::get('data/select', [SelectiveDataController::class, 'index']);
        // Manage Author
        Route::apiResource('authors', AuthorController::class);
        // Manage Category
        Route::apiResource('categories', CategoryController::class);
        // Manage Publisher
        Route::apiResource('publishers', PublisherController::class);
        // Manage Supplier
        Route::apiResource('suppliers', SupplierController::class);
        // Manage Book (include book cate, image, inventory)
        Route::apiResource('books', BookController::class);
        // Manage GRN no update
        Route::apiResource('goods-received-notes', GoodsReceivedNoteController::class);
        // Manage Discount
        Route::apiResource('discounts', DiscountController::class);
        // Manage Slider
        Route::apiResource('sliders', SliderController::class);
        // Manage Order (all order list, order details, update status order)
        Route::prefix('orders')->group(function () {
            Route::get('/', [AdminOrderController::class, 'index'])->name('orders.index');
            Route::get('{id}', [AdminOrderController::class, 'show'])->name('orders.show');
            Route::put('{id}', [AdminOrderController::class, 'updateOrderStatus'])->name('orders.update');
        });   
        // Dashboard
        Route::prefix('dashboards')->group(function () {
            Route::get('books/selling', [DashboardController::class, 'getSellingBook']);
            Route::get('books/statistic', [DashboardController::class, 'getBookStatistics']);
            Route::get('orders/statistic', [DashboardController::class, 'getTotalIncomeInMonth']);
            Route::get('orders/total', [DashboardController::class, 'getTotalOrderInMonth']);
            Route::get('users/statistic', [DashboardController::class, 'getUserStatistics']);
            Route::get('grn/statistic', [DashboardController::class, 'getGRNStatistics']);   
        });
    });
});
/*
|----------------------------------------------------------------
|Shipper Protected Route
|----------------------------------------------------------------
*/
Route::group(['middleware' => ['auth:admins'], 'prefix' => 'shippers'], function () {
    Route::get('orders', [ShipperOrderController::class, 'index'])->name('shipper-orders.index');
    Route::put('orders/{id}', [ShipperOrderController::class, 'update'])->name('shipper-orders.update');
    Route::post('goods-received-notes', [GoodsReceivedNoteController::class, 'store']);
    Route::post('logout', [AdminAuthController::class, 'logout']);     

 });
/*
|----------------------------------------------------------------
|User Protected Route
|----------------------------------------------------------------
*/
Route::group(['middleware' => ['auth:users'], 'prefix' => 'users' ], function () {
    // Personal user route(logout, get and update info, change password)
    Route::post('logout', [UserAuthController::class, 'logout']);
    Route::get('profile', [ProfileController::class, 'getPersonalData'])->name('profile.index');
    Route::put('profile/update', [ProfileController::class, 'updatePersonalData'])->name('profile.update');
    Route::put('password/change', [PasswordController::class, 'changePassword'])->name('password.change');
    // Order book route(create order, get all order, get details, cancel order)
    Route::post('orders', [UserOrderController::class, 'store'])->name('user-orders.store');
    Route::get('orders', [UserOrderController::class, 'index'])->name('user-orders.index');
    Route::get('orders/{id}', [UserOrderController::class, 'show'])->name('user-orders.show');
    Route::delete('orders/{id}', [UserOrderController::class, 'destroy'])->name('user-orders.destroy'); 
    // Review route(create,edit,show)
    Route::post('reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('reviews/{id}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::get('reviews/{id}', [ReviewController::class, 'show'])->name('reviews.show');
});
// Upload image
    Route::post('image/upload', [ImageController::class, 'store'])->name('images.store');

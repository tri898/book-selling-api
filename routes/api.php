<?php
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthAdminController;
use App\Http\Controllers\ AuthUserController;
use App\Http\Controllers\ AdminController;
use App\Http\Controllers\ UserController;
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
// Public routes
        // Reg and login
Route::post('/register', [AuthUserController::class, 'register']);
Route::post('/login', [AuthUserController::class, 'login']);
Route::post('/admin/login', [AuthAdminController::class, 'login']);
        // Book
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/search/{name}', [ProductController::class, 'search']);

// Protected routes admin
Route::group(['middleware' => ['auth:admins']], function () {
    Route::post('admin/logout', [AuthAdminController::class, 'logout']);
    Route::get('admin/users-list', [AdminController::class, 'usersList']);
    Route::put('admin/update-status-user/{id}', [AdminController::class, 'updateStatus']);

    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});
// Protected routes user
Route::group(['middleware' => ['auth:users']], function () {
  
    Route::post('/logout', [AuthUserController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/update-profile', [UserController::class, 'updateProfile']);
});




Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

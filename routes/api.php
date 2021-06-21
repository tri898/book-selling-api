<?php
use App\Http\Controllers\AuthorController;
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
// Author table
Route::get('/authors', [AuthorController::class, 'index']);
Route::get('/authors/{id}', [AuthorController::class, 'show']);
Route::get('/authors/search/{name}', [AuthorController::class, 'search']);

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

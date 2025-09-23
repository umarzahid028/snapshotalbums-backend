<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\User\AuthController as UserAuthController;
use App\Http\Controllers\Api\V1\User\StripeSubscriptionController;
use App\Http\Controllers\Api\V1\User\BlogController as UserBlogController;
use App\Http\Controllers\Api\V1\User\FaqController as UserFaqController;
use App\Http\Controllers\Api\V1\User\AlbumController as UserAlbumController;
use App\Http\Controllers\Api\V1\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Api\V1\User\SubscriptionPlanController as UserSubscriptionPlanController;

use App\Http\Controllers\Api\V1\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Api\V1\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\V1\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Api\V1\Admin\SubscriptionPlanController as AdminSubscriptionPlanController;
use App\Http\Controllers\Api\V1\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\V1\Admin\DriveAccountController as AdminDriveAccountController;
use App\Http\Controllers\Api\V1\Admin\SettingsController as AdminSettingsController;


Route::prefix('v1')->group(function () {
    
    Route::get('/auth/google/redirect', [UserAuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [UserAuthController::class, 'handleGoogleCallback']);

    Route::get('/google/connect-drive/callback', [UserAuthController::class, 'handleConnectGoogleDriveCallback']);

    Route::get('/home', [UserDashboardController::class, 'home']);

    Route::post('/contact', [UserDashboardController::class, 'contact']);

    // User Email/Password Auth
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login'])->name('login');
    Route::post('/forgot-password', [UserAuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [UserAuthController::class, 'resetPassword']); 

    Route::get('/blogs', [UserBlogController::class, 'index']);
    Route::get('/blogs/{slug}', [UserBlogController::class, 'show']);

    Route::get('/faqs', [UserFaqController::class, 'index']);

    Route::get('/plan', [UserSubscriptionPlanController::class, 'index']);

    Route::post('/get-token', [UserAuthController::class, 'token']);

    Route::post('/upload-image', [UserAlbumController::class, 'save_image']);

    // Upload File
    Route::post('/upload/file', [UserAlbumController::class, 'upload']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [UserAuthController::class, 'logout']);
        
        Route::get('/user-dashboard', [UserDashboardController::class, 'index']);

        Route::get('/user-profile', [UserDashboardController::class, 'profile']);
        Route::post('/user-profile/update', [UserDashboardController::class, 'updateProfile']);
        Route::post('/user-profile/update-password', [UserDashboardController::class, 'updatePassword']);
        Route::delete('/user-profile/delete', [UserDashboardController::class, 'deleteAccount']);
        
        Route::get('/user', [UserAuthController::class, 'user']);

        Route::get('/google/connect-drive', [UserAuthController::class, 'connectGoogleDrive']);
        Route::post('/google/revoke-drive', [UserAuthController::class, 'disconnectGoogleDrive']);

        Route::post('/subscribe', [StripeSubscriptionController::class, 'subscribe']);
        Route::post('/subscription/cancel', [StripeSubscriptionController::class, 'cancel']);
        Route::get('/subscription/status', [StripeSubscriptionController::class, 'status']);

        Route::post('/albums', [UserAlbumController::class, 'create']);
        Route::get('/albums/list', [UserAlbumController::class, 'list']);

        Route::post('/drive/file', [UserAlbumController::class, 'get_file']);
    });

    Route::prefix('admin')->group(function () {
        Route::post('/register', [AdminAuthController::class, 'register']);
        Route::post('/login', [AdminAuthController::class, 'login']);
        Route::post('/forgot-password', [AdminAuthController::class, 'forgotPassword']);

        Route::middleware('auth:admin')->group(function () {
            Route::post('/logout', [AdminAuthController::class, 'logout']);

            Route::get('/dashboard', [AdminAuthController::class, 'dashboard']);


            Route::get('/all-user', [AdminUserController::class, 'index']);
            Route::post('/user', [AdminUserController::class, 'store']);
            Route::get('/user/{id}', [AdminUserController::class, 'show']);
            Route::put('/user/{id}', [AdminUserController::class, 'update']);
            Route::delete('/user/{id}', [AdminUserController::class, 'destroy']);

            Route::get('/blogs', [AdminBlogController::class, 'index']);
            Route::get('/blogs/{slug}', [UserBlogController::class, 'show']);
            Route::post('/blogs/store', [AdminBlogController::class, 'store']);
            Route::put('/blogs/{id}', [AdminBlogController::class, 'update']);
            Route::delete('/blogs/{slug}', [AdminBlogController::class, 'destroy']);

            Route::get('/faqs', [AdminFaqController::class, 'index']);
            Route::get('/faqs/{id}', [AdminFaqController::class, 'show']);
            Route::post('/faqs/store', [AdminFaqController::class, 'store']);
            Route::put('/faqs/{id}', [AdminFaqController::class, 'update']);
            Route::delete('/faqs/{id}', [AdminFaqController::class, 'destroy']);

            Route::get('/plans', [AdminSubscriptionPlanController::class, 'index']);
            Route::post('/plans', [AdminSubscriptionPlanController::class, 'store']);
            Route::get('/plans/{id}', [AdminSubscriptionPlanController::class, 'show']);
            Route::put('/plans/{id}', [AdminSubscriptionPlanController::class, 'update']);
            Route::put('/plans/status-update/{id}', [AdminSubscriptionPlanController::class, 'update_status']);
            Route::delete('/plans/{id}', [AdminSubscriptionPlanController::class, 'destroy']);

            Route::get('/google-drive', [AdminDriveAccountController::class, 'index']);

            Route::get('/bill-Subscription', [AdminSubscriptionPlanController::class, 'bill_Subscription']);

            Route::get('/setting', [AdminSettingsController::class, 'index']);
            Route::post('/setting', [AdminSettingsController::class, 'storeOrUpdate']);
        });
    });

    Route::get('/ping', function () {
        return response()->json(['message' => 'SnapShotAlbums API is running 🚀']);
    });
});

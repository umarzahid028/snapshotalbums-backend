<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\User\AuthController as UserAuthController;
use App\Http\Controllers\Api\V1\User\StripeSubscriptionController;
use App\Http\Controllers\Api\V1\User\BlogController as UserBlogController;
use App\Http\Controllers\Api\V1\User\FaqController as UserFaqController;
use App\Http\Controllers\Api\V1\User\AlbumController as UserAlbumController;
use App\Http\Controllers\Api\V1\User\DashboardController as UserDashboardController;

use App\Http\Controllers\Api\V1\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Api\V1\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\V1\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Api\V1\Admin\SubscriptionPlanController as AdminSubscriptionPlanController;


Route::prefix('v1')->group(function () {
    
    Route::get('/auth/google/redirect', [UserAuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [UserAuthController::class, 'handleGoogleCallback']);

    Route::get('/google/connect-drive/callback', [UserAuthController::class, 'handleConnectGoogleDriveCallback']);


    // User Email/Password Auth
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login'])->name('login');
    Route::post('/forgot-password', [UserAuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [UserAuthController::class, 'resetPassword']); 

    Route::get('/blogs', [UserBlogController::class, 'index']);
    Route::get('/blogs/{slug}', [UserBlogController::class, 'show']);

    Route::get('/faqs', [UserFaqController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [UserAuthController::class, 'logout']);
        
        Route::get('/user-dashboard', [UserDashboardController::class, 'index']);
        
        Route::get('/user', [UserAuthController::class, 'user']);

        Route::get('/google/connect-drive', [UserAuthController::class, 'connectGoogleDrive']);

        Route::post('/subscribe', [StripeSubscriptionController::class, 'subscribe']);
        Route::post('/subscription/cancel', [StripeSubscriptionController::class, 'cancel']);
        Route::get('/subscription/status', [StripeSubscriptionController::class, 'status']);

        Route::post('/albums', [UserAlbumController::class, 'create']);
        Route::get('/albums/list', [UserAlbumController::class, 'list']);

        Route::post('/upload/file', [UserAlbumController::class, 'upload']);
    });

    Route::prefix('admin')->group(function () {
        Route::post('/register', [AdminAuthController::class, 'register']);
        Route::post('/login', [AdminAuthController::class, 'login']);
        Route::post('/forgot-password', [AdminAuthController::class, 'forgotPassword']);

        Route::middleware('auth:admin')->group(function () {
            Route::post('/logout', [AdminAuthController::class, 'logout']);

            Route::get('/blogs', [AdminBlogController::class, 'index']);
            Route::get('/blogs/{slug}', [UserBlogController::class, 'show']);
            Route::post('/blogs/store', [AdminBlogController::class, 'store']);
            Route::put('/blogs/{slug}', [AdminBlogController::class, 'update']);
            Route::delete('/blogs/{slug}', [AdminBlogController::class, 'destroy']);

            Route::get('/faqs', [AdminFaqController::class, 'index']);
            Route::get('/faqs/{id}', [AdminFaqController::class, 'show']);
            Route::post('/faqs/store', [AdminFaqController::class, 'store']);
            Route::put('/faqs/{id}', [AdminFaqController::class, 'update']);
            Route::delete('/faqs/{id}', [AdminFaqController::class, 'destroy']);

            Route::get('/plans', [AdminSubscriptionPlanController::class, 'index']);
            Route::get('/plans/{slug}', [AdminSubscriptionPlanController::class, 'show']);
            Route::post('/plans', [AdminSubscriptionPlanController::class, 'store']);
            Route::put('/plans/{slug}', [AdminSubscriptionPlanController::class, 'update']);
            Route::delete('/plans/{slug}', [AdminSubscriptionPlanController::class, 'destroy']);
        });
    });

    Route::get('/ping', function () {
        return response()->json(['message' => 'SnapShotAlbums API is running 🚀']);
    });
});

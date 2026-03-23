<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ── Public routes (no token required) ─────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register-3-tokens', [AuthController::class, 'registerWithThreeTokens']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// ── Protected routes (Bearer token required) ───────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth helpers
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
    });

    // User CRUD (Admin only)
    Route::prefix('users')->middleware('role:admin')->group(function () {
        Route::get('/',          [UserController::class, 'index']);
        Route::get('/{user}',    [UserController::class, 'show']);
        Route::put('/{user}',    [UserController::class, 'update']);
        Route::patch('/{user}',  [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
    });

    // ── Role & Permission Management (Admin only) ────────────────────────
    Route::prefix('roles')->middleware('role:admin')->group(function () {
        Route::get('/', [RolePermissionController::class, 'getAllRoles']);
        Route::get('/{id}', [RolePermissionController::class, 'getRole']);
    });

    Route::prefix('permissions')->middleware('role:admin')->group(function () {
        Route::get('/', [RolePermissionController::class, 'getAllPermissions']);
    });

    // User roles and permissions endpoints
    Route::prefix('users')->middleware('role:admin')->group(function () {
        Route::get('/roles-permissions/all', [RolePermissionController::class, 'getAllUsersWithRoles']);
        Route::get('/{userId}/roles-permissions', [RolePermissionController::class, 'getUserRolesAndPermissions']);
        Route::post('/{userId}/assign-role', [RolePermissionController::class, 'assignRoleToUser']);
        Route::post('/{userId}/remove-role', [RolePermissionController::class, 'removeRoleFromUser']);
        Route::post('/{userId}/sync-roles', [RolePermissionController::class, 'syncUserRoles']);
    });

    // ── Notification Routes ────────────────────────────────────────────────
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'getNotifications']);
        Route::get('/unread', [NotificationController::class, 'getUnreadNotifications']);
        Route::post('/send', [NotificationController::class, 'sendNotification']);
        Route::post('/send-multiple', [NotificationController::class, 'sendToMultiple'])->middleware('role:admin');
        Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'deleteNotification']);
    });
});
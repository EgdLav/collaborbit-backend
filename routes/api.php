<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok',]);
});

/// registration
Route::post('/register', [AuthController::class, 'register']);
Route::get('/email/verify', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'verified.api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

//   user
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::get('/profile', [UserController::class, 'me']);
    Route::patch('/profile', [UserController::class, 'update']);
    Route::delete('/profile', [UserController::class, 'destroy']);
//   workspace operations
    Route::get('/workspaces', [WorkspaceController::class, 'index']);
    Route::get('/workspaces/owner', [WorkspaceController::class, 'owner']);
    Route::post('/workspaces', [WorkspaceController::class, 'store']);
    Route::patch('/workspaces/{workspace}', [WorkspaceController::class, 'update']);
    Route::get('/workspaces/{workspace}', [WorkspaceController::class, 'show']);
    Route::delete('/workspaces/{workspace}', [WorkspaceController::class, 'destroy']);

//   workspace categories operations
    Route::post('/workspaces/{workspace}/categories', [CategoryController::class, 'store']);
    Route::patch('/workspaces/{workspace}/categories/{category}', [CategoryController::class, 'update'])->scopeBindings();
    Route::delete('/workspaces/{workspace}/categories/{category}', [CategoryController::class, 'destroy'])->scopeBindings();
    Route::get('/workspaces/{workspace}/categories/{category}', [CategoryController::class, 'show'])->scopeBindings();
    // invite to workspace
    Route::post('/workspaces/{workspace}/invitations', [InvitationController::class, 'store'])->scopeBindings();
    Route::patch('/workspaces/{workspace}/invitations/{invitation}', [InvitationController::class, 'update'])->scopeBindings();
    Route::get('/invitations', [InvitationController::class, 'index']);

    //task operations
    Route::get('/workspaces/{workspace}/categories/{category}/tasks/{task}', [TaskController::class, 'show'])->scopeBindings();
    Route::post('/workspaces/{workspace}/categories/{category}/tasks', [TaskController::class, 'store']);
    Route::patch('/workspaces/{workspace}/categories/{category}/tasks/{task}', [TaskController::class, 'update'])->scopeBindings();
    Route::delete('/workspaces/{workspace}/categories/{category}/tasks/{task}', [TaskController::class, 'destroy'])->scopeBindings();
    Route::patch('/workspaces/{workspace}/categories/{category}/tasks/{task}/category', [TaskController::class, 'changeCategory'])->scopeBindings();

    // chat and messages
    Route::get('/chats', [ChatController::class, 'index']);
    Route::get('/chats/{chat}', [ChatController::class, 'show']);
    Route::get('/workspaces/{workspace}/chat', [ChatController::class, 'byWorkspace']);
    Route::post('/chats/private/{user}', [ChatController::class, 'store']);

    Route::get('/chats/{chat}/messages', [MessageController::class, 'index']);
    Route::post('/chats/{chat}/messages', [MessageController::class, 'store']);
});

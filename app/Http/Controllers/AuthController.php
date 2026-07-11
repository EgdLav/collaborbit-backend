<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}
    //
    public function register(RegisterRequest $request) {
        $user = $this->authService->register(
            $request->validated(),
            $request->file('avatar')
        );

        /// TODO: REMOVE
        switch ($user->email) {
            case "artemalmazov86@gmail.com":
                $user->markEmailAsVerified();
                break;
            case "artemalmazov86+1@gmail.com":
                $user->markEmailAsVerified();
                break;
            case "artemalmazov86+2@gmail.com":
                $user->markEmailAsVerified();
                break;
            case "artemalmazov86+3@gmail.com":
                $user->markEmailAsVerified();
                break;
            case "artemalmazov86+4@gmail.com":
                $user->markEmailAsVerified();
                break;
            default:break;
        }

        ///
        return ApiResponse::success('Check your email to verify your account.', 201, [
            'user' => new UserResource($user),
        ]);
    }
    public function login(LoginRequest $request) {
        $token = $this->authService->login($request->only('email', 'password'));
        if (!$token) {
            return ApiResponse::error('Authentication failed', 401);
        }
        if (!auth()->user()->hasVerifiedEmail()) {
            return ApiResponse::error('Email not verified', 403);
        }
        return ApiResponse::success('Successfully logged in', 200, [
            'token' => $token,
            'user' => new UserResource(auth()->user()),
        ]);
    }
    public function logout(Request $request)
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
    public function verifyEmail(Request $request)
    {
        if ($request->token === null) {
            return ApiResponse::error('Invalid token', 400);
        }
        $user = User::where('email_token', $request->token)->first();

        if (!$user) {
            return ApiResponse::error('Invalid token', 400);
        }
        if ($user->hasVerifiedEmail()) {
            return ApiResponse::success('Email already verified');
        }
//        $user->email_verified_at = now();
//        $user->email_token = null;
//        $user->save();
//        return response()->json(['message' => 'Email verified']);
        $user->markEmailAsVerified();
        return ApiResponse::success('Email verified successfully');
    }
}

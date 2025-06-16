<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use Interns2025b\Http\Requests\LoginRequest;
use Symfony\Component\HttpFoundation\Response as Status;

class LoginController extends Controller
{
    public function login(LoginRequest $loginRequest, AuthManager $auth): JsonResponse
    {
        $credentials = $loginRequest->validated();

        if (!$auth->attempt($credentials)) {
            if ($loginRequest->expectsJson()) {
                return response()->json([
                    "message" => "auth.failed",
                ], Status::HTTP_FORBIDDEN);
            }
        }

        $user = $auth->user();
        $token = $user->createToken("token")->plainTextToken;

        return response()->json([
            "message" => "success",
            "token" => $token,
            "user_id" => $user->id,
        ], Response::HTTP_OK);
    }
}

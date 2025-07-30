<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use Interns2025b\Http\Requests\LoginRequest;
use Interns2025b\Http\Resources\UserResource;
use Symfony\Component\HttpFoundation\Response as Status;

class LoginController extends Controller
{
    public function login(LoginRequest $loginRequest, AuthManager $auth): JsonResponse
    {
        $credentials = $loginRequest->validated();

        if (!$auth->attempt($credentials)) {
            return response()->json([
                "message" => __("auth.failed"),
            ], Status::HTTP_FORBIDDEN);
        }

        $user = $auth->user();
        $user->load("badge");
        $token = $user->createToken("token")->plainTextToken;

        return response()->json([
            "message" => "success",
            "token" => $token,
            "user" => new UserResource($user),
        ], Status::HTTP_OK);
    }
}

<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response as Status;

class LogoutController
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        } else {
            Auth::guard("web")->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            "message" => __("auth.logout"),
        ], Status::HTTP_OK);
    }
}

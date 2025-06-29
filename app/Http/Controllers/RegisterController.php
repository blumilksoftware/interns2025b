<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Interns2025b\Http\Requests\RegisterRequest;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;

class RegisterController extends Controller
{
    public function register(RegisterRequest $registerRequest): JsonResponse
    {
        $validated = $registerRequest->validated();
        $userExist = User::query()->where("email", $validated["email"])->exists();

        if (!$userExist) {
            $user = new User($validated);
            $user->password = Hash::make($validated["password"]);
            $user->save();
            $user->assignRole("user");

            $user->sendEmailVerificationNotification();
        }

        return response()->json([
            "message" => "success",
            "notification" => "We have sent you a confirmation on your email address"
        ])->setStatusCode(Status::HTTP_OK);
    }
}

<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Interns2025b\Actions\RegisterUserAction;
use Interns2025b\Http\Requests\RegisterRequest;
use Symfony\Component\HttpFoundation\Response as Status;

class RegisterController extends Controller
{
    public function register(RegisterRequest $registerRequest, RegisterUserAction $action): JsonResponse
    {
        $action->execute($registerRequest->toDto());

        return response()->json([
            "message" => "success",
        ])->setStatusCode(Status::HTTP_OK);
    }
}

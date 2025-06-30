<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Interns2025b\Actions\LogoutUserAction;
use Symfony\Component\HttpFoundation\Response as Status;

class LogoutController
{
    public function __invoke(Request $request, LogoutUserAction $action): JsonResponse
    {
        $action->execute($request->user());

        return response()->json([
            "message" => __("auth.logout"),
        ], Status::HTTP_OK);
    }
}

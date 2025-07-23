<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Interns2025b\Http\Requests\SendOrganizationInvitationRequest;
use Interns2025b\Mail\OrganizationInvitationMail;
use Interns2025b\Models\Organization;
use Symfony\Component\HttpFoundation\Response;

class OrganizationInvitationController extends Controller
{
    private const DAY = 86400;

    public function send(SendOrganizationInvitationRequest $request, Organization $organization, Mailer $mailer): JsonResponse
    {
        $this->authorize("invite", $organization);

        $user = $request->user();
        $limiter = app(RateLimiter::class);
        $key = "org-invite:{$user->id}:{$organization->id}:" . md5($request->email);

        if ($limiter->tooManyAttempts($key, 1)) {
            return response()->json([
                "message" => __("organization.invitation_throttled"),
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        $limiter->hit($key, self::DAY);

        $mailer->to($request->email)->send(new OrganizationInvitationMail($organization));

        return response()->json([
            "message" => __("organization.invitation_sent"),
        ]);
    }

    public function accept(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user || $user->email !== $request->query("email")) {
            return response()->json([
                "message" => __("organization.invitation_unauthorized"),
            ], Response::HTTP_FORBIDDEN);
        }

        $organization = Organization::findOrFail($request->query("organization"));
        $organization->users()->syncWithoutDetaching([$user->id]);

        return response()->json([
            "message" => __("organization.invitation_accepted"),
        ]);
    }
}

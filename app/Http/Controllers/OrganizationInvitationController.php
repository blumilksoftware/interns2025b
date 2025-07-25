<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Interns2025b\Actions\ThrottleAction;
use Interns2025b\Http\Requests\SendOrganizationInvitationRequest;
use Interns2025b\Mail\OrganizationInvitationMail;
use Interns2025b\Models\Organization;
use Symfony\Component\HttpFoundation\Response;

class OrganizationInvitationController extends Controller
{
    public function send(SendOrganizationInvitationRequest $request, Organization $organization, Mailer $mailer, ThrottleAction $throttle): JsonResponse
    {
        $this->authorize("invite", $organization);

        $user = $request->user();
        $key = "org-invite:{$user->id}:{$organization->id}:" . md5($request->email);

        $throttle->handle($key, "1day", "organization.invitation_throttled");

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

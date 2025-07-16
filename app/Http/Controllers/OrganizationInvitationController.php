<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Interns2025b\Http\Requests\SendOrganizationInvitationRequest;
use Interns2025b\Mail\OrganizationInvitationMail;
use Interns2025b\Models\Organization;
use Symfony\Component\HttpFoundation\Response;

class OrganizationInvitationController extends Controller
{
    public function send(SendOrganizationInvitationRequest $request, Organization $organization, Mailer $mailer): JsonResponse
    {
        $this->authorize("invite", $organization);

        $url = URL::temporarySignedRoute(
            "organizations.accept-invite",
            now()->addHours(48),
            [
                "organization" => $organization->id,
                "email" => $request->email,
            ],
        );

        $mailer->to($request->email)->send(new OrganizationInvitationMail($organization->name, $url));

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

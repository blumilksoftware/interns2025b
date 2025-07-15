<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Interns2025b\Mail\OrganizationInvitationMail;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response;

class OrganizationInvitationController extends Controller
{
    public function send(Request $request, Organization $organization): JsonResponse
    {
        $request->validate([
            "email" => "required|email|exists:users,email",
        ]);

        $this->authorize("invite", $organization);

        $url = URL::temporarySignedRoute(
            "organization.accept-invite",
            now()->addHours(48),
            [
                "organization" => $organization->id,
                "email" => $request->email,
            ],
        );

        Mail::to($request->email)->send(new OrganizationInvitationMail($organization->name, $url));

        return response()->json([
            "message" => __("organization.invitation_sent"),
        ]);
    }

    public function accept(Request $request): JsonResponse
    {
        $user = User::where("email", $request->query("email"))->first();

        if (!$user || $user->id !== auth()->id()) {
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

<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Interns2025b\Mail\DeleteAccountLinkMail;
use Interns2025b\Models\User;

class UserDeletionController extends Controller
{
    public function requestDelete(Request $request)
    {
        $user = $request->user();

        $url = URL::temporarySignedRoute(
            "api.confirmDelete",
            now()->addMinutes(60),
            ["user" => $user->id],
        );

        Mail::to($user->email)->send(new DeleteAccountLinkMail($user, $url));

        return response()->json(["message" => __("profile.email_sent")], 200);
    }

    public function confirmDelete(Request $request, $user)
    {
        $user = User::findOrFail($user);

        $user->delete();

        return response()->json(["message" => __("profile.deleted")]);
    }
}

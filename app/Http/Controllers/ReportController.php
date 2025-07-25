<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Interns2025b\Models\Event;
use Interns2025b\Models\Organization;
use Interns2025b\Models\Report;
use Interns2025b\Models\User;

class ReportController
{
    public function store(Request $request)
    {
        $request->validate([
            "type" => "required|in:user,organization,event",
            "id" => "required|integer",
            "reason" => "nullable|string|max:1000",
        ]);

        $user = $request->user();
        $type = match ($request->input("type")) {
            "user" => User::class,
            "organization" => Organization::class,
            "event" => Event::class,
        };

        $alreadyReported = Report::where("reporter_id", $user->id)
            ->where("reportable_type", $type)
            ->where("reportable_id", $request->id)
            ->whereDate("created_at", Carbon::today())
            ->exists();

        if ($alreadyReported) {
            return response()->json(["message" => __("You have already reported this today.")], 429);
        }

        Report::create([
            "reporter_id" => $user->id,
            "reportable_type" => $type,
            "reportable_id" => $request->id,
            "reason" => $request->input("reason"),
        ]);

        return response()->json(["message" => __("Reported successfully.")]);
    }
}

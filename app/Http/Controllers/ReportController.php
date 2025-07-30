<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Interns2025b\Http\Requests\StoreReportRequest;
use Interns2025b\Http\Resources\ReportResource;
use Interns2025b\Models\Event;
use Interns2025b\Models\Organization;
use Interns2025b\Models\Report;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;

class ReportController
{
    public function store(StoreReportRequest $request): JsonResponse
    {
        $user = $request->user();

        $type = match ($request->input("type")) {
            "user" => User::class,
            "organization" => Organization::class,
            "event" => Event::class,
        };

        $alreadyReported = Report::query()->where("reporter_id", $user->id)
            ->where("reportable_type", $type)
            ->where("reportable_id", $request->input("id"))
            ->whereDate("created_at", Carbon::today())
            ->exists();

        if ($alreadyReported) {
            return response()->json([
                "message" => __("report.already_reported"),
            ], Status::HTTP_TOO_MANY_REQUESTS);
        }

        Report::create([
            "reporter_id" => $user->id,
            "reportable_type" => $type,
            "reportable_id" => $request->input("id"),
            "reason" => $request->input("reason"),
        ]);

        return response()->json([
            "message" => __("report.success"),
        ], Status::HTTP_OK);
    }

    public function userReports(): AnonymousResourceCollection
    {
        return ReportResource::collection(
            Report::query()->where("reportable_type", User::class)->latest()->get(),
        );
    }

    public function organizationReports(): AnonymousResourceCollection
    {
        return ReportResource::collection(
            Report::query()->where("reportable_type", Organization::class)->latest()->get(),
        );
    }

    public function eventReports(): AnonymousResourceCollection
    {
        return ReportResource::collection(
            Report::query()->where("reportable_type", Event::class)->latest()->get(),
        );
    }
}

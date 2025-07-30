<?php

declare(strict_types=1);

namespace Interns2025b\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $reporter_id
 * @property string $reportable_type
 * @property int $reportable_id
 * @property string $reason
 * @property Model|MorphTo $reportable
 */
class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        "reporter_id",
        "reportable_type",
        "reportable_id",
        "reason",
    ];

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function alreadyReportedToday(int $reporterId, string $reportableType, int $reportableId): bool
    {
        return static::query()
            ->where("reporter_id", $reporterId)
            ->where("reportable_type", $reportableType)
            ->where("reportable_id", $reportableId)
            ->whereDate("created_at", Carbon::today())
            ->exists();
    }
}

<?php

declare(strict_types=1);

namespace Interns2025b\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
}

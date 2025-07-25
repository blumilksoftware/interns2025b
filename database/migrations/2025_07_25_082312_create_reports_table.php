<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("reports", function (Blueprint $table): void {
            $table->id();
            $table->foreignId("reporter_id")->constrained("users")->cascadeOnDelete();
            $table->morphs("reportable");
            $table->text("reason")->nullable();
            $table->timestamps();

            $table->unique([
                "reporter_id",
                "reportable_type",
                "reportable_id",
                "created_at",
            ], "daily_unique_report");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("reports");
    }
};

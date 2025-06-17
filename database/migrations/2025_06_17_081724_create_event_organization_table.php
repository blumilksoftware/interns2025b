<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("event_organization", function (Blueprint $table): void {
            $table->id();
            $table->foreignId("event_id")->constrained()->onDelete("cascade");
            $table->foreignId("organization_id")->constrained()->onDelete("cascade");
            $table->timestamps();
            $table->unique(["event_id", "organization_id"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("event_organization");
    }
};

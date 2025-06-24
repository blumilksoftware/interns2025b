<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("event_user", function (Blueprint $table): void {
            $table->id();
            $table->foreignId("event_id")->constrained()->cascadeOnDelete();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(["event_id", "user_id"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("event_user");
    }
};

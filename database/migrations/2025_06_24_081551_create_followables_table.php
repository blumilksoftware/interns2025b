<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("followables", function (Blueprint $table): void {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->cascadeOnDelete();
            $table->morphs("followable");
            $table->timestamps();
            $table->unique(["user_id", "followable_type", "followable_id"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("followables");
    }
};

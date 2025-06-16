<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("organizations", function (Blueprint $table): void {
            $table->id();
            $table->string("name");
            $table->foreignId("owner_id")->nullable()->constrained("users")->nullOnDelete();
            $table->string("group_url")->nullable();
            $table->string("avatar_url")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("organizations");
    }
};

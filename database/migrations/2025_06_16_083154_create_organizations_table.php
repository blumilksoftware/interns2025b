<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Interns2025b\Models\User;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("organizations", function (Blueprint $table): void {
            $table->id();
            $table->string("name");
            $table->foreignIdFor(User::class, "owner_id")->nullable()->constrained()->nullOnDelete();
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

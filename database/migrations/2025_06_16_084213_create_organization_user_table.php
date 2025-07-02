<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("organization_user", function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(["organization_id", "user_id"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("organization_user");
    }
};

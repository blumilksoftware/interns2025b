<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("events", function (Blueprint $table): void {
            $table->id();
            $table->string("title");
            $table->longText("description")->nullable();
            $table->dateTime("start")->nullable();
            $table->dateTime("end")->nullable();
            $table->string("location")->nullable();
            $table->string("address")->nullable();
            $table->decimal("latitude", 10, 7)->nullable();
            $table->decimal("longitude", 10, 7)->nullable();
            $table->boolean("is_paid")->default(false);
            $table->decimal("price", 8, 2)->nullable();
            $table->enum("status", ["draft", "published", "ongoing", "ended", "canceled"])->default("draft");
            $table->string("image_url")->nullable();
            $table->string("age_category")->nullable();
            $table->string("owner_type");
            $table->unsignedBigInteger("owner_id");
            $table->index(["owner_type", "owner_id"]);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("events");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            // 'photography' or 'videography' — matches the /services/{type} routes.
            $table->string('service_type', 40);
            $table->string('name');
            // Bullet points, stored as a JSON array of strings.
            $table->json('description')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('offered_price', 12, 2)->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['service_type', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_packages');
    }
};

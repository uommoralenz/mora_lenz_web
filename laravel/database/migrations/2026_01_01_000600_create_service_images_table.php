<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Images for the rotating carousel at the top of each /services/{type} page.
        // This replaces the loose Supabase storage bucket the original site listed.
        Schema::create('service_images', function (Blueprint $table) {
            $table->id();
            $table->string('service_type', 40);
            $table->string('image_url');
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['service_type', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_images');
    }
};

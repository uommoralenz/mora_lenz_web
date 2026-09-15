<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('featured_galleries', function (Blueprint $table) {
            $table->string('facebook_album_url', 2048)->nullable()->after('description');
        });

        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_id')->constrained('featured_galleries')->cascadeOnDelete();
            $table->string('image_url');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['gallery_id', 'sort_order']);
        });

        // Preserve all existing entries as one-photo albums.
        DB::table('featured_galleries')->whereNotNull('image_url')->orderBy('id')->each(function ($gallery) {
            DB::table('gallery_images')->insert([
                'gallery_id' => $gallery->id,
                'image_url' => $gallery->image_url,
                'description' => $gallery->description,
                'sort_order' => 0,
                'created_at' => $gallery->created_at,
                'updated_at' => $gallery->updated_at,
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
        Schema::table('featured_galleries', function (Blueprint $table) {
            $table->dropColumn('facebook_album_url');
        });
    }
};

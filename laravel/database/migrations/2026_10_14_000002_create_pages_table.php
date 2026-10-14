<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Free-form pages built from the same blocks as an event page. Only
        // "about" exists for now; the slug column leaves room for more.
        if (! Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('title');
                $table->text('description')->nullable();
                $table->longText('content')->nullable();
                $table->text('page_options')->nullable();
                $table->string('image_url')->nullable();
                $table->timestamps();
            });
        }

        if (DB::table('pages')->where('slug', 'about')->doesntExist()) {
            DB::table('pages')->insert([
                'slug' => 'about',
                'title' => 'About Mora Lenz',
                'description' => 'Mora Lenz is the Mass Media Club of the University of Moratuwa. We capture moments, create stories and bring visions to life through the lens of creativity.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};

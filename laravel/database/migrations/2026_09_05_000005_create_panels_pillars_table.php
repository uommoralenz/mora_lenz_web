<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('panels_pillars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['panel', 'pillar'])->default('panel');
            $table->string('icon')->default('users');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('member_count')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'sort_order']);
            $table->index(['sort_order', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panels_pillars');
    }
};

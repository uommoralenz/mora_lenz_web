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
        Schema::create('admin_sessions', function (Blueprint $table) {
            $table->char('token_hash', 64)->primary();
            $table->foreignUuid('admin_id')->constrained('admin_users')->cascadeOnDelete();
            $table->char('password_version', 64);
            $table->unsignedBigInteger('expires_at')->index();
        });
        Schema::create('admin_rate_limits', function (Blueprint $table) {
            $table->char('bucket', 64)->primary();
            $table->unsignedInteger('attempts');
            $table->unsignedBigInteger('expires_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_rate_limits');
        Schema::dropIfExists('admin_sessions');
    }
};

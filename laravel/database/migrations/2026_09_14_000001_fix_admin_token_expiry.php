<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_tokens', function (Blueprint $table) {
            // DATETIME avoids legacy MySQL's implicit first-TIMESTAMP updates.
            $table->dateTime('expires_at')->change();
        });
    }

    public function down(): void
    {
        Schema::table('admin_tokens', function (Blueprint $table) {
            $table->timestamp('expires_at')->useCurrentOnUpdate(false)->change();
        });
    }
};

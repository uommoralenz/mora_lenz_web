<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('featured_galleries', 'show_on_homepage')) {
            Schema::table('featured_galleries', fn (Blueprint $table) => $table->boolean('show_on_homepage')->default(true));
        }
    }

    public function down(): void
    {
        Schema::table('featured_galleries', fn (Blueprint $table) => $table->dropColumn('show_on_homepage'));
    }
};

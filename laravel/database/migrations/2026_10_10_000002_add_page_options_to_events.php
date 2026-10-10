<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // How the page AROUND the content blocks is laid out — column width,
        // which hero treatment, whether the date/location pills show. Keeping
        // it as one JSON column means a new layout choice is a panel change
        // rather than another migration.
        //
        // Null means "every default", so existing events render exactly as
        // they did before. See App\Support\EventBlocks::pageOptions().
        //
        // text, not json, to match the content column next to it: the app casts
        // both to arrays itself and never queries inside them, and plain text
        // keeps working on the older MySQL this is deployed to.
        Schema::table('events', function (Blueprint $table) {
            $table->text('page_options')->nullable()->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('page_options');
        });
    }
};

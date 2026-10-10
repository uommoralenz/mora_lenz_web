<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // page_options started as `text` (65,535 BYTES in MySQL) when it only
        // held three layout choices. It now also carries the page-wide custom
        // code, up to 20,000 characters — and JSON encoding inflates that well
        // past the limit: every "/" becomes "\/" and every non-ASCII character
        // becomes a 6-byte \uXXXX escape. Over the limit, MySQL either rejects
        // the save or silently truncates it, which leaves the column holding
        // JSON that no longer parses.
        //
        // longText matches the `content` column next to it, which holds the
        // blocks (including custom HTML) for the same reason.
        Schema::table('events', function (Blueprint $table) {
            $table->longText('page_options')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->text('page_options')->nullable()->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('featured_galleries', 'facebook_album_urls')) {
            Schema::table('featured_galleries', fn (Blueprint $table) => $table->json('facebook_album_urls')->nullable());
        }

        // Carry the existing single link over as the first entry of the list.
        DB::table('featured_galleries')
            ->whereNotNull('facebook_album_url')
            ->whereNull('facebook_album_urls')
            ->orderBy('id')
            ->each(fn ($g) => DB::table('featured_galleries')->where('id', $g->id)
                ->update(['facebook_album_urls' => json_encode([$g->facebook_album_url])]));
    }

    public function down(): void
    {
        if (Schema::hasColumn('featured_galleries', 'facebook_album_urls')) {
            Schema::table('featured_galleries', fn (Blueprint $table) => $table->dropColumn('facebook_album_urls'));
        }
    }
};

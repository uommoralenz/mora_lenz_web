<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Past events carry their own photos and Facebook album links, which
        // replaces the separate Gallery section. The old featured_galleries
        // tables are left in place so nothing already uploaded is lost.
        //
        // text, not json, for the same reason as events.page_options: the app
        // casts it itself and the older MySQL here has no use for json.
        if (! Schema::hasColumn('events', 'facebook_album_urls')) {
            Schema::table('events', function (Blueprint $table) {
                $table->text('facebook_album_urls')->nullable()->after('image_url');
            });
        }

        if (! Schema::hasTable('event_photos')) {
            Schema::create('event_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
                $table->string('image_url');
                $table->string('caption', 300)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index(['event_id', 'sort_order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('event_photos');

        if (Schema::hasColumn('events', 'facebook_album_urls')) {
            Schema::table('events', fn (Blueprint $table) => $table->dropColumn('facebook_album_urls'));
        }
    }
};

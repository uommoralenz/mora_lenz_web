<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rank within a group or subgroup, so a body like the Executive
        // Committee can be shown as the hierarchy it actually is instead of one
        // flat grid where the President sits in the same size card as everyone
        // else. 1 = the top role, 2 = the office-bearers under it, 3 = the rest.
        //
        // Defaulting to 3 leaves every existing member exactly where they are:
        // a group with no ranks set renders as the same single grid as before.
        Schema::table('team_members', function (Blueprint $table) {
            $table->unsignedTinyInteger('tier')->default(3)->after('profession');
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->index(['group_id', 'subgroup_id', 'tier', 'sort_order'], 'team_members_tier_index');
        });
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropIndex('team_members_tier_index');
            $table->dropColumn('tier');
        });
    }
};

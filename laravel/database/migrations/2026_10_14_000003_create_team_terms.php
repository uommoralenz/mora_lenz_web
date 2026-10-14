<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The committee changes every year, and each year's team is kept as
        // its own term with its own page at /team/{slug}. Groups belong to a
        // term; subgroups and members follow their group as before.
        if (! Schema::hasTable('team_terms')) {
            Schema::create('team_terms', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                // The year the term started, which is what the pages sort by.
                $table->unsignedSmallInteger('year');
                $table->string('description', 300)->nullable();
                $table->boolean('is_current')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'year']);
            });
        }

        if (! Schema::hasColumn('team_groups', 'term_id')) {
            Schema::table('team_groups', function (Blueprint $table) {
                $table->foreignId('term_id')->nullable()->after('id')
                    ->constrained('team_terms')->cascadeOnDelete();
            });
        }

        // Everything entered so far is the team serving now.
        if (DB::table('team_terms')->doesntExist()) {
            $year = (int) now()->year;
            $name = $year.'/'.substr((string) ($year + 1), -2);

            DB::table('team_terms')->insert([
                'name' => $name,
                'slug' => $year.'-'.substr((string) ($year + 1), -2),
                'year' => $year,
                'is_current' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $current = DB::table('team_terms')->where('is_current', true)->value('id')
            ?? DB::table('team_terms')->orderByDesc('year')->value('id');

        DB::table('team_groups')->whereNull('term_id')->update(['term_id' => $current]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('team_groups', 'term_id')) {
            Schema::table('team_groups', function (Blueprint $table) {
                $table->dropConstrainedForeignId('term_id');
            });
        }

        Schema::dropIfExists('team_terms');
    }
};

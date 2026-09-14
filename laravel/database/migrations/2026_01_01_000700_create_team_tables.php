<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Group -> Subgroup -> Member, exactly the hierarchy the original
        // OurTeam component rendered. Members may hang directly off a group
        // (subgroup_id null) or off one of its subgroups.
        Schema::create('team_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('team_subgroups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('team_groups')->cascadeOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['group_id', 'is_active', 'sort_order']);
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('team_groups')->cascadeOnDelete();
            $table->foreignId('subgroup_id')->nullable()->constrained('team_subgroups')->nullOnDelete();
            $table->string('name');
            $table->string('profession');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['group_id', 'subgroup_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('team_subgroups');
        Schema::dropIfExists('team_groups');
    }
};

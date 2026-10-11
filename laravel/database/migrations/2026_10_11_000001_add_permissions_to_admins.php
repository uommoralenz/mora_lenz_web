<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // List of section keys this admin may manage. Super admins ignore it.
            $table->json('permissions')->nullable()->after('is_super_admin');
        });

        // Before this existed every admin could manage everything. Keep that
        // true for the accounts that already exist so nobody is locked out;
        // a super admin can narrow them afterwards.
        DB::table('admins')
            ->where('is_super_admin', false)
            ->update(['permissions' => json_encode(['events', 'gallery', 'services', 'team', 'messages'])]);
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};

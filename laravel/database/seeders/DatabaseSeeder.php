<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // SuperAdminSeeder is safe to re-run: it never overwrites an existing
        // account, so `php artisan db:seed` will not reset a changed password.
        $this->call([
            SuperAdminSeeder::class,
            SampleContentSeeder::class,
        ]);
    }
}

<?php

use App\Models\AdminUser;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:create-super {email?}', function () {
    $email = (string) ($this->argument('email') ?: $this->ask('Super admin email'));
    $name = (string) $this->ask('Super admin name', 'Super Admin');
    $password = (string) $this->secret('Super admin password');
    $confirmPassword = (string) $this->secret('Confirm password');

    $validator = Validator::make([
        'email' => $email,
        'name' => $name,
        'password' => $password,
        'confirm_password' => $confirmPassword,
    ], [
        'email' => ['required', 'email', Rule::unique('admin_users', 'email')],
        'name' => ['required', 'string', 'max:255'],
        'password' => ['required', 'string', 'min:10'],
        'confirm_password' => ['same:password'],
    ]);

    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }

        return self::FAILURE;
    }

    AdminUser::query()->create([
        'name' => $name,
        'email' => $email,
        'password_hash' => Hash::make($password),
        'role' => 'super_admin',
        'is_active' => true,
    ]);

    $this->info('Super admin created.');

    return self::SUCCESS;
})->purpose('Create the first super admin account for the Vercel admin panel');

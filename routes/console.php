<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:create {email} {name}', function (): int {
    $input = [
        'email' => $this->argument('email'),
        'name' => $this->argument('name'),
        'password' => $this->secret('Choose a secure administrator password'),
    ];

    $confirmation = $this->secret('Confirm the administrator password');
    $validator = Validator::make(
        [...$input, 'password_confirmation' => $confirmation],
        [
            'email' => ['required', 'email:rfc'],
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()],
        ]
    );

    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }

        return 1;
    }

    $user = User::query()->firstOrNew(['email' => $input['email']]);
    $user->forceFill([
        'name' => $input['name'],
        'password' => $input['password'],
        'email_verified_at' => $user->email_verified_at ?? now(),
        'is_admin' => true,
    ])->save();

    $this->info("Administrator {$user->email} is ready. Sign in at /admin/login.");

    return 0;
})->purpose('Create an administrator or promote and reset an existing user');

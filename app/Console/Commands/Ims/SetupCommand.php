<?php

namespace App\Console\Commands\Ims;

use App\Cloud\Cloud;
use App\Models\User;
use Illuminate\Console\Command;

class SetupCommand extends Command
{
    protected $signature = 'ims:setup';

    protected $description = 'Setup IoT Management System';

    public function handle()
    {
        $user = config('ims.user');
        $password = config('ims.password');
        $deviceName = config('ims.env');

        $loginResponse = Cloud::login($user, $password, $deviceName);

        $escaped = preg_quote('=' . config('ims.auth_token'), '/');
        $escaped = "/^IMS_AUTH_TOKEN{$escaped}/m";

        file_put_contents(base_path('.env'), preg_replace(
            $escaped,
            'IMS_AUTH_TOKEN=' . $loginResponse['token'],
            file_get_contents(base_path('.env'))
        ));

        [$id, $token] = explode('|', $loginResponse['token']);

        $loginResponse['token'] = $token;

        $user = User::create([
            'id' => $loginResponse['id'],
            'name' => $loginResponse['username'],
            'email' => $loginResponse['email'],
            'password' => \Hash::make($password),
            'is_admin' => 0
        ]);

        $user->id = $loginResponse['id'];
        $user->save();
    }
}

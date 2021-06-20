<?php


namespace App\Cloud;


use App\Models\User;

class Cloud
{
    public static function post($uri, $request, $withToken = true)
    {
        $url = config('ims.cloud_url');
        if ($withToken)
        {
            $token = config('ims.auth_token');

            if (empty($token))
            {
                $user = config('ims.user');
                $password = config('ims.password');
                $deviceName = config('ims.env');

                self::login($user, $password, $deviceName);
            }
        }

        if (!\Str::startsWith($uri, '/'))
        {
            $uri = '/' . $uri;
        }

        $response = \Http::withOptions([
            'verify' => false,
        ]);
        if ($withToken)
        {
            $response = $response->withToken($token);
        }

        $response = $response->acceptJson()->post($url . $uri, $request);

        return $response->json();
    }

    public static function login($email, $password, $deviceName)
    {
        $response = Cloud::post('login', ['email' => $email, 'password' => $password, 'device_name' => $deviceName], false);

        $escaped = preg_quote('=' . config('ims.auth_token'), '/');
        $escaped = "/^IMS_AUTH_TOKEN{$escaped}/m";

        [$id, $token] = explode('|', $response['token']);

        $user = User::whereEmail($email)->first();

        $localToken = $user->tokens()->create([
            'token' => hash('sha256', $token),
            'name' => 'fog',
            'abilities' => ['*'],
        ]);

        $response['token'] = $localToken->id . '|' . $token;

        file_put_contents(base_path('.env'), preg_replace(
            $escaped,
            'IMS_AUTH_TOKEN=' . $response['token'],
            file_get_contents(base_path('.env'))
        ));

        return $response;
    }
}

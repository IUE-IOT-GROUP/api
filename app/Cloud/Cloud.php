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
            ray($request);
            $token = config('ims.auth_token');
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

        [$id, $token] = explode('|', $response['token']);

        $user = User::whereEmail($email)->first();

        $localToken = $user->tokens()->create([
            'token' => hash('sha256', $token),
            'name' => $deviceName,
            'abilities' => ['*'],
        ]);

        $response['token'] = $localToken->id . '|' . $token;

        return $response;
    }
}

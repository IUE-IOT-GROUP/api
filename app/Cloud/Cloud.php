<?php


namespace App\Cloud;


class Cloud
{
    public static function post($uri, $request)
    {
        $url = config('ims.cloud_url');

        if (! \Str::startsWith($uri, '/'))
            $uri = '/' . $uri;

        $response = \Http::withOptions([
            'verify' => false
        ])->acceptJson()->post($url . $uri, $request);

        return $response->json();
    }
}

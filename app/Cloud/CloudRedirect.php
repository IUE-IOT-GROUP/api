<?php


namespace App\Cloud;


class CloudRedirect
{
    private static $server;
    private static $port;
    private static $url;

    public static function post($uri, $request)
    {
        $url = config('ims.cloud_ip');

        $response = \Http::acceptJson()->post($url . '/' . $uri, $request);
        return $response->json();
    }
}

<?php

namespace Moodle;

class REST
{
    static $uri;

    public static function setServiceURI($uri)
    {
        self::$uri = rtrim($uri, '/');
    }

    public function get($path)
    {
        $curl = curl_init($uri .'/'. ltrim($path, '/'));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curl_response = curl_exec($curl);
        curl_close($curl);

        return json_decode($curl_response);
    }


    public function post($path, $data)
    {
        $curl = curl_init($uri .'/'. ltrim($path, '/'));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $curl_response = curl_exec($curl);
        curl_close($curl);

        return json_decode($curl_response);
    }
}

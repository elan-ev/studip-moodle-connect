<?php

namespace Moodle;

class REST
{
    static $uri;
    static $token;

    public static function setServiceURI($uri)
    {
        self::$uri = rtrim($uri, '/');
    }

    public static function setToken($token)
    {
        self::$token = $token;
    }

    public static function checkConfig()
    {
        if (!self::$uri || !self::$token) {
            throw new UnconfiguredException('Moodle connector is not configured!');
        }
    }

    public static function get($function)
    {
        self::checkConfig();

        $curl = curl_init(self::$uri . '/webservice/rest/server.php?wstoken=' .
            self::$token . '&wsfunction=' . $function . '&moodlewsrestformat=json');

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);                       // follow redirects (fe http to https)

        $curl_response = curl_exec($curl);
        curl_close($curl);

        return self::except(\json_decode($curl_response, true), $function);
    }


    public static function post($function, $data)
    {
        self::checkConfig();

        $curl = curl_init(self::$uri . '/webservice/rest/server.php?wstoken=' .
            self::$token . '&wsfunction=' . $function . '&moodlewsrestformat=json');

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, \http_build_query($data));
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);                       // follow redirects (fe http to https)

        $curl_response = curl_exec($curl);
        curl_close($curl);

        return self::except(\json_decode($curl_response, true), $function);
    }

    /**
     * Throws an exception if an API response contains an exception, returns the
     * response if none is found
     *
     * @param mixed $response the response-array from the api-call
     * @param string $api_route the route whoch was tried to call
     *
     * @return mixed $response
     *
     * @throws Moodle\APIException
     */
    public static function except($response, $api_route)
    {
        if ($response['exception']) {
            throw new APIException($response, $api_route);
        }

        return $response;
    }
}

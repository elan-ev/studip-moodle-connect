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

    public function get($function)
    {
        $curl = curl_init($uri .'/webservice/rest/server.php?wstoken=' . self::$token . '&wsfunction=' . $function . '&moodlewsrestformat=json');

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curl_response = curl_exec($curl);
        curl_close($curl);

        return self::xml2array(new SimpleXml($curl_response));
    }


    public function post($function, $data)
    {
        $curl = curl_init($uri .'/webservice/rest/server.php?wstoken=' . self::$token . '&wsfunction=' . $function . '&moodlewsrestformat=json');

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $curl_response = curl_exec($curl);
        curl_close($curl);

        return self::xml2array(new SimpleXml($curl_response));
    }

    /**
     * function xml2array
     *
     * This function is part of the PHP manual.
     *
     * The PHP manual text and comments are covered by the Creative Commons
     * Attribution 3.0 License, copyright (c) the PHP Documentation Group
     *
     * @author  k dot antczak at livedata dot pl
     * @date    2011-04-22 06:08 UTC
     * @link    http://www.php.net/manual/en/ref.simplexml.php#103617
     * @license http://www.php.net/license/index.php#doc-lic
     * @license http://creativecommons.org/licenses/by/3.0/
     * @license CC-BY-3.0 <http://spdx.org/licenses/CC-BY-3.0>
     */
    private static function xml2array ( $xmlObject, $out = array () )
    {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? self::xml2array ( $node ) : $node;

        return $out;
    }
}

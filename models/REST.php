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

    public static function get($function)
    {
        $curl = curl_init(self::$uri .'/webservice/rest/server.php?wstoken=' .
            self::$token . '&wsfunction=' . $function . '&moodlewsrestformat=json');

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curl_response = curl_exec($curl);
        curl_close($curl);

        return \studip_utf8decode(json_decode($curl_response, true));
    }


    public static function post($function, $data)
    {
        $curl = curl_init(self::$uri .'/webservice/rest/server.php?wstoken=' .
            self::$token . '&wsfunction=' . $function . '&moodlewsrestformat=json');

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, \studip_utf8encode(http_build_query($data)));

        $curl_response = curl_exec($curl);
        curl_close($curl);

        return \studip_utf8decode(json_decode($curl_response, true));
    }

    /**
     * Generate a cryptographically secure password with at least one of each of
     * uppercase/lowercase letters, numbers and symbols.
     *
     * @return string  the generated password
     */
    public static function createPassword()
    {
        do {
            $bytes = openssl_random_pseudo_bytes(32, $strong);

            if (false !== $bytes && true === $strong) {
                $pw = substr(base64_encode($bytes), 0, 32);
            } else {
                throw new \Exception("Unable to generate secure token from OpenSSL.");
            }

        // make sure that at least one of each of the following chars is present:
        // uppercase letter, lowercase letter, number, symbol
        } while (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/', $pw));

        return $pw;
    }
}

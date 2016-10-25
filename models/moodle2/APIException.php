<?php
namespace Moodle2;

class APIException extends \Exception
{
        public function __construct($response, $api_route)
        {
            parent::__construct($response['message'] . ' (' . $response['exception']
                . ', '. $response['errorcode'].') while trying to call: ' . $api_route);
        }
}

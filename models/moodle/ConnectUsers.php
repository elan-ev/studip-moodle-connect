<?php

namespace Moodle;

class ConnectUsers extends \SimpleORMap
{
    protected static function configure($config = array())
    {
        $config['db_table'] = 'moodle_connect_users';

        parent::configure($config);
    }
}

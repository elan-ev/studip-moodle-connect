<?php

namespace Moodle;

class ConnectUsers extends \SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'moodle_connect_users';

        parent::configure($config);
    }
}

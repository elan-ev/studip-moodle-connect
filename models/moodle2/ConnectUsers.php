<?php

namespace Moodle2;

class ConnectUsers extends \SimpleORMap
{
    protected static function configure($config = array())
    {
        $config['db_table'] = 'moodle2_connect_users';

        parent::configure($config);
    }
}

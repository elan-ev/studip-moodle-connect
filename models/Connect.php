<?php

namespace Moodle;

class Connect extends \SimpleORMap
{
    protected static function configure($config = array())
    {
        $config['db_table'] = 'moodle_connect';

        parent::configure($config);
    }
}

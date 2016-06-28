<?php

namespace Moodle;

class ConnectCourses extends \SimpleORMap
{
    protected static function configure($config = array())
    {
        $config['db_table'] = 'moodle_connect_courses';

        parent::configure($config);
    }
}

<?php

namespace Moodle;

class ConnectCourses extends \SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'moodle_connect_courses';

        parent::configure($config);
    }
}

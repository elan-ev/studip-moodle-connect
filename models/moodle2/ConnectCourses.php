<?php

namespace Moodle2;

class ConnectCourses extends \SimpleORMap
{
    protected static function configure($config = array())
    {
        $config['db_table'] = 'moodle2_connect_courses';

        parent::configure($config);
    }
}

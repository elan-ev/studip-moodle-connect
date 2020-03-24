<?php
require_once __DIR__ . '/bootstrap.php';

/**
 * MoodleConnect.class.php
 *
 * ...
 *
 * @author  Till Glöggler <tgloeggl@uos.de>
 * @version 0.1a
 */

class MoodleConnect extends StudIPPlugin implements StandardPlugin
{

    public function getTabNavigation($course_id)
    {
        return [
            'moodle' => new Navigation(
                'Moodle',
                PluginEngine::getURL($this, [], 'index/index')
            )
        ];
    }

    public function getNotificationObjects($course_id, $since, $user_id)
    {
        return [];
    }

    public function getIconNavigation($course_id, $last_visit, $user_id)
    {
        return null;
    }

    public function getInfoTemplate($course_id)
    {
        return null;
    }
}

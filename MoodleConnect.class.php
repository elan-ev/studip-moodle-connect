<?php
require 'bootstrap.php';

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
        return array(
            'moodle' => new Navigation(
                'Moodle',
                PluginEngine::getURL($this, array(), 'index')
            )
        );
    }

    public function getNotificationObjects($course_id, $since, $user_id)
    {
        return array();
    }

    public function getIconNavigation($course_id, $last_visit, $user_id)
    {
        return null;
    }

    public function getInfoTemplate($course_id)
    {
        return null;
    }

    public function perform($unconsumed_path)
    {
        $this->setupAutoload();
        $dispatcher = new Trails_Dispatcher(
            $this->getPluginPath(),
            rtrim(PluginEngine::getLink($this, array(), null), '/'),
            'show'
        );
        $dispatcher->plugin = $this;
        $dispatcher->dispatch($unconsumed_path);
    }

    private function setupAutoload()
    {
        if (class_exists('StudipAutoloader')) {
            StudipAutoloader::addAutoloadPath(__DIR__ . '/models');
        } else {
            spl_autoload_register(function ($class) {
                include_once __DIR__ . $class . '.php';
            });
        }
    }
}

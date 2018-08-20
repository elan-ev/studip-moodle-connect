<?php
require 'bootstrap.php';

/**
 * MoodleConnectZwei.class.php
 *
 * ...
 *
 * @author  Till Gl�ggler <tgloeggl@uos.de>
 * @version 0.1a
 */

class MoodleConnectZwei extends StudIPPlugin implements StandardPlugin
{

    public function __construct()
    {
        parent::__construct();
    }

    public function initialize ()
    {
        PageLayout::addStylesheet($this->getPluginURL().'/assets/style.css');
        PageLayout::addScript($this->getPluginURL().'/assets/application.js');
    }

    public function getTabNavigation($course_id)
    {
        return array(
            'moodle2' => new Navigation(
                'Moodle2',
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
        // ...
    }

    public function getInfoTemplate($course_id)
    {
        // ...
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

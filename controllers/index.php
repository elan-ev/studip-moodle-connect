<?php
class IndexController extends StudipController {

    public function __construct($dispatcher)
    {
        parent::__construct($dispatcher);
        $this->plugin = $dispatcher->plugin;
        Navigation::activateItem('course/moodle');
    }

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        Moodle\REST::setServiceURI(Config::getInstance()->MOODLE_API_URI);
        Moodle\REST::setToken(Config::getInstance()->MOODLE_API_TOKEN);

        $this->course_id = Request::get('cid');

        // $this->set_layout($GLOBALS['template_factory']->open('layouts/base_without_infobox.php'));
        // PageLayout::setTitle('');
    }

    public function index_action()
    {
        SimpleORMap::expireTableScheme();
        $this->moodle = Moodle\Connect::findByRange_Id($this->course_id);

        $this->moodle_courses = Moodle\REST::get();
    }

    public function goto_action($id)
    {

    }

    public function create_action()
    {
        Moodle\REST::post();
    }

    // customized #url_for for plugins
    function url_for($to)
    {
        $args = func_get_args();

        # find params
        $params = array();
        if (is_array(end($args))) {
            $params = array_pop($args);
        }

        # urlencode all but the first argument
        $args = array_map('urlencode', $args);
        $args[0] = $to;

        return PluginEngine::getURL($this->dispatcher->plugin, $params, join('/', $args));
    }
}

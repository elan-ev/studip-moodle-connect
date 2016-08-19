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

        $this->moodle_uri = Config::getInstance()->MOODLE_API_URI;

        if ($this->moodle_uri) {
            Moodle\REST::setServiceURI($this->moodle_uri);
            Moodle\REST::setToken(Config::getInstance()->MOODLE_API_TOKEN);
        }

        $this->course_id       = Request::get('cid');
        $this->course          = Course::find($this->course_id);
        $this->user            = $GLOBALS['user'];
        $this->elevated_rights = $GLOBALS['perm']->have_studip_perm('tutor', $this->course_id);

        PageLayout::setTitle($this->course->getFullname()." - " ._("Moodle"));

        // $this->set_layout('layouts/base');
        $this->set_layout($GLOBALS['template_factory']->open('layouts/base'));
    }

    public function index_action()
    {
        SimpleORMap::expireTableScheme();
        $this->moodle = array_pop(Moodle\ConnectCourses::findByCourse_Id($this->course_id));


        $this->unconfigured = false;

        try {
            if ($this->moodle) {
                $this->connected_course = array_pop(Moodle\REST::post('core_course_get_courses', array(
                    options => array(
                        'ids' => array(
                            $this->moodle->moodle_id
                        )
                    )
                )));

                // create user account and add user too moodle-course (if necessary)
                try {
                    $this->moodle_user = Moodle\Helper::checkPrerequisites($this->user, $this->course_id);
                } catch (Moodle\APIException $e) {
                    PageLayout::postMessage(MessageBox::error(dgettext(
                        'moodle_connect',
                        'Fehler beim prüfen der Voraussetzungen zur Weiterleitung nach Moodle'
                    ) .' ('. $e->getMessage() .')'));
                } catch (Moodle\UnconfiguredException $e) {

                }
            } else if ($this->elevated_rights) {
                $this->moodle_courses = Moodle\Helper::getCoursesForUser($this->user->email);
            }

        } catch (Moodle\UnconfiguredException $e) {
            PageLayout::postMessage(MessageBox::error(
                _('Die Moodle-Schnittstelle wurde noch nicht konfiguriert! '
                    . 'Wenden Sie sich bitte an einen Systemadministrator.')
            ));

            $this->unconfigured = true;
        }
    }

    public function connect_action($moodle_course = null)
    {
        CSRFProtection::verifySecurityToken();

        if (!$this->elevated_rights) {
            throw new AccessDeniedException();
        }

        if (!Request::option('moodle_course') && !$moodle_course) {
            throw new InvalidArgumentException('No course id given while trying to connect to moodle course');
        }

        $connect = new Moodle\ConnectCourses();
        $connect->course_id = $this->course_id;
        $connect->moodle_id = Request::option('moodle_course', $moodle_course);

        $connect->store();

        PageLayout::postMessage(MessageBox::success(
            _('Diese Veranstaltung ist nun mit einem Moodle-Kurs verknüpft!')
        ));

        $this->redirect('index');
    }

    public function disconnect_action($moodle_id)
    {
        CSRFProtection::verifySecurityToken();

        if (!$this->elevated_rights) {
            throw new AccessDeniedException();
        }

        if (!$moodle_id) {
            throw new InvalidArgumentException('No course id given while trying to disconnect to moodle course');
        }

        if ($connect = array_pop(Moodle\ConnectCourses::findByCourse_id($this->course_id))) {
            if ($connect->moodle_id == $moodle_id) {
                $connect->delete();
            }
        }

        // TODO: remove participants of current course from moodle_course (vs. remove all participants?!?)

        PageLayout::postMessage(MessageBox::success(
            _('Diese Veranstaltung ist nun mit keinem Moodle-Kurs mehr verknüpft.')
        ));

        $this->redirect('index');
    }

    public function create_action()
    {
        if (!$this->elevated_rights) {
            throw new AccessDeniedException();
        }

        $moodle = array_pop(Moodle\ConnectCourses::findByCourse_Id($this->course_id));

        if (!$moodle) {
            // TODO: for the time being, use the first available category
            $category = reset(Moodle\REST::get('core_course_get_categories'));

            $data = array('courses' => array(
                array(
                    'fullname' => studip_utf8encode($this->course->getFullname('number-name-semester')),
                    'shortname' => md5(uniqid()),
                    'categoryid' => $category['id']
                )
            ));

            $response = Moodle\REST::post('core_course_create_courses', $data);
            $moodle_course = array_pop($response);


            PageLayout::postMessage(MessageBox::success(
                _('Es wurde ein neuer Kurs in Moodle angelegt.')
            ));

            $this->redirect('index/connect/' . $moodle_course['id']);

            return;
        } else {
            throw new Exception('course is already connected!');
        }

        $this->redirect('index');
    }

    // customized #url_for for plugins
    public function url_for($to)
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

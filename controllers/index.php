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
        $this->course = Course::find($this->course_id);

        PageLayout::setTitle($this->course->getFullname()." - " ._("Moodle"));
    }

    public function index_action()
    {
        SimpleORMap::expireTableScheme();
        $this->moodle = Moodle\Connect::findOneByRange_Id($this->course_id);

        if ($this->moodle) {
            $this->connected_course = array_pop(Moodle\REST::post('core_course_get_courses', array(
                options => array(
                    'ids' => array(
                        $this->moodle->moodle_id
                    )
                )
            )));
        } else {
            $this->moodle_courses = Moodle\REST::get('core_course_get_courses');
        }

        // $this->moodle_courses = Moodle\REST::get('');
    }

    public function connect_action($moodle_course = null)
    {
        CSRFProtection::verifySecurityToken();

        if (!$GLOBALS['perm']->have_studip_perm($this->course_id, 'tutor')) {
            throw new AccessDeniedException();
        }

        if (!Request::option('moodle_course') && !$moodle_course) {
            throw new InvalidArgumentException('No course id given while trying to connect to moodle course');
        }

        $connect = new Moodle\Connect();
        $connect->type = 'course';
        $connect->range_id = $this->course_id;
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

        if (!$GLOBALS['perm']->have_studip_perm($this->course_id, 'tutor')) {
            throw new AccessDeniedException();
        }

        if (!$moodle_id) {
            throw new InvalidArgumentException('No course id given while trying to disconnect to moodle course');
        }

        if ($connect = Moodle\Connect::findOneByRange_id($this->course_id)) {
            if ($connect->moodle_id == $moodle_id) {
                $connect->delete();
            }
        }

        PageLayout::postMessage(MessageBox::success(
            _('Diese Veranstaltung ist nun mit keinem Moodle-Kurs mehr verknüpft.')
        ));

        $this->redirect('index');
    }

    public function goto_action()
    {
        if (!$GLOBALS['perm']->have_studip_perm($this->course_id, 'autor')) {
            throw new AccessDeniedException();
        }

        // get connected course
        $connected_course = Moodle\Connect::findOneByRange_id($this->course_id);

        if ($connected_course) {
            // check if the current user already exists in Moodle
            $users = Moodle\REST::post('core_user_get_users', array(
                'criteria' => array(
                    array ('key' => 'username', 'value' => $GLOBALS['user']->username)
                )
            ));

            // if user does not exists in moodle, create it
            if (empty($users['users'])) {
                $data = array('users' => array(
                    array(
                        'username'  => $GLOBALS['user']->username,
                        'password'  => 'W45wef6ew5#',
                        'firstname' => $GLOBALS['user']->vorname,
                        'lastname'  => $GLOBALS['user']->nachname,
                        'email'     => $GLOBALS['user']->email
                    )
                ));

                $response = Moodle\REST::post('core_user_create_users', $data);
            }

            // redirect to moodle
            // $connected_course->moodle_id
        }

        var_dump($users);
        var_dump($data, $response);die;
    }

    public function create_action()
    {
        $moodle = Moodle\Connect::findOneByRange_Id($this->course_id);

        if (!$moodle) {
            $data = array('courses' => array(
                array(
                    'fullname' => $this->course->veranstaltungsnummer .': '. $this->course->name,
                    'shortname' => md5(uniqid()),
                    'categoryid' => 1
                )
            ));

            $response = Moodle\REST::post('core_course_create_courses', $data);

            if ($response['exception']) {
                PageLayout::postMessage(MessageBox::error(
                    _('Fehler beim anlegen des Kurses!'),
                    $response
                ));

            } else {
                $moodle_course = array_pop($response);

                PageLayout::postMessage(MessageBox::success(
                    _('Es wurde ein neuer Kurs in Moodle angelegt.')
                ));

                $this->redirect('index/connect/' . $moodle_course['id']);

                return;
            }
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

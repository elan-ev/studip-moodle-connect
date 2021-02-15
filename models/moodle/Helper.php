<?php

namespace Moodle;

class Helper
{
    /**
     * Get moodle user-data for passed username
     *
     * @param string $username
     *
     * @return mixed  moodle user-data or false, if user does not exist in moodle
     */
    public static function getUser($username)
    {
        $users = REST::post('core_user_get_users', [
            'criteria' => [
                ['key' => 'username', 'value' => mb_strtolower($username)]
            ]
        ]);

        if (empty($users['users'])) {
            return false;
        }

        return $users['users'][0];
    }

    /**
     * Get moodle user-data for passed mail address
     *
     * @param string $mail
     *
     * @return mixed  moodle user-data or false, if user does not exist in moodle
     */
    public static function getUserByMail($mail)
    {
        $users = REST::post('core_user_get_users', [
            'criteria' => [
                ['key' => 'email', 'value' => $mail]
            ]
        ]);

        if (empty($users['users'])) {
            return false;
        }

        return $users['users'][0];
    }


    public static function getUsernameByMail($mail)
    {
        return array_pop(ConnectUsers::findbyEmail($mail))->moodle_username;

    }

    /**
     * Return to user-data for the passed user in the passed course.
     * Returns false, if the user is not enroled in the passed course
     *
     * @param string $username username for the user to check
     * @param int $course_id course-id of moodle-course
     *
     * @return mixed  array of user-data or false, if user is not in passed course
     */
    public static function getUserInCourse($username, $course_id)
    {
        $response = REST::post('core_enrol_get_enrolled_users', [
            'courseid' => $course_id
        ]);

        $user_in_course = false;
        foreach ($response as $m_user) {
            if ($m_user['username'] == mb_strtolower($username)) {
                $user_in_course = $m_user;
                break;
            }
        }

        return $user_in_course;
    }

    /**
     * Enrole the passed user in the passed course with the passed role
     *
     * @param int $user_id user-id of moodle-user
     * @param int $course_id course-id of moodle-course
     * @param string $role moodle role-shortname
     *
     * @return void
     */
    public static function enroleUserInCourse($user_id, $course_id, $role)
    {
        $role_id = self::getIdForRole($role);

        $response = REST::post('enrol_manual_enrol_users', ['enrolments' => [
            [
                'roleid'   => $role_id,
                'userid'   => $user_id,
                'courseid' => (int)$course_id
            ]
        ]]);
    }

    /**
     * Return the moodle-equivalent for the perms the user has in the passed
     * Stud.IP course
     *
     * @param string $user_id the id of the Stud.IP user
     * @param string $course_id the id of the Stud.IP course
     *
     * @return string  the moodle shortname for the equivalent role
     *
     * @throws Exception
     */
    public static function getTranslatedRole($user_id, $course_id)
    {
        global $perm;

        $role = false;
        switch ($perm->get_studip_perm($course_id, $user_id)) {
            case 'user':
            case 'autor':
                $role = 'student';
                break;
            case 'tutor':
            case 'dozent':
                $role = 'editingteacher';
                break;
        }

        if (!$role) {
            throw new \Exception('Could not determine moodle equivalent for Stud.IP-role: ' . $GLOBALS['perm']->get_studip_perm($course_id));
        }

        return $role;
    }

    /**
     * Translate the shortname of the moodle role into a role-id
     *
     * @param string $rolename moodle shortname of role
     *
     * @return int  the internal id for the role in moodle
     */
    private static function getIdForRole($rolename)
    {
        switch ($rolename) {
            case 'editingteacher':
                return 3;
                break;
            case 'student'       :
                return 5;
                break;
        }
    }

    /**
     * Generate a cryptographically secure password with at least one of each of
     * uppercase/lowercase letters, numbers and symbols.
     *
     * @return string  the generated password
     */
    public static function createPassword()
    {
        do {
            $bytes = openssl_random_pseudo_bytes(32, $strong);

            if (false !== $bytes && true === $strong) {
                $pw = mb_substr(base64_encode($bytes), 0, 20);
            } else {
                throw new \Exception("Unable to generate secure token from OpenSSL.");
            }

            // make sure that at least one of each of the following chars is present:
            // uppercase letter, lowercase letter, number, symbol
        } while (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/', $pw));

        return $pw;
    }

    public static function checkPrerequisites($studip_user, $course_id)
    {
        if ($GLOBALS['perm']->have_perm('admin', $studip_user->user_id)) {
            return false;
        }

        // get connected course
        $connected_course = array_pop(ConnectCourses::findByCourse_id($course_id));

        if ($connected_course) {

            // check if the current user already exists in Moodle and create it if necessary
            //if (!$moodle_user = self::getUser(strtolower($studip_user->email))) {
            if (!$moodle_user = self::getUser(self::getUsernameByMail($studip_user->email))) {

                // check if a user with the currents user e-mail-address already exists
                if (self::getUserByMail($studip_user->email)) {
                    // TODO: messages should not be posted here
                    \PageLayout::postError(sprintf(
                        _('Es gibt bereits einen Nutzer in Moodle mit dieser Mail-Adresse! (%s)'),
                        $studip_user->email
                    ));
                    return false;
                }

                $pw = self::createPassword();

                $data = ['users' => [
                    [
                        'username'  => mb_strtolower($studip_user->email),
                        'password'  => $pw,
                        'firstname' => $studip_user->vorname,
                        'lastname'  => $studip_user->nachname,
                        'email'     => $studip_user->email
                    ]
                ]];

                REST::post('core_user_create_users', $data);

                // create entry in moodle_connect_users
                if (!$connected_user = array_pop(ConnectUsers::findbyEmail($studip_user->email))) {
                    $connected_user                  = new ConnectUsers();
                    $connected_user->moodle_username = $studip_user->email;
                    $connected_user->email           = $studip_user->email;
                }
                $connected_user->moodle_password = $pw;
                $connected_user->store();

                $moodle_user = self::getUser(mb_strtolower($studip_user->email));

            } else {
                // load users credentials from DB
                $connected_user = array_pop(ConnectUsers::findByEmail($studip_user->email));

                if (!$connected_user) {
                    \PageLayout::postError(sprintf(
                        _('Es gibt bereits einen Nutzer in Moodle mit dieser Mail-Adresse, aber es konnte kein zugehöriges Passwort gefunden werden! (%s)'),
                        $studip_user->email
                    ));
                }
            }

            $course_role = self::getTranslatedRole($studip_user->id, $course_id);

            // check if user is already enroled in moodle-course
            $course_user = self::getUserInCourse(self::getUsernameByMail($studip_user->email), $connected_course->moodle_id);

            // enrole user for moodle-course, if necessary
            if (!$course_user) {
                self::enroleUserInCourse($moodle_user['id'], $connected_course->moodle_id, $course_role);
                $course_user = self::getUserInCourse(self::getUsernameByMail($studip_user->email), $connected_course->moodle_id);

                if (!$course_user) {
                    throw new \Exception('Could not enrole user in moodle-course!');
                }
            } else {
                // user is in course, check if he has the correct role in the course
                $user_has_role = false;
                foreach ($course_user['roles'] as $m_role) {
                    if ($m_role['shortname'] == $course_role) {
                        $user_has_role = $course_role;
                        break;
                    }
                }

                // if the user has not the correct role, assign it
                if (!$user_has_role) {
                    self::enroleUserInCourse($moodle_user['id'], $course_id, $course_role);
                }
            }
        }
        return $connected_user ?: array_pop(ConnectUsers::findByEmail($studip_user->email));
    }

    /**
     * Return courses the passed user is enroled for as editingteacher
     *
     * @param string $username
     *
     * @return array  moodle-courses
     */
    public static function getCoursesForUser($username)
    {
        $moodle_user = self::getUser($username);

        $role_id = self::getIdForRole('editingteacher');
        $courses = [];

        if ($moodle_user) {
            foreach (REST::post('core_enrol_get_users_courses', [
                'userid' => $moodle_user['id']
            ]) as $course) {
                $user_course = self::getUserInCourse($username, $course['id']);

                foreach ($user_course['roles'] as $role) {
                    if ($role['roleid'] == $role_id) {
                        $courses[] = $course;
                    }
                }
            }
        }

        return $courses;
    }
}

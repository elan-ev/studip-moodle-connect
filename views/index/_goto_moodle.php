<form method="post" action="<?= $moodle_uri ?>/login/index.php?course_id=<?= $moodle->moodle_id ?>" target="_blank">
    <input type="hidden" name="username" value="<?= $user->username ?>">
    <input type="hidden" name="password" value="<?= $moodle_user->moodle_password ?>">
    <?= \Studip\Button::create(dgettext('moodle_connect', 'Zum Kurs in Moodle')) ?>
</form>

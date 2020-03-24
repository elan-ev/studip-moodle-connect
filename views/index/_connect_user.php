<h2><?= dgettext('moodle_connect', 'Mit vorhandenem Nutzer in Moodle verknüpfen') ?></h2>
<form method="post" action="<?= $controller->url_for('index/connect_user/') ?>" class="default">
    <?= CSRFProtection::tokenTag() ?>
    <label>
        <?= dgettext('moodle_connect', 'Moodle-User der verknüpft werden soll:') ?><br>
        <input name="moodle_username">
    </label>
    <label>

        <?= dgettext('moodle_connect', 'Moodle-Passwort des Moodle-Users der verknüpft werden soll:') ?>
        <input name="moodle_user_pw">
    </label>
    <?= \Studip\Button::create(_('Mit Nutzer verknüpfen')) ?>
</form>

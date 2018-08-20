<h2>Mit vorhandenem Nutzer in Moodle verknüpfen</h2>
<form method="post" action="<?= $controller->url_for('index/connect_user/') ?>">
    <?= CSRFProtection::tokenTag() ?>
    Moodle-User der verknüpft werden soll:<br> 
    <input name="moodle_username">    
    </input><br>
    Moodle-Passwort des Moodle-Users der verknüpft werden soll:<br>
    <input name="moodle_user_pw">
    </input><br>
    <?= \Studip\Button::create(_('Mit Nutzer verknüpfen')) ?>
</form>

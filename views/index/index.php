<?
$info = new ListWidget();
$info->setTitle(_('Informationen'));
$info->addElement(new WidgetElement($moodle
    ? _('Veranstaltung ist mit Kurs in Moodle verknüpft.')
    : _('Veranstaltung ist bisher mit keinem Kurs in Moodle verknüpft.')
));

Sidebar::get()->addWidget($info);
?>

<? if ($moodle) : ?>
    <!-- Zum Kurs in Moodle (new tab) -->
    <?= \Studip\LinkButton::create(_('Zum Kurs in Moodle'), $controller->url_for('index/goto/'. $moodle->moodle_id)) ?>
<? else : ?>
    <!-- Kurse in Moodle erstellen -->
    <? if (!empty($moodle_courses)) : ?>
    <form method="post" action="<?= $controller->url_for('index/connect/') ?>">
        <select name="moodle_course">
            <? foreach($moodle_courses as $course) : ?>
            <option value="<?= $course['id'] ?>"><?= $course['name'] ?></option>
            <? endforeach ?>
        </select>
        <?= \Studip\Button::create(_('Mit Moodlekurs verknüpfen')) ?>
    </form>
    <? endif ?>

    <?= \Studip\LinkButton::create(_('Neuen Kurs in Moodle erstellen'), $controller->url_for('index/connect/')) ?>
<? endif ?>

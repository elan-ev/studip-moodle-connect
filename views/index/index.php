<?
/*
$info = new ListWidget();
$info->setTitle(_('Informationen'));
$info->addElement(new InfoboxElement($connected_course
    ? sprintf(dgettext('moodle_connect', 'Veranstaltung ist mit dem Kurs "%s" in Moodle verknüpft.'), $connected_course['fullname'])
    : dgettext('moodle_connect', 'Veranstaltung ist bisher mit keinem Kurs in Moodle verknüpft.'),
    (class_exists('Icon') ? Icon::create('exclaim', 'info') : 'icons/16/black/exclaim')
));

Sidebar::get()->addWidget($info);
*/

if ($connected_course && $elevated_rights && !$unconfigured) {
    $actions = new ActionsWidget();
    $actions->setTitle(_('Aktionen'));

    $actions->addLink(
        dgettext('moodle_connect', 'Kursverknüpfung aufheben'),
        $controller->url_for('index/disconnect/' . $moodle->moodle_id),
        Icon::create('link-intern')
    );

    Sidebar::get()->addWidget($actions);
}

if ($connected_course) : ?>
    <?= MessageBox::info(sprintf(dgettext('moodle_connect', 'Veranstaltung ist mit dem Kurs "%s" in Moodle verknüpft.'), $connected_course['fullname'])) ?>
<? else : ?>
    <?= MessageBox::warning(dgettext('moodle_connect', 'Veranstaltung ist bisher mit keinem Kurs in Moodle verknüpft.')) ?>
<? endif ?>

<? if (!$moodle_user) : ?>
 <?= $this->render_partial('index/_connect_user') ?>
<? endif ?>


<? if (!$unconfigured) : ?>
    <? if ($connected_course) : ?>
        <!-- Zum Kurs in Moodle (new tab) -->
        <? if ($GLOBALS['perm']->have_perm('admin')) : ?>
            <?= MessageBox::info(dgettext('moodle_connect', 'Root und Admins dürfen nicht direkt zu Moodle wechseln!')) ?>
        <? else : ?>
            <? if ($moodle_user) : ?>
                <?= $this->render_partial('index/_goto_moodle') ?>
            <? endif ?>
        <? endif ?>
    <? elseif ($elevated_rights) : ?>
        <!-- Kurse in Moodle erstellen -->
        <? if (!empty($moodle_courses)) : ?>
            <?= $this->render_partial('index/_connect_course') ?>
        <? endif ?>

        <h2>Kurs in Moodle anlegen</h2>
        <?= \Studip\LinkButton::create(dgettext('moodle_connect', 'Neuen Kurs in Moodle erstellen'), $controller->url_for('index/create')) ?>
    <? else : ?>
        <?= MessageBox::info(dgettext('moodle_connect', 'Es wurde noch kein Moodle-Kurs mit dieser Veranstaltung verknüpft.')) ?>
    <? endif ?>
<? endif ?>

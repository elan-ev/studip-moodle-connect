<?
$info = new ListWidget();
$info->setTitle(_('Informationen'));
$info->addElement(new InfoboxElement($connected_course
    ? sprintf(_('Veranstaltung ist mit dem Kurs "%s" in Moodle verknüpft.'), $connected_course['fullname'])
    : _('Veranstaltung ist bisher mit keinem Kurs in Moodle verknüpft.'),
    Icon::create('exclaim', 'info')
));

Sidebar::get()->addWidget($info);

if ($connected_course) {
    $actions = new ActionsWidget();
    $actions->setTitle(_('Aktionen'));

    $actions->addLink(
        'Kursverküpfung aufheben',
        $controller->url_for('index/disconnect/' . $moodle->moodle_id),
        Icon::create('link-intern', 'clickable')
    );

    Sidebar::get()->addWidget($actions);
}

?>

<? if ($connected_course) : ?>
    <!-- Zum Kurs in Moodle (new tab) -->
    <?= \Studip\LinkButton::create(_('Zum Kurs in Moodle'), $controller->url_for('index/goto')) ?>
<? elseif ($GLOBALS['perm']->have_studip_perm($this->course_id, 'tutor')) : ?>
    <!-- Kurse in Moodle erstellen -->
    <? if (!empty($moodle_courses)) : ?>
        <?= $this->render_partial('index/_connect_course') ?>
    <? endif ?>

    <h2>Kurs in Moodle anlegen</h2>
    <?= \Studip\LinkButton::create(_('Neuen Kurs in Moodle erstellen'), $controller->url_for('index/create')) ?>
<? else : ?>
    <?= MessageBox::info(_('Es wurde noch kein Moodle-Kurs mit dieser Veranstaltung verknüpft.')) ?>
<? endif ?>

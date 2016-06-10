<? if ($is_connected) :
    $this->render_partial('_goto_moodle.php');
else:
    $this->render_partial('_connect_course.php');
endif;

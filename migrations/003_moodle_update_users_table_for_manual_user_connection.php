<?php

class MoodleUpdateUsersTableForManualUserConnection extends Migration
{
    public function description()
    {
        return 'Update table for connected users for manual user connection';
    }

    public function up()
    {
        $db = DBManager::get();

        // update db-table
        $db->exec("ALTER TABLE `moodle_connect_users`
            ADD `moodle_username` varchar(255) AFTER `email`");

        SimpleORMap::expireTableScheme();
    }

    public function down()
    {
    }
}

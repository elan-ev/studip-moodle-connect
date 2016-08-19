<?php

class MoodleUpdateUsersTable extends Migration
{
    public function description()
    {
        return 'Update table for connected users';
    }

    public function up()
    {
        $db = DBManager::get();

        // update db-table
        $db->exec("ALTER TABLE `moodle_connect_users`
            CHANGE `user_id` `email` varchar(255) NOT NULL AFTER `id`");

        $db->exec("UPDATE moodle_connect_users mcu
            LEFT JOIN auth_user_md5 au ON(au.user_id = mcu.email)
            SET mcu.email = au.Email");

        SimpleORMap::expireTableScheme();
    }

    public function down()
    {
    }
}

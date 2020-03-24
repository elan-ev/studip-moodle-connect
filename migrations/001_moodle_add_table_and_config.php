<?php

class MoodleAddTableAndConfig extends Migration
{
    public function description()
    {
        return 'Add config entry for "MOODLE_API_URI" and DB table for MoodleConnect';
    }

    public function up()
    {
        $db = DBManager::get();

        // add config-entry
        $query     = "INSERT IGNORE INTO `config` (
                    `field`, `value`, `type`, `range`, `section`,
                    `mkdate`, `chdate`, `description`
                  ) VALUES (
                    :field, :value, 'string', 'global', 'moodle',
                    UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :description
                  )";
        $statement = $db->prepare($query);

        $statement->execute([
            ':field'       => 'MOODLE_API_URI',
            ':value'       => '',
            ':description' => 'URL zur Moodle REST API'
        ]);

        $statement->execute([
            ':field'       => 'MOODLE_API_TOKEN',
            ':value'       => '',
            ':description' => 'Token für die Moodle REST API'
        ]);


        // add db-table
        $db->exec("CREATE TABLE IF NOT EXISTS `moodle_connect_courses` (
            `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `course_id` varchar(32) NOT NULL,
            `moodle_id` int NOT NULL
        )");

        $db->exec("ALTER TABLE `moodle_connect_courses`
            ADD UNIQUE `course_id_moodle_id` (`course_id`, `moodle_id`)");


        $db->exec("CREATE TABLE IF NOT EXISTS `moodle_connect_users` (
            `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` varchar(32) NOT NULL,
            `moodle_password` varchar(32) NOT NULL
        )");

        $db->exec("ALTER TABLE `moodle_connect_users`
            ADD UNIQUE `user_id` (`user_id`)");

        SimpleORMap::expireTableScheme();
    }

    public function down()
    {
        $db = DBManager::get();

        $db->exec("DELETE FROM `config` WHERE `field` = 'MOODLE_API_URI'");
        $db->exec("DELETE FROM `config` WHERE `field` = 'MOODLE_API_TOKEN'");

        $db->exec("DROP TABLE moodle_connect_users");
        $db->exec("DROP TABLE moodle_connect_courses");

        SimpleORMap::expireTableScheme();
    }
}

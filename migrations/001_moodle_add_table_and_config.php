<?php

class Moodle2AddTableAndConfig extends Migration
{
    public function description()
    {
        return 'Add config entry for "MOODLE_API_URI" and DB table for MoodleConnect';
    }

    public function up()
    {
        $db = DBManager::get();

        // add config-entry
        $query = "INSERT IGNORE INTO `config` (
                    `config_id`, `parent_id`, `field`, `value`, `is_default`,
                    `type`, `range`, `section`, `mkdate`, `chdate`, `description`
                  ) VALUES (
                    MD5(:field), '', :field, :value, 1, 'string', 'global', 'moodle',
                    UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :description
                  )";
        $statement = $db->prepare($query);

        $statement->execute(array(
            ':field'       => 'MOODLE2_API_URI',
            ':value'       => '',
            ':description' => 'URL zur Moodle2 REST API'
        ));

        $statement->execute(array(
            ':field'       => 'MOODLE2_API_TOKEN',
            ':value'       => '',
            ':description' => 'Token für die Moodle2 REST API'
        ));


        // add db-table
        $db->exec("CREATE TABLE IF NOT EXISTS `moodle2_connect_courses` (
            `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `course_id` varchar(32) NOT NULL,
            `moodle_id` int NOT NULL
        )");

        $db->exec("ALTER TABLE `moodle2_connect_courses`
            ADD UNIQUE `course_id_moodle_id` (`course_id`, `moodle_id`)");


        $db->exec("CREATE TABLE IF NOT EXISTS `moodle2_connect_users` (
            `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` varchar(32) NOT NULL,
            `moodle_password` varchar(32) NOT NULL
        )");

        $db->exec("ALTER TABLE `moodle2_connect_users`
            ADD UNIQUE `user_id` (`user_id`)");

        SimpleORMap::expireTableScheme();
    }

    public function down()
    {
        $db = DBManager::get();

        $db->exec("DELETE FROM `config` WHERE `field` = 'MOODLE2_API_URI'");
        $db->exec("DELETE FROM `config` WHERE `field` = 'MOODLE2_API_TOKEN'");

        $db->exec("DROP TABLE moodle2_connect_users");
        $db->exec("DROP TABLE moodle2_connect_courses");

        SimpleORMap::expireTableScheme();
    }
}

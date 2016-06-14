<?php

class MoodleAddTableAndConfig
{
    public function description()
    {
        return 'Add config entry for "MOODLE_API_URI" and DB table for MoodleConnect';
    }

    public function up()
    {
        $db = DBManager::get();

        // add config-entry
        $query = "INSERT INTO `config` (
                    `config_id`, `parent_id`, `field`, `value`, `is_default`,
                    `type`, `range`, `section`, `mkdate`, `chdate`, `description`
                  ) VALUES (
                    MD5(:field), '', :field, :value, 1, 'array', 'user', '',
                    UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :description
                  )";
        $statement = $db->prepare($query);

        $statement->execute(array(
            ':field'       => 'MOODLE_API_URI',
            ':value'       => '',
            ':description' => 'URL zur Moodle REST API'
        ));

        $statement->execute(array(
            ':field'       => 'MOODLE_API_TOKEN',
            ':value'       => '',
            ':description' => 'Token für die Moodle REST API'
        ));


        // add db-table
        $db->exec("CREATE TABLE `moodle_connect` (
            `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `type` enum('course','user') NOT NULL,
            `range_id` varchar(32) NOT NULL,
            `moodle_id` int NOT NULL
        )");

        SimpleORMap::expireTableScheme();
    }

    public function down()
    {
        $db = DBManager::get();

        $db->exec("DELETE FROM `config` WHERE `field` = 'MOODLE_API_URI'");
        $db->exec("DELETE FROM `config` WHERE `field` = 'MOODLE_API_TOKEN'");

        $db->exec("DROP TABLE moodle_connect");

        SimpleORMap::expireTableScheme();
    }
}

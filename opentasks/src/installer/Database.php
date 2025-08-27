<?php
namespace Hp\Opentasks\Installer;

class Database
{
    public static function installQueries()
    {
        $queries = [];
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'open_tasks` (
        `id` int NOT NULL AUTO_INCREMENT,
        `position` int NOT NULL DEFAULT 0,
        `active` int NOT NULL DEFAULT 1,
        PRIMARY KEY (`id`)
        )ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET= utf8';

        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'open_tasks_lang` (
        `task_id` int NOT NULL AUTO_INCREMENT,
        `lang_id` int NOT NULL,
        `title` VARCHAR(250) NULL,
        `resume` TINYTEXT NULL,
        `description` TEXT NULL,
        PRIMARY KEY (`task_id`,`lang_id`)
        )ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET= utf8';

        return $queries;
    }

    public static function unInstallQueries(){
        $queries = [];

        $queries[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'open_tasks` ';
        $queries[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'open_tasks_lang` ';

        return $queries;

    }
}
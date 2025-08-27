<?php
namespace Hp\Testimonial\Install;

class Database
{
    public static function installQueries()
    {
        $queries = [];
        $queries[] = 'CREATE TABLE IF NOT EXISTS `testomonial` (
        `id` int NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(256) NOT NULL,
        `message` TEXT NOT NULL,
        PRIMARY KEY (`id`)
        )ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET= utf8';
        return $queries;
    }

    public static function unInstallQueries()
    {
        $queries = [];

        $queries[] = 'DROP TABLE IF EXISTS `testomonial` ';

        return $queries;
    }
}

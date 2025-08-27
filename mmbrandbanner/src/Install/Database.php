<?php

namespace Hp\Mmbrandbanner\Install;

class Database
{
    public static function installQueries()
    {
        $queries = [];
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'brand_banner` (
        `id` int NOT NULL AUTO_INCREMENT,
        `id_manufacturer` int NOT NULL,
        `title` VARCHAR(250) NOT NULL,
        `image` VARCHAR(250) NOT NULL,
        `description` TEXT NOT NULL,
        `active` int NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`)
        )ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET= utf8';
        return $queries;
    }

    public static function unInstallQueries(){
        $queries = [];

        $queries[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'brand_banner` ';

        return $queries;

    }
}
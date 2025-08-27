<?php
namespace Hp\Mmreassurances\Install;

class Database
{
    public static function installQueries()
    {
        $queries = [];
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'mm_reassurance` (
        `id` int NOT NULL AUTO_INCREMENT,
        `image` varchar(250) NOT NULL,
        `position` int NOT NULL DEFAULT -1,
        `active` int NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`)
        )ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET= utf8';
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'mm_reassurance_lang` (
        `id_reassurance` int NOT NULL,
        `title` varchar(255) NOT NULL,
        `description` LONGTEXT NOT NULL,
        PRIMARY KEY (`id_reassurance`)
        )ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET= utf8';


        return $queries;
    }

    public static function unInstallQueries(){
        $queries = [];

        $queries[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'mm_reassurance` ';
        $queries[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'mm_reassurance_lang` ';

        return $queries;

    }


}
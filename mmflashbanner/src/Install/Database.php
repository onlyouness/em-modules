<?php

namespace Hp\MmFlashBanner\Install;

class Database
{
    public static function installQueries()
    {
        $queries = [];
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'banners_flash` (
        `id` int NOT NULL AUTO_INCREMENT,
        `category_id` int NOT NULL,
         `title` VARCHAR(250) NOT NULL,
         `image` VARCHAR(250) NOT NULL,
        `description` TEXT NOT NULL,
        `active` int NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`)
        )ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET= utf8';
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'banners_flash_section` (
        `id` int NOT NULL AUTO_INCREMENT,
         `title` VARCHAR(250) NOT NULL,
        `description` TEXT NOT NULL,
        `short_description` TEXT NOT NULL,
        PRIMARY KEY (`id`)
        )ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET= utf8';
        return $queries;
    }

    public static function unInstallQueries(){
        $queries = [];

        $queries[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'banners_flash` ';
        $queries[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'banners_flash_section` ';

        return $queries;

    }
}
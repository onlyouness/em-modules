<?php
/**
 * 2007-2023 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2023 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'b2bregistration` (
    `id_b2bregistration` int(11) NOT NULL AUTO_INCREMENT,
    `id_customer` int(11) NOT NULL,
    `id_b2b_profile` INT(11) NOT NULL,
    `middle_name` varchar(255),
    `name_suffix` varchar(255),
    `flag` tinyint(1) default \'0\',
    `active` tinyint(1) default \'0\',
    PRIMARY KEY  (`id_b2bregistration`, `id_customer`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'b2b_profile` (
    `id_b2b_profile`                    INT(11) NOT NULL AUTO_INCREMENT,
    `b2b_profile_group`                 INT(11) NOT NULL,
    `b2b_tos_page`                      INT(11) NOT NULL,
    `b2b_link_rewrite`                  VARCHAR(256),
    `b2b_redirect_url`                  VARCHAR(256),
    `groupBox`                          TEXT,
    `b2b_name_prefix`                   TEXT,
    `b2b_name_suffix`                   TEXT,
    `b2b_profile_link`                  TINYINT(2) NOT Null Default 0,
    `b2b_customer_enable_group`         TINYINT(2) NOT Null Default 0,
    `b2b_profile_dob`                   TINYINT(2) NOT Null Default 0,
    `b2b_profile_siret`                 TINYINT(2) NOT Null Default 0,
    `b2b_website`                       TINYINT(2) NOT Null Default 0,
    `b2b_address`                       TINYINT(2) NOT Null Default 0,
    `active`                            TINYINT(2) NOT Null Default 0,
    `b2b_customer_auto_approval`        TINYINT(2) NOT Null Default 0,
    `b2b_redrection`                    TINYINT(2) NOT Null Default 0,
    `b2b_custom_fields`                 TINYINT(2) NOT Null Default 0,
    `b2b_name_prefix_active`            TINYINT(2) NOT Null Default 0,
    `b2b_name_suffix_active`            TINYINT(2) NOT Null Default 0,
    `b2b_middle_name_active`            TINYINT(2) NOT Null Default 0,
    `b2b_dob_active`                    TINYINT(2) NOT Null Default 0,
    `b2b_siret_active`                  TINYINT(2) NOT Null Default 0,
    `partner_option`                    TINYINT(2) NOT Null Default 0,
    `newsletter`                        TINYINT(2) NOT Null Default 0,
    PRIMARY KEY                         (`id_b2b_profile`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'b2b_profile_shop(
    `id_b2b_profile`                  int(11) NOT NULL,
    `id_shop`                         int(11) NOT NULL,
    PRIMARY KEY                       (`id_b2b_profile`,`id_shop`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'b2b_profile_lang` (
    `id_b2b_profile`                    INT(11) NOT NULL,
    `id_lang`                           INT(11) NOT NULL,
    `b2b_profile_name`                  VARCHAR(128),
    `b2b_profile_link_text`             VARCHAR(128),
    `b2b_personal_info_heading`         VARCHAR(256),
    `b2b_company_info_heading`          VARCHAR(256),
    `b2b_signin_heading`                VARCHAR(128),
    `b2b_address_heading`               VARCHAR(256),
    `b2b_customfields_heading`          VARCHAR(256),
    `b2b_account_msg`                   TEXT,
    PRIMARY KEY                         (`id_b2b_profile`, `id_lang`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'bb_registration_fields(
    `id_bb_registration_fields`       INT(11) unsigned NOT NULL auto_increment,
    `field_type`            enum(
        \'text\',
        \'textarea\',
        \'date\',
        \'boolean\',
        \'multiselect\',
        \'select\',
        \'checkbox\',
        \'radio\',
        \'message\',
        \'image\',
        \'attachment\'
        ) default \'text\',
    `field_validation`              VARCHAR(255) default NULL,
    `position`                      TINYINT(4) default 0,
    `assoc_shops`                   VARCHAR(255) default ' . (int) Context::getContext()->shop->id . ',
    `value_required`                TINYINT(1) default NULL,
    `editable`                      TINYINT(1) default 1,
    `extensions`                    VARCHAR(128) DEFAULT \'jpg\',
    `attachment_size`               DECIMAL(10,2) NOT NULL DEFAULT \'2.0\',
    `alert_type`                    VARCHAR(30) default NULL,
    `show_customer`                 TINYINT(1) default NULL,
    `show_email`                    TINYINT(1) default NULL,
    `show_admin`                    TINYINT(1) default NULL,
    `active`                        TINYINT(1) default NULL,
    `dependant`                     TINYINT(1) default \'0\',
    `dependant_field`               INT(11) default \'0\',
    `dependant_value`               INT(11) default \'0\',
    `limit`                         INT(11) default \'0\',
    `id_b2b_profile`                INT(11) default \'0\',
    `created_time`                  datetime default NULL,
    `update_time`                   datetime default NULL,
    PRIMARY KEY                     (`id_bb_registration_fields`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

// Table bb_registration_fields_lang
$sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'bb_registration_fields_lang(
        `id_bb_registration_fields`      int(11) NOT NULL auto_increment,
        `id_lang`                        int(11) NOT NULL,
        `field_name`                     varchar(255) default NULL,
        `default_value`                  varchar(255) default NULL,
        PRIMARY KEY                      (`id_bb_registration_fields`,`id_lang`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

// Table bb_registration_fields_values
$sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'bb_registration_fields_values(
        `field_value_id`                int(11) NOT NULL auto_increment,
        `id_bb_registration_fields`     int(11) NOT NULL,
        PRIMARY KEY                     (`field_value_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8';

// Table bb_registration_option_values_lang
$sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'bb_registration_fields_values_lang(
        `field_value_id`                int(11) NOT NULL,
        `id_lang`                       int(11) NOT NULL DEFAULT ' . (int) Configuration::get('PS_LANG_DEFAULT') . ',
        `field_value`                   text,
        PRIMARY KEY                     (`field_value_id`, `id_lang`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8';

// Table bb_registration_userdata
$sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'bb_registration_userdata(
        `value_id`                      int(10) unsigned NOT NULL auto_increment,
        `id_bb_registration_fields`     int(10) unsigned default NULL,
        `id_customer`                   int(10) unsigned default NULL,
        `id_guest`                      int(10) unsigned default 0,
        `field_value_id`                mediumtext,
        `value`                         mediumtext,
        PRIMARY KEY                     (`value_id`),
        UNIQUE KEY `uniq`               (`id_bb_registration_fields`,`id_customer`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

// Table bb_registration_fields_shop
$sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'bb_registration_fields_shop(
        `id_bb_registration_fields`       int(11) NOT NULL,
        `id_shop`                         int(11) NOT NULL,
        PRIMARY KEY                       (`id_bb_registration_fields`,`id_shop`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

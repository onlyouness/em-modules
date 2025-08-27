<?php
/**
 * B2B Registration.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright Copyright 2023 © fmemodules All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  FMM Modules
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class BusinessAccountModel extends ObjectModel
{
    public $id_b2bregistration;
    public $id_b2b_profile;
    public $id_customer;
    public $middle_name;
    public $name_suffix;
    public $flag;
    public $active;
    public static $definition = [
        'table' => 'b2bregistration',
        'primary' => 'id_b2bregistration',
        'fields' => [
            'id_customer' => ['type' => self::TYPE_INT],
            'id_b2b_profile' => ['type' => self::TYPE_INT],
            'middle_name' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'name_suffix' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'flag' => ['type' => self::TYPE_BOOL],
            'active' => ['type' => self::TYPE_BOOL],
        ],
    ];

    public static function tableExists($table)
    {
        return (bool) Db::getInstance()->executeS('SHOW TABLES LIKE \'' . _DB_PREFIX_ . bqSQL($table) . '\'');
    }

    public static function getOldCustomFields($table)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from($table);

        return Db::getInstance()->executeS($sql);
    }

    public static function getFieldsRequiredDB()
    {
        $customer = new Customer();
        $allRequiredFields = $customer->getFieldsRequiredDatabase();

        $filteredFields = [];
        if (isset($allRequiredFields) && $allRequiredFields) {
            foreach ($allRequiredFields as $field) {
                $filteredFields[] = $field['field_name'];
            }
        }

        return $filteredFields;
    }

    public static function insertOldDataToNewTables($data)
    {
        if (!empty($data)) {
            foreach ($data as $b2b) {
                Db::getInstance()->insert(
                    'bb_registration_fields',
                    [
                        'id_bb_registration_fields' => (int) $b2b['id_b2b_custom_fields'],
                        'field_type' => $b2b['b2b_field_type'],
                        'active' => $b2b['active'],
                        'id_b2b_profile' => $b2b['id_b2b_profile'],
                        'position' => $b2b['position'],
                        'value_required' => $b2b['field_required'],
                    ]
                );
                Db::getInstance()->insert(
                    'bb_registration_fields_shop',
                    [
                        'id_bb_registration_fields' => (int) $b2b['id_b2b_custom_fields'],
                        'id_shop' => (int) Context::getContext()->shop->id,
                    ]
                );
            }
        }

        return true;
    }

    public static function insertOldLangDataToNewTables($data)
    {
        if (!empty($data)) {
            foreach ($data as $b2b) {
                Db::getInstance()->insert(
                    'bb_registration_fields_lang',
                    [
                        'id_bb_registration_fields' => (int) $b2b['id_b2b_custom_fields'],
                        'id_lang' => $b2b['id_lang'],
                        'field_name' => $b2b['b2b_field_name'],
                    ]
                );
            }
        }

        return true;
    }

    public static function insertOldUserDataToNewTables($data)
    {
        if (!empty($data)) {
            foreach ($data as $b2b) {
                return Db::getInstance()->insert(
                    'bb_registration_userdata',
                    [
                        'value_id' => (int) $b2b['id_b2b_fields_data'],
                        'id_bb_registration_fields' => (int) $b2b['id_field'],
                        'id_customer' => $b2b['id_customer'],
                        'value' => $b2b['b2b_field_name'],
                        'field_value_id' => 0,
                    ]
                );
            }
        }

        return true;
    }

    public static function getAllGenders($id_lang)
    {
        $sql = new DbQuery();
        $sql->select('b.*, a.*');
        $sql->from('gender', 'a');
        $sql->leftJoin('gender_lang', 'b', 'b.`id_gender` = a.`id_gender` AND b.`id_lang` = ' . (int) $id_lang);

        return Db::getInstance()->executeS($sql);
    }

    public static function existsTab($tab_class)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT id_tab AS id
            FROM `' . _DB_PREFIX_ . 'tab` t
            WHERE LOWER(t.`class_name`) = \'' . pSQL($tab_class) . '\'');

        if (count($result) == 0) {
            return false;
        }

        return true;
    }

    public static function getB2BCustomer($id_lang, $id_customer)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        $sql = new DbQuery();
        $sql->select('
        c.*,
        g.*,
        a.id_address,
        a.alias,
        a.city,
        a.address1,
        a.address2,
        a.vat_number,
        a.id_country,
        a.id_state,
        a.postcode,
        a.phone,
        shop.*,
        b.*
        ');
        $sql->from('customer', 'c');
        $sql->leftJoin('gender_lang', 'g', 'c.`id_gender` = g.`id_gender` AND g.`id_lang` = ' . (int) $id_lang);
        $sql->leftJoin('address', 'a', 'a.`id_customer` = c.`id_customer`');
        $sql->leftJoin('shop', 'shop', 'c.`id_shop` = shop.`id_shop`');
        $sql->leftJoin('b2bregistration', 'b', 'c.`id_customer` = b.`id_customer`');
        $sql->where('c.id_customer =' . (int) $id_customer);

        return Db::getInstance()->getRow($sql);
    }

    public static function getB2BCustomers($id_lang, $id_customer)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('customer', 'c');
        $sql->leftJoin('gender_lang', 'g', 'c.`id_gender` = g.`id_gender` AND g.`id_lang` = ' . (int) $id_lang);
        $sql->leftJoin('address', 'a', 'a.`id_customer` = c.`id_customer`');
        $sql->leftJoin('shop', 'shop', 'c.`id_shop` = shop.`id_shop`');
        $sql->leftJoin('b2bregistration', 'b', 'c.`id_customer` = b.`id_customer`');
        $sql->where('c.id_customer =' . (int) $id_customer);

        return Db::getInstance()->ExecuteS($sql);
    }

    public static function getGenderName($id_gender, $id_lang)
    {
        $sql = new DbQuery();
        $sql->select('name');
        $sql->from('gender_lang');
        $sql->where('id_gender=' . (int) $id_gender . ' AND id_lang=' . (int) $id_lang);

        return Db::getInstance()->getRow($sql);
    }

    public static function getRegisteredB2B($id_customer)
    {
        $sql = new DbQuery();
        $sql->select('id_customer, id_b2bregistration, id_b2b_profile');
        $sql->from('b2bregistration');
        $sql->where('id_customer=' . (int) $id_customer);

        return Db::getInstance()->getRow($sql);
    }

    public static function getAllCategories()
    {
        $sql = new DbQuery();
        $sql->select('id_category');
        $sql->from('category');
        $result = Db::getInstance()->executeS($sql);
        if ($result) {
            foreach ($result as &$res) {
                $res = array_shift($res);
            }
        }

        return $result;
    }

    public static function addB2BGroupToCategory($id_category, $id_group)
    {
        if (!BusinessAccountModel::isAlreadyAdded($id_category, $id_group)) {
            return Db::getInstance()->insert(
                'category_group',
                ['id_category' => (int) $id_category,
                    'id_group' => (int) $id_group]
            );
        }

        return true;
    }

    public static function isAlreadyAdded($id_category, $id_group)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('category_group');
        $sql->WHERE('`id_category` = ' . (int) $id_category . ' AND id_group= ' . (int) $id_group);

        return (bool) Db::getInstance()->getRow($sql);
    }

    public static function extraFieldsDeletion($id_customer)
    {
        return Db::getInstance()->delete('b2bregistration', 'id_customer =' . (int) $id_customer);
    }

    public static function deleteNotCustomer()
    {
        return Db::getInstance()->delete('b2bregistration', 'id_customer = 0');
    }

    public static function deleteAddress($id_customer)
    {
        return Db::getInstance()->delete('address', 'id_customer =' . (int) $id_customer);
    }

    public static function getAddress($id_customer)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('address');
        $sql->where('id_customer=' . (int) $id_customer);

        return Db::getInstance()->getRow($sql);
    }

    public static function getBusinessStatus($id_customer)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('b2bregistration');
        $sql->where('id_customer=' . (int) $id_customer);

        return Db::getInstance()->getRow($sql);
    }

    public static function deleteDefaultValues()
    {
        return Configuration::deleteByName('B2BREGISTRATION_ENABLE_DISABLE')
        && Configuration::deleteByName('B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE')
        && Configuration::deleteByName('B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE')
        && Configuration::deleteByName('B2BREGISTRATION_NAME_PREFIX_OPTIONS')
        && Configuration::deleteByName('B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE')
        && Configuration::deleteByName('B2BREGISTRATION_NAME_SUFFIX_OPTIONS')
        && Configuration::deleteByName('B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE')
        && Configuration::deleteByName('B2BREGISTRATION_GROUPS')
        && Configuration::deleteByName('B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE')
        && Configuration::deleteByName('B2BREGISTRATION_ADMIN_EMAIL_SENDER')
        && Configuration::deleteByName('B2BREGISTRATION_ADMIN_EMAIL_ID')
        && Configuration::deleteByName('B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE')
        && Configuration::deleteByName('B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE')
        && Configuration::deleteByName('B2BREGISTRATION_SITE_KEY')
        && Configuration::deleteByName('B2BREGISTRATION_SECRET_KEY')
        && Configuration::deleteByName('B2BREGISTRATION_DOB_ENABLE_DISABLE')
        && Configuration::deleteByName('B2BREGISTRATION_ENABLE_CUSTOM_FIELDS')
        && Configuration::deleteByName('B2BREGISTRATION_ADDRESS_ENABLE_DISABLE')
        && Configuration::deleteByName('B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE')
        && Configuration::deleteByName('B2BREGISTRATION_WEBSITE_ENABLE_DISABLE')
        && Configuration::deleteByName('B2BREGISTRATION_URL_KEY')
        && Configuration::deleteByName('B2BREGISTRATION_URL_TEXT')
        && Configuration::deleteByName('B2BREGISTRATION_PERSONAL_TEXT')
        && Configuration::deleteByName('B2BREGISTRATION_COMPANY_TEXT')
        && Configuration::deleteByName('B2BREGISTRATION_SIGNIN_TEXT')
        && Configuration::deleteByName('B2BREGISTRATION_CMS_PAGES')
        && Configuration::deleteByName('B2BREGISTRATION_CMS_PAGES_RULE')
        && Configuration::deleteByName('B2BREGISTRATION_NORMAL_REGISTRATION')
        && Configuration::deleteByName('B2BREGISTRATION_AUTO_APPROVEL')
        && Configuration::deleteByName('B2BREGISTRATION_CUSTOM_FIELD_TEXT')
        && Configuration::deleteByName('B2BREGISTRATION_ERROR_MSG_TEXT')
        && Configuration::deleteByName('B2BREGISTRATION_ADDRESS_TEXT')
        && Configuration::deleteByName('B2BREGISTRATION_GROUP_ENABLE_DISABLE')
        && Configuration::deleteByName('B2BREGISTRATION_GROUP_SELECTION')
        && Configuration::deleteByName('B2BREGISTRATION_STATE_ENABLE_DISABLE')
        && Configuration::deleteByName('B2BREGISTRATION_COUNTRY_ENABLE_DISABLE')
        && Configuration::deleteByName('B2BREGISTRATION_TOPLINK_POSITION')
        && Configuration::deleteByName('B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR')
        && Configuration::deleteByName('B2BREGISTRATION_TOPLINK_TEXT_COLOR')
        && Configuration::deleteByName('B2BREGISTRATION_CUSTOM_TEXT')
        && Configuration::deleteByName('B2BREGISTRATION_ENABLE_DISABLE_VAT_VALIDATION')

        ;
    }

    public static function upgradeB2BModule()
    {
        $return = true;
        if (self::columnExists('id_b2b_profile', 'b2bregistration')) {
            $return = true;
        } else {
            $return = Db::getInstance()->execute('
                ALTER TABLE `' . _DB_PREFIX_ . 'b2bregistration`
                ADD `id_b2b_profile` int(11)
           ');
        }
        if (self::columnExists('id_b2b_profile', 'b2b_custom_fields')) {
            $return = true;
        } else {
            $return = Db::getInstance()->execute('
                ALTER TABLE `' . _DB_PREFIX_ . 'b2b_custom_fields`
                ADD `id_b2b_profile` int(11)
            ');
        }
        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'b2b_profile` (
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
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;');
        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'b2b_profile_lang` (
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
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;');

        return $return;
    }

    public static function columnExists($column_name, $table_name)
    {
        $columns = Db::getInstance()->ExecuteS('SELECT COLUMN_NAME FROM information_schema.columns
            WHERE table_schema = "' . _DB_NAME_ . '" AND table_name = "' . _DB_PREFIX_ . pSQL($table_name) . '"');
        if (isset($columns) && $columns) {
            foreach ($columns as $column) {
                if ($column['COLUMN_NAME'] == $column_name) {
                    return true;
                }
            }
        }

        return false;
    }
}

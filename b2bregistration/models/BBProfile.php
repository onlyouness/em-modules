<?php
/**
 * DISCLAIMER.
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FME Modules
 *  @copyright 2023 FME Modules
 *  @license   Comerical Licence
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class BBProfile extends ObjectModel
{
    public $b2b_profile_group;

    public $b2b_tos_page;

    public $b2b_link_rewrite;

    public $b2b_redirect_url;

    public $b2b_name_prefix;

    public $b2b_name_suffix;

    public $b2b_website;

    public $b2b_address;

    public $active;

    public $b2b_customer_auto_approval;

    public $b2b_redrection;

    public $b2b_custom_fields;

    public $b2b_name_prefix_active;

    public $b2b_name_suffix_active;

    public $b2b_middle_name_active;

    public $b2b_dob_active;

    public $b2b_siret_active;

    public $b2b_profile_name;

    public $b2b_personal_info_heading;

    public $b2b_company_info_heading;

    public $b2b_signin_heading;

    public $b2b_address_heading;

    public $b2b_customfields_heading;

    public $b2b_account_msg;

    public $b2b_profile_link_text;

    public $b2b_profile_link;

    public $b2b_profile_dob;

    public $b2b_profile_siret;

    public $partner_option = 0;

    public $newsletter = 0;

    public $b2b_customer_enable_group;

    public $groupBox;

    public static $definition = [
        'table' => 'b2b_profile',
        'primary' => 'id_b2b_profile',
        'multilang' => true,
        'fields' => [
            'b2b_profile_group' => ['type' => self::TYPE_INT, 'required' => true],
            'b2b_tos_page' => ['type' => self::TYPE_INT],
            'b2b_link_rewrite' => ['type' => self::TYPE_STRING, 'validate' => 'isLinkRewrite', 'required' => true],
            'b2b_redirect_url' => ['type' => self::TYPE_STRING, 'validate' => 'isAbsoluteUrl'],
            'b2b_name_prefix' => ['type' => self::TYPE_STRING],
            'b2b_name_suffix' => ['type' => self::TYPE_STRING],
            'groupBox' => ['type' => self::TYPE_STRING],
            'active' => ['type' => self::TYPE_BOOL],
            'b2b_customer_enable_group' => ['type' => self::TYPE_BOOL],
            'b2b_profile_link' => ['type' => self::TYPE_BOOL],
            'b2b_website' => ['type' => self::TYPE_BOOL],
            'b2b_address' => ['type' => self::TYPE_BOOL],
            'b2b_profile_dob' => ['type' => self::TYPE_BOOL],
            'b2b_profile_siret' => ['type' => self::TYPE_BOOL],
            'b2b_customer_auto_approval' => ['type' => self::TYPE_BOOL],
            'b2b_redrection' => ['type' => self::TYPE_BOOL],
            'b2b_custom_fields' => ['type' => self::TYPE_BOOL],
            'b2b_name_prefix_active' => ['type' => self::TYPE_BOOL],
            'b2b_name_suffix_active' => ['type' => self::TYPE_BOOL],
            'b2b_middle_name_active' => ['type' => self::TYPE_BOOL],
            'b2b_dob_active' => ['type' => self::TYPE_BOOL],
            'b2b_siret_active' => ['type' => self::TYPE_BOOL],
            'partner_option' => ['type' => self::TYPE_BOOL],
            'newsletter' => ['type' => self::TYPE_BOOL],
            'b2b_profile_name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'],
            'b2b_profile_link_text' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString'],
            'b2b_personal_info_heading' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString'],
            'b2b_company_info_heading' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString'],
            'b2b_signin_heading' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString'],
            'b2b_address_heading' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString'],
            'b2b_customfields_heading' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString'],
            'b2b_account_msg' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
        ],
    ];

    public function add($autodate = true, $null_values = false)
    {
        $languages = Language::getLanguages();
        $default_lang = Configuration::get('PS_LANG_DEFAULT');
        $properties = [
            'b2b_profile_name',
            'b2b_personal_info_heading',
            'b2b_company_info_heading',
            'b2b_signin_heading',
            'b2b_address_heading',
            'b2b_customfields_heading',
            'b2b_account_msg',
            'b2b_profile_link_text',
        ];

        foreach ($properties as $property) {
            if (isset($this->$property) && is_array($this->$property)) {
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    if (empty($this->$property[$id_lang]) && !empty($this->$property[$default_lang])) {
                        $this->$property[$id_lang] = $this->$property[$default_lang];
                    }
                }
            }
        }
        $this->b2b_name_prefix = (isset($this->b2b_name_prefix)
            && is_array($this->b2b_name_prefix)) ? implode(',', $this->b2b_name_prefix) : '';
        $this->groupBox = (isset($this->groupBox)
            && is_array($this->groupBox)) ? implode(',', $this->groupBox) : '';

        return parent::add($autodate, $null_values);
    }

    public function update($null_values = false)
    {
        $this->b2b_name_prefix = (isset($this->b2b_name_prefix)
            && is_array($this->b2b_name_prefix)) ? implode(',', $this->b2b_name_prefix) : '';
        $this->groupBox = (isset($this->groupBox)
            && is_array($this->groupBox)) ? implode(',', $this->groupBox) : '';

        return parent::update($null_values);
    }

    public static function positionOccupied($position)
    {
        if (!$position) {
            return false;
        }
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('b2b_custom_fields');
        $sql->where('position=' . (int) $position);

        return (bool) DB::getInstance()->getRow($sql);
    }

    public static function getHigherPosition()
    {
        $sql = new DbQuery();
        $sql->select('MAX(`position`)');
        $sql->from('b2b_custom_fields');
        $position = DB::getInstance()->getValue($sql);

        return (is_numeric($position)) ? $position : -1;
    }

    public function updatePosition($way, $position)
    {
        $sql = new DbQuery();
        $sql->select('`id_b2b_custom_fields`, `position`');
        $sql->from('b2b_custom_fields');
        $sql->where('id_b2b_custom_fields=' . (int) Tools::getValue('id'));
        $sql->orderby('`position` ASC');
        $res = Db::getInstance()->executeS($sql);
        if (!$res) {
            return false;
        }
        foreach ($res as $field) {
            if ((int) $field['id_b2b_custom_fields'] == (int) $this->id) {
                $moved_field = $field;
            }
        }

        if (!isset($moved_field) || !isset($position)) {
            return false;
        }

        return Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'b2b_custom_fields`
            SET `position`= `position` ' . ($way ? '- 1' : '+ 1') . '
            WHERE `position`
            ' . ($way
            ? '> ' . (int) $moved_field['position'] . ' AND `position` <= ' . (int) $position
            : '< ' . (int) $moved_field['position'] . ' AND `position` >= ' . (int) $position))
        && Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'b2b_custom_fields`
            SET `position` = ' . (int) $position . '
            WHERE `id_b2b_custom_fields` = ' . (int) $moved_field['id_b2b_custom_fields']);
    }

    public static function selectCustomFields($id_lang, $id_profile = null)
    {
        if ($id_profile == null) {
            $id_profile = 0;
        }
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('b2b_custom_fields', 'f');
        $sql->leftJoin('b2b_custom_fields_lang', 'fl', 'fl.`id_b2b_custom_fields` = f.`id_b2b_custom_fields`');
        $sql->where('fl.id_lang=' . (int) $id_lang);
        $sql->where('f.active = 1');
        $sql->orderby('f.position');

        return Db::getInstance()->executeS($sql);
    }

    public static function isKeyExists($key)
    {
        return (bool) Db::getInstance()->getValue('SELECT *
            FROM `' . _DB_PREFIX_ . self::$definition['table'] . '`
            WHERE b2b_link_rewrite = \'' . pSQL($key) . '\'
            AND active = 1');
    }

    public static function getIdProfileByKey($key, $active = true)
    {
        $sql = new DbQuery();
        $sql->select('id_b2b_profile');
        $sql->from(self::$definition['table']);
        $sql->where('b2b_link_rewrite = \'' . pSQL($key) . '\'');
        if ($active) {
            $sql->where('active = 1');
        }

        return (int) Db::getInstance()->getValue($sql);
    }

    public static function getGroupByProfile($id_b2b_profile)
    {
        if (!$id_b2b_profile) {
            return false;
        }

        $sql = new DbQuery();
        $sql->select('b2b_profile_group');
        $sql->from(self::$definition['table']);
        $sql->where('id_b2b_profile = ' . (int) $id_b2b_profile);

        return (int) Db::getInstance()->getValue($sql);
    }

    public static function getAllOccupiedGroups($active = false, $exclude = [])
    {
        $sql = new DbQuery();
        $sql->select('id_b2b_profile');
        $sql->from(self::$definition['table']);

        if (isset($exclude) && $exclude) {
            $sql->where('id_b2b_profile NOT IN (' . implode(',', array_map('intval', $exclude)) . ')');
        }

        if ($active) {
            $sql->where('active = 1');
        }

        $occupiedIds = [];
        $result = Db::getInstance()->executeS($sql);
        if (isset($result) && $result) {
            foreach ($result as $res) {
                $occupiedIds[] = (int) $res['id_b2b_profile'];
            }
        }

        return $occupiedIds;
    }

    public static function getIdProfileByField($field, $value, $active = true)
    {
        if (empty($field) || empty($value)) {
            return false;
        }

        $sql = new DbQuery();
        $sql->select('id_b2b_profile');
        $sql->from(self::$definition['table']);
        $sql->where(pSQL($field) . ' = \'' . pSQL($value) . '\'');
        if ($active) {
            $sql->where('active = 1');
        }

        return (int) Db::getInstance()->getValue($sql);
    }

    public static function getProfileGroup($id_profile)
    {
        $sql = new DbQuery();
        $sql->select('b2b_profile_group');
        $sql->from(self::$definition['table']);
        $sql->where('id_b2b_profile = ' . (int) $id_profile);
        $sql->orderBy('id_b2b_profile ASC');

        return (int) Db::getInstance()->getValue($sql);
    }

    public static function getTopLinks($active = true, $id_lang = null, $id_shop = null)
    {
        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        if (!$id_shop) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        $sql = new DbQuery();
        $sql->select('b2b.*, b2bl.*');
        $sql->from(self::$definition['table'], 'b2b');
        $sql->leftJoin(
            self::$definition['table'] . '_lang',
            'b2bl',
            'b2b.id_b2b_profile = b2bl.id_b2b_profile'
        );
        if (Shop::isFeatureActive()) {
            $sql->leftJoin(
                'group_shop',
                'sg',
                'sg.id_group = b2b.b2b_profile_group'
            );
            $sql->leftJoin(
                'b2b_profile_shop',
                'ps',
                'ps.id_b2b_profile = b2bl.id_b2b_profile'
            );
            if ($active) {
                $sql->where('sg.id_shop = ' . (int) $id_shop);
                $sql->where('ps.id_shop = ' . (int) $id_shop);
            }
        }

        if ($active) {
            $sql->where('b2b.active = 1');
        }

        $sql->where('b2b.b2b_profile_link = 1');
        $sql->where('b2bl.id_lang = ' . (int) $id_lang);
        $links = Db::getInstance()->executeS($sql);
        if (isset($links) && $links) {
            foreach ($links as &$link) {
                $link['top_link_text'] = $link['b2b_profile_link_text'];
                $link['page_link'] = Context::getContext()->link->getModuleLink(
                    'b2bregistration',
                    'business',
                    ['profile_key' => $link['b2b_link_rewrite']]
                );
            }
        }

        return $links;
    }

    public static function getAllProfiles($active = false, $id_lang = null)
    {
        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        $sql = new DbQuery();
        $sql->select('b.id_b2b_profile, bl.b2b_profile_name');
        $sql->from(self::$definition['table'], 'b');
        $sql->leftJoin(
            self::$definition['table'] . '_lang',
            'bl',
            'b.id_b2b_profile = bl.id_b2b_profile'
        );
        $sql->where('bl.id_lang = ' . (int) $id_lang);
        if ($active) {
            $sql->where('b.active = 1');
        }

        $profiles = Db::getInstance()->executeS($sql);
        array_unshift(
            $profiles,
            [
                'id_b2b_profile' => '0',
                'b2b_profile_name' => Module::getInstanceByName('b2bregistration')->l('Default'),
            ]
        );

        return $profiles;
    }

    public static function getAllBBGroups($active = false)
    {
        $sql = new DbQuery();
        $sql->select('b2b_profile_group');
        $sql->from(self::$definition['table']);

        if ($active) {
            $sql->where('active = 1');
        }

        $b2bGroups = [];
        $result = Db::getInstance()->executeS($sql);
        if (isset($result) && $result) {
            foreach ($result as $res) {
                $b2bGroups[] = (int) $res['b2b_profile_group'];
            }
        }

        return $b2bGroups;
    }

    public static function getProfileStatus($id_profile = null)
    {
        if (!$id_profile) {
            return false;
        }
        $sql = new DbQuery();
        $sql->select('active');
        $sql->from(self::$definition['table']);
        $sql->where('id_b2b_profile = ' . (int) $id_profile);

        $active = Db::getInstance()->getValue($sql);

        return $active;
    }
}

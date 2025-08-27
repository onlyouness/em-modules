<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FME Modules
 *  @copyright 2021 FME Modules
 *  @license   Comerical Licence
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class BToBCustomFields extends ObjectModel
{
    public $id_bb_registration_fields;

    public $field_type;

    public $field_validation;

    public $position;

    public $default_value;

    public $value_required;

    public $editable = 1;

    public $active;

    public $alert_type = 'info';

    public $extensions = 'jpg';

    public $attachment_size = 2;

    public $created_time;

    public $update_time;

    public $field_name;

    public $assoc_shops;

    public $dependant;

    public $dependant_field;

    public $dependant_value;

    public $limit;

    public $id_b2b_profile = 0;

    public const KB = 1024;

    public const MB = 1048576;

    public static $definition = [
        'table' => 'bb_registration_fields',
        'primary' => 'id_bb_registration_fields',
        'multilang' => true,
        'fields' => [
            'active' => ['type' => self::TYPE_BOOL],
            'created_time' => ['type' => self::TYPE_DATE],
            'update_time' => ['type' => self::TYPE_DATE],
            'value_required' => ['type' => self::TYPE_BOOL],
            'editable' => ['type' => self::TYPE_BOOL],
            'alert_type' => ['type' => self::TYPE_STRING],
            'position' => ['type' => self::TYPE_INT],
            'field_validation' => ['type' => self::TYPE_STRING],
            'extensions' => ['type' => self::TYPE_STRING],
            'attachment_size' => ['type' => self::TYPE_FLOAT],
            'field_type' => ['type' => self::TYPE_NOTHING],
            'assoc_shops' => ['type' => self::TYPE_STRING],
            'dependant' => ['type' => self::TYPE_INT],
            'dependant_field' => ['type' => self::TYPE_INT],
            'dependant_value' => ['type' => self::TYPE_INT],
            'limit' => ['type' => self::TYPE_INT],
            'id_b2b_profile' => ['type' => self::TYPE_INT],
            'default_value' => ['type' => self::TYPE_STRING, 'lang' => true],
            'field_name' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
            ],
        ],
    ];

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }

    public function delete()
    {
        $res = Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'bb_registration_fields
            WHERE id_bb_registration_fields = ' .
            (int) $this->id_bb_registration_fields);

        $res &= parent::delete();

        return $res;
    }

    public function deleteSelection($selection)
    {
        if (!is_array($selection)) {
            exit(Tools::displayError());
        }

        $result = true;
        foreach ($selection as $id) {
            $this->id_bb_registration_fields = (int) $id;
            $result = $result && $this->delete();
        }

        return $result;
    }

    public static function deleteCustomerData($id_customer)
    {
        if (!$id_customer) {
            return false;
        } else {
            $imgTypes = self::getFieldIdByType('image');
            $attachmentTypes = self::getFieldIdByType('attachment');
            $fileType = array_merge($imgTypes, $attachmentTypes);
            if (isset($fileType) && $fileType) {
                foreach ($fileType as $id_bb_registration_fields) {
                    $filePath = self::getFileValues(
                        $id_bb_registration_fields,
                        'id_customer = ' . (int) $id_customer
                    );
                    if (isset($filePath) && file_exists($filePath)) {
                        self::deleteDir(dirname($filePath));
                    }
                }
            }

            return (bool) Db::getInstance()->delete(
                'bb_registration_userdata',
                'id_customer = ' . (int) $id_customer
            );
        }
    }

    public static function getCustomFieldValues($id)
    {
        return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM ' .
            _DB_PREFIX_ . 'bb_registration_fields_values
            WHERE id_bb_registration_fields = ' . (int) $id);
    }

    public static function getOptions($id_bb_registration_fields, $id_lang = null)
    {
        if (!$id_bb_registration_fields) {
            return [];
        }

        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT
        val.*,
        fvl.*,
        val.`field_value_id` as val
        FROM ' . _DB_PREFIX_ . 'bb_registration_fields_values val
        LEFT JOIN ' . _DB_PREFIX_ . 'bb_registration_fields_values_lang fvl
            ON (val.field_value_id = fvl.field_value_id AND fvl.id_lang = ' .
            (int) $id_lang . ')
        WHERE val.id_bb_registration_fields = ' .
            (int) $id_bb_registration_fields);
    }

    public static function getAllFields(
        $where = null,
        $id_lang = null,
        $order_by = null,
        $way = 'ASC'
    ) {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        $sql = 'SELECT
        a.*,
        b.`field_name`,
        b.`default_value`,
        val.`field_value_id`,
        val.`value`,
        fvl.`field_value`,
        val.`id_customer`
            FROM ' . _DB_PREFIX_ . 'bb_registration_fields a
            INNER JOIN ' . _DB_PREFIX_ . 'bb_registration_fields_lang b
                ON (a.id_bb_registration_fields = b.id_bb_registration_fields AND b.id_lang = ' .
            (int) $id_lang . ')
            INNER JOIN ' . _DB_PREFIX_ . 'bb_registration_userdata val
                ON (a.id_bb_registration_fields = val.id_bb_registration_fields)
            LEFT JOIN ' . _DB_PREFIX_ . 'bb_registration_fields_values_lang fvl
                ON (val.field_value_id = fvl.field_value_id AND fvl.id_lang = ' .
            (int) $id_lang . ')
            WHERE a.active = 1 ' . (($where) ? ' AND ' .
                ($where) : '') . (($order_by) ? ' ORDER BY ' . $order_by . (($way) ? ' ' . $way : '') : '');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
    }

    public static function getListFields($where, $id_lang = null)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        $sql = 'SELECT
        a.*,
        b.`field_name`,
        val.`field_value_id`,
        val.`value`,
        fvl.`field_value`,
        c.`id_customer`
            FROM ' . _DB_PREFIX_ . 'bb_registration_fields a
            INNER JOIN ' . _DB_PREFIX_ . 'bb_registration_fields_lang b
                ON (a.id_bb_registration_fields = b.id_bb_registration_fields AND b.id_lang = ' .
            (int) $id_lang . ')
            INNER JOIN ' . _DB_PREFIX_ . 'bb_registration_userdata val
                ON (a.id_bb_registration_fields = val.id_bb_registration_fields)
            LEFT JOIN ' . _DB_PREFIX_ . 'bb_registration_fields_values_lang fvl
                ON (val.field_value_id = fvl.field_value_id AND fvl.id_lang = ' .
            (int) $id_lang . ')
            WHERE a.active = 1
            AND val.`id_customer` IN (SELECT id_customer FROM `' . _DB_PREFIX_ . 'customer`)
            AND ' . pSQL($where);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
    }

    public static function getFieldsOnly($where, $id_shop, $id_lang = null, $order_by = null, $way = 'ASC')
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $sql = 'SELECT a.*, b.`field_name`, b.`default_value`
            FROM ' . _DB_PREFIX_ . 'bb_registration_fields a
            LEFT JOIN ' . _DB_PREFIX_ . 'bb_registration_fields_lang b
                ON (a.id_bb_registration_fields = b.id_bb_registration_fields and b.id_lang = ' . (int) $id_lang . ')
            LEFT JOIN ' . _DB_PREFIX_ . 'bb_registration_fields_shop sh
                ON (a.id_bb_registration_fields = sh.id_bb_registration_fields)
            WHERE ' . (!empty($where) ? $where : '1') . ' AND sh.id_shop =' . (int) $id_shop .
            ($order_by ? ' ORDER BY ' . $order_by . ($way ? ' ' . $way : '') : '');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
    }

    public function getDependentFieldValue($id_bb_registration_fields)
    {
        $sql = new DbQuery();
        $sql->select('`dependant`, `dependant_value`');
        $sql->from('bb_registration_fields');
        $sql->where('`id_bb_registration_fields` = ' . (int) $id_bb_registration_fields);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public function fieldValidate($fields, $id_customer = null)
    {
        $errors = [];
        $allfields = $this->getCustomFields(
            Context::getContext()->cookie->id_lang
        );

        foreach ($allfields as $f) {
            if (!isset($fields[$f['id_bb_registration_fields']])) {
                if ($f['field_type'] == 'image' || $f['field_type'] == 'attachment') {
                    $fields[$f['id_bb_registration_fields']] = null;
                } else {
                    continue;
                }
            }
            $user_field_value = $fields[$f['id_bb_registration_fields']];
            if (
                $f['field_type'] == 'image'
                || $f['field_type'] == 'attachment'
            ) {
                $file_value = self::getFieldValue(
                    $f['id_bb_registration_fields'],
                    $id_customer
                );

                if (
                    $f['value_required'] == 1 && empty($file_value)
                    && (
                        !isset($_FILES['fields'])
                        || !isset($_FILES['fields']['name'][$f['id_bb_registration_fields']])
                        || empty($_FILES['fields']['name'][$f['id_bb_registration_fields']])
                    )
                ) {
                    $errors[] = $f['field_name'] . ' ' .
                        Module::getInstanceByName('b2bregistration')->trans['required'];
                } elseif (isset($_FILES, $_FILES['fields']['name'][$f['id_bb_registration_fields']])) {
                    $ext = pathinfo(
                        $_FILES['fields']['name'][$f['id_bb_registration_fields']],
                        PATHINFO_EXTENSION
                    );
                    $size = Tools::ps_round(
                        $_FILES['fields']['size'][$f['id_bb_registration_fields']] / Fields::MB,
                        2,
                        PS_ROUND_UP
                    );
                    if (
                        $ext && isset($f['extensions']) && $f['extensions']
                        && !in_array($ext, explode(',', $f['extensions']))
                    ) {
                        $errors[] = $f['field_name'] . ' ' .
                            Module::getInstanceByName('b2bregistration')->trans['type'];
                    }
                    if (
                        $size && isset($f['attachment_size'])
                        && $f['attachment_size'] && $size > $f['attachment_size']
                    ) {
                        $errors[] = $f['field_name'] . ' ' .
                            Module::getInstanceByName('b2bregistration')->trans['size'];
                    }
                }
            } elseif ($f['field_type'] != 'boolean') {
                if ($f['value_required'] == 1 && empty($user_field_value)) {
                    if ($f['dependant'] <= 0) {
                        $errors[] = $f['field_name'] . ' ' .
                            Module::getInstanceByName('b2bregistration')->trans['required'];
                    } elseif ($f['dependant'] > 0) {
                        $depend_val = (int) self::getDependantCheckedValue(
                            $f['id_bb_registration_fields']
                        );
                        $depend_field = (int) self::getDependantCheckedVal(
                            $f['id_bb_registration_fields']
                        );

                        if (isset($fields[$depend_field])) {
                            $existance = $fields[$depend_field];
                            if (is_array($existance)) {
                                if (in_array($depend_val, $existance)) {
                                    $errors[] = $f['field_name'] . ' ' .
                                        Module::getInstanceByName('b2bregistration')->trans['required'];
                                }
                            } else {
                                if ($depend_val == $existance) {
                                    $errors[] = $f['field_name'] . ' ' .
                                        Module::getInstanceByName('b2bregistration')->trans['required'];
                                }
                            }
                        }
                    }
                }
            }

            if (array_key_exists($f['id_bb_registration_fields'], $fields) && !empty($f['field_validation']) && !empty($user_field_value)) {
                if ($f['dependant'] > 0) {
                    $depend_val = (int) self::getDependantCheckedValue(
                        $f['id_bb_registration_fields']
                    );
                    $depend_field = (int) self::getDependantCheckedVal(
                        $f['id_bb_registration_fields']
                    );
                    if (isset($fields[$depend_field])) {
                        $existance = $fields[$depend_field];
                        if (is_array($existance)) {
                            if (in_array($depend_val, $existance)) {
                                if (!call_user_func('Validate::' .
                                    $f['field_validation'], $user_field_value)) {
                                    $errors[] = $f['field_name'] . ' ' .
                                        Module::getInstanceByName('b2bregistration')->trans['invalid'];
                                }
                            }
                        } else {
                            if ($depend_val == $existance) {
                                if (!call_user_func('Validate::' .
                                    $f['field_validation'], $user_field_value)) {
                                    $errors[] = $f['field_name'] . ' ' .
                                        Module::getInstanceByName('b2bregistration')->trans['invalid'];
                                }
                            }
                        }
                    }
                } else {
                    if (!call_user_func('Validate::' .
                        $f['field_validation'], $user_field_value)) {
                        $errors[] = $f['field_name'] . ' ' .
                            Module::getInstanceByName('b2bregistration')->trans['invalid'];
                    }
                }
            }
            // character limit validation as of v2.2.0
            if ($f['field_type'] == 'textarea' || $f['field_type'] == 'text') {
                $string_length = Tools::strlen($user_field_value);
                if ($f['limit'] > 0 && $string_length > $f['limit']) {
                    $errors[] = $f['field_name'] . ' ' .
                        Module::getInstanceByName('b2bregistration')->trans['limit'];
                }
            }
        }
        $_POST['errors'] = $errors;

        return $errors;
    }

    public function saveFieldValues(
        $fields,
        $id_customer,
        $id_lang = null,
        $type = 'customer'
    ) {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $errors = 0;
        $errors_count = [];
        $errors_count = self::fieldValidate($fields, $id_customer);

        if (!count($errors_count)) {
            if (!empty($this->getAllFields($id_customer))) {
                $this->deleteCustomerData($id_customer);
            }
            if (isset($fields) && $fields) {
                foreach ($fields as $id => $field) {
                    $fieldArray = is_array($field) ? implode(',', $field) : $field;
                    $dependentField = $this->getDependentFieldValue($id);
                    $saveField = true;
                    if ($dependentField && $dependentField['dependant']) {
                        $dependant_value = $dependentField['dependant_value'];
                        if (!isset($fields['$dependant_value'])) {
                            $saveField = false;
                            foreach ($fields as $value) {
                                if ((is_array($value) && in_array($dependant_value, $value)) || ($dependant_value == $value)) {
                                    $saveField = true;
                                    break;
                                }
                            }
                        }
                    }

                    if ($saveField) {
                        $field_type = self::getFieldType($id);
                        $predefinedFields = ['text', 'textarea', 'date', 'boolean'];
                        if (is_array($field)) {
                            $field = implode(',', $field);
                        }
                        if (isset($field_type) && in_array($field_type, $predefinedFields)) {
                            if ($type == 'guest') {
                                $sql_fields_vals = '(' . (int) $id . ', 0, ' .
                                    (int) $id_customer . ', 0, "' . pSQL($field) . '")';
                                $sql_upd = 'id_bb_registration_fields = ' . (int) $id .
                                    ', id_customer = 0, id_guest = ' . (int) $id_customer .
                                    ', value = "' . pSQL($field) . '"';
                            } else {
                                $sql_fields_vals = '(' . (int) $id . ',' . (int) $id_customer .
                                    ', 0, 0, "' . pSQL($field) . '")';
                                $sql_upd = 'id_bb_registration_fields = ' . (int) $id . ', id_customer = ' .
                                    (int) $id_customer . ', id_guest = 0, value = "' . pSQL($field) . '"';
                            }
                        } else {
                            if ($type == 'guest') {
                                $sql_fields_vals = '(' . (int) $id . ', 0, ' .
                                    (int) $id_customer . ', "' . pSQL($field) . '", "")';
                                $sql_upd = 'id_bb_registration_fields = ' . (int) $id .
                                    ', id_customer = 0, id_guest = ' . (int) $id_customer .
                                    ', field_value_id = "' . pSQL($field) . '"';
                            } else {
                                $sql_fields_vals = '(' . (int) $id . ',' .
                                    (int) $id_customer . ', 0, "' . pSQL($field) . '", "")';
                                $sql_upd = 'id_bb_registration_fields = ' . (int) $id .
                                    ', id_customer = ' . (int) $id_customer .
                                    ', id_guest = 0, field_value_id = "' . pSQL($field) . '"';
                            }
                        }
                        $sql = 'INSERT INTO ' . _DB_PREFIX_ .
                            'bb_registration_userdata (
                            id_bb_registration_fields,
                            id_customer,
                            id_guest,
                            field_value_id, value
                            )
                            VALUES ' . (string) $sql_fields_vals .
                            ' ON DUPLICATE KEY UPDATE ' . (string) $sql_upd;

                        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql)) {
                            ++$errors;
                        }
                    }
                }
            }

            if (
                isset($_FILES, $_FILES['fields'], $_FILES['fields']['name'])
                && count($_FILES['fields']['name']) > 0
            ) {
                $files = $_FILES['fields'];
                foreach ($files['name'] as $key => $file) {
                    if (
                        $files['error'][$key]
                        && isset(Module::getInstanceByName('b2bregistration')->file_errors[$files['error'][$key]])
                    ) {
                        ++$errors;
                    } else {
                        $ext = Tools::substr(
                            $files['name'][$key],
                            strrpos($files['name'][$key], '.') + 1
                        );
                        $file_name = 'field_' . $key . '.' . $ext;

                        $index_file = _PS_MODULE_DIR_ .
                            Module::getInstanceByName('b2bregistration')->name .
                            DIRECTORY_SEPARATOR . 'index.php';
                        $file_path = _PS_UPLOAD_DIR_ .
                            Module::getInstanceByName('b2bregistration')->name .
                            DIRECTORY_SEPARATOR . $id_customer;
                        if (!file_exists($file_path)) {
                            @mkdir($file_path, 0777, true);
                            @copy($index_file, $file_path . DIRECTORY_SEPARATOR .
                                'index.php');
                        }

                        $file_path .= DIRECTORY_SEPARATOR . $key;
                        if (!file_exists($file_path)) {
                            @mkdir($file_path, 0777, true);
                            @copy($index_file, $file_path . DIRECTORY_SEPARATOR . 'index.php');
                        }

                        // remove old file
                        if (file_exists($file_path . DIRECTORY_SEPARATOR . $file_name)) {
                            @unlink($file_path . DIRECTORY_SEPARATOR . $file_name);
                        }

                        if (!move_uploaded_file(
                            $files['tmp_name'][$key],
                            $file_path . DIRECTORY_SEPARATOR . $file_name
                        )) {
                            ++$errors;
                        } else {
                            $field_value = $file_path . DIRECTORY_SEPARATOR . $file_name;
                            if ($type == 'guest') {
                                $sql_fields_vals = '(' . (int) $key . ', 0, ' . (int) $id_customer .
                                    ', "", "' . pSQL($field_value) . '")';
                                $sql_upd = 'id_bb_registration_fields = ' . (int) $key .
                                    ', id_guest = ' . (int) $id_customer . ', value = "' . pSQL($field_value) . '"';
                            } else {
                                $sql_fields_vals = '(' . (int) $key . ',' . (int) $id_customer .
                                    ', 0, "", "' . pSQL($field_value) . '")';
                                $sql_upd = 'id_bb_registration_fields = ' . (int) $key .
                                    ', id_customer = ' . (int) $id_customer . ', value = "' . pSQL($field_value) . '"';
                            }

                            $sql = 'INSERT INTO ' . _DB_PREFIX_ .
                                'bb_registration_userdata (
                                id_bb_registration_fields,
                                id_customer,
                                id_guest,
                                field_value_id,
                                value
                                ) VALUES ' . (string) $sql_fields_vals .
                                ' ON DUPLICATE KEY UPDATE ' . (string) $sql_upd;

                            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql)) {
                                ++$errors;
                            }
                        }
                    }
                }
            }
        }

        $result = ['result' => true];
        if ($errors > 0 || count($errors_count) > 0) {
            $result = ['result' => false, 'errors' => $errors_count];
        }

        return $result;
    }

    public static function getCustomFields($id_lang, $id_shop = null, $id_profile = null)
    {
        if (!$id_shop) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        $sql = new DbQuery();
        $sql->select('t.*, tl.field_name, tl.default_value');
        $sql->from(self::$definition['table'], 't');
        $sql->leftJoin(
            self::$definition['table'] . '_lang',
            'tl',
            't.id_bb_registration_fields = tl.id_bb_registration_fields'
        );

        $sql->where('t.active = 1');
        $sql->where('tl.id_lang = ' . (int) $id_lang);
        $sql->orderBy('t.position');
        if ($id_profile) {
            $sql->where('t.id_b2b_profile = ' . (int) $id_profile);
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $final = [];
        if (isset($result)) {
            foreach ($result as &$res) {
                if ($res['assoc_shops'] && $id_shop) {
                    if (in_array($id_shop, explode(',', $res['assoc_shops']))) {
                        $final[] = $res;
                    }
                }
            }
        }

        return $final;
    }

    public static function profileHasFields($id_profile, $id_shop = null)
    {
        if (!$id_shop) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(self::$definition['table']);

        $sql->where('active = 1');
        $sql->where('id_b2b_profile = ' . (int) $id_profile);

        return (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function getCustomFieldsValues($id_bb_registration_fields, $id_lang = null)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT fv.*, fvl.*
            FROM ' . _DB_PREFIX_ . 'bb_registration_fields_values fv
            JOIN ' . _DB_PREFIX_ . 'bb_registration_fields_values_lang fvl
                ON(fv.field_value_id = fvl.field_value_id AND fvl.id_lang = ' .
            (int) $id_lang . ')
            WHERE id_bb_registration_fields = ' . (int) $id_bb_registration_fields);
    }

    public function getCustomFieldsOptions($id_bb_registration_fields, $id_lang = null)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        $sql = 'SELECT fv.*, fvl.*
            FROM ' . _DB_PREFIX_ . 'bb_registration_fields_values fv
            JOIN ' . _DB_PREFIX_ . 'bb_registration_fields_values_lang fvl
                ON(fv.field_value_id = fvl.field_value_id)
            WHERE fv.id_bb_registration_fields = ' .
            (int) $id_bb_registration_fields . ' AND fvl.id_lang = ' . (int) $id_lang;

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
    }

    public static function getUserLang($id_customer)
    {
        return (int) Db::getInstance()->getValue(
            'SELECT DISTINCT(`id_lang`) FROM `' . _DB_PREFIX_ . 'bb_registration_userdata`
            WHERE id_customer = ' . (int) $id_customer
        );
    }

    public static function addOption($id_bb_registration_fields)
    {
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'bb_registration_fields_values` (
            id_bb_registration_fields
            ) VALUES (' . pSQL($id_bb_registration_fields) . ')';
        if (Db::getInstance()->execute($sql)) {
            return Db::getInstance()->Insert_ID();
        }

        return false;
    }

    public static function addOptionValue($id_option, $id_lang, $value)
    {
        return Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'bb_registration_fields_values_lang`
            (
                field_value_id,
                id_lang,
                field_value
                ) VALUES (' . (int) $id_option . ', ' . (int) $id_lang . ', "' . pSQl($value) . '")'
        );
    }

    public static function getFormatedValue(
        $field,
        $id_lang = null,
        $id_customer = null,
        $id_guest = null
    ) {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $where = null;
        if ($id_customer) {
            $where = 'id_customer = ' . (int) $id_customer;
        }
        if ($id_guest) {
            $where = 'id_guest = ' . (int) $id_guest;
        }

        if (in_array(
            $field['field_type'],
            ['multiselect', 'radio', 'checkbox', 'select']
        )) {
            $options = self::getFileValues(
                $field['id_bb_registration_fields'],
                $where,
                'field_value_id'
            );
            if ($field['field_type'] == 'select') {
                return $options;
            }

            if (isset($options) && $options) {
                $options = explode(',', $options);
            }

            return (isset($options) && $options) ? $options : [];
        } elseif (in_array($field['field_type'], ['image', 'attachment'])) {
            $value = (self::getFileValues(
                $field['id_bb_registration_fields'],
                $where
            )) ? self::getFileValues(
                $field['id_bb_registration_fields'],
                $where
            ) : $field['default_value'];
            if ($value) {
                $value = __PS_BASE_URI__ .
                    str_replace(_PS_ROOT_DIR_ . '/', '', $value);
            }

            return $value;
        } else {
            $value = self::getFileValues(
                $field['id_bb_registration_fields'],
                $where
            ) ? self::getFileValues(
                $field['id_bb_registration_fields'],
                $where
            ) : $field['default_value'];

            return $value;
        }
    }

    public static function getFieldsValueById($id_value, $id_lang = null)
    {
        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        if (!$id_value) {
            return false;
        }

        return Db::getInstance()->getValue(
            'SELECT `field_value`
            FROM ' . _DB_PREFIX_ . 'bb_registration_fields_values_lang
            WHERE field_value_id = ' . (int) $id_value . ' AND `id_lang` = ' .
                (int) $id_lang
        );
    }

    public static function getOptionValue($options, $id_lang = null)
    {
        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        $result = Db::getInstance()->executeS('SELECT `field_value`
            FROM `' . _DB_PREFIX_ . 'bb_registration_fields_values_lang`
            WHERE `field_value_id` IN(' . $options . ')
            AND `id_lang` = ' . (int) $id_lang);

        if (isset($result)) {
            foreach ($result as &$res) {
                if (is_array($res)) {
                    $res = array_shift($res);
                }
            }
            $result = implode(',', $result);
        }

        return $result;
    }

    public static function removeOptionById($id_bb_registration_fields)
    {
        $res = Db::getInstance()->Execute('DELETE fv.*, fvl.*
            FROM `' . _DB_PREFIX_ . 'bb_registration_fields_values` fv
            JOIN `' . _DB_PREFIX_ . 'bb_registration_fields_values_lang` fvl
                ON (fv.field_value_id = fvl.field_value_id)
            WHERE fv.id_bb_registration_fields = ' .
            (int) $id_bb_registration_fields);
        if ($res) {
            return Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'bb_registration_userdata`
                WHERE id_bb_registration_fields = ' .
                (int) $id_bb_registration_fields);
        }
    }

    public static function getOptionsById($id_bb_registration_fields)
    {
        $sql = 'SELECT
        fv.`field_value_id`,
        fv.`id_bb_registration_fields`,
        fvl.`id_lang`,
        fvl.`field_value`
        FROM `' . _DB_PREFIX_ . 'bb_registration_fields_values` fv
        RIGHT JOIN `' . _DB_PREFIX_ . 'bb_registration_fields_values_lang` fvl
            ON (fv.field_value_id = fvl.field_value_id)
        WHERE fv.id_bb_registration_fields = ' .
            (int) $id_bb_registration_fields;

        $result = Db::getInstance()->ExecuteS($sql);
        $final = [];
        if ($result) {
            foreach ($result as $value) {
                $final[$value['field_value_id']][$value['id_lang']] = $value['field_value'];
            }
        }

        return $final;
    }

    public static function updateStatus($field, $id_bb_registration_fields)
    {
        return (bool) Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'bb_registration_fields`
            SET `' . bqSQL($field) . '` = !' . bqSQL($field) .
            ' WHERE id_bb_registration_fields = ' . (int) $id_bb_registration_fields);
    }

    public static function getFieldType($id_bb_registration_fields)
    {
        if (!$id_bb_registration_fields) {
            return false;
        } else {
            return (string) Db::getInstance()->getValue(
                'SELECT `field_type` FROM `' . _DB_PREFIX_ . 'bb_registration_fields`
                WHERE id_bb_registration_fields = ' . (int) $id_bb_registration_fields
            );
        }
    }

    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS(
            'SELECT `id_bb_registration_fields`, `position`
            FROM `' . _DB_PREFIX_ . 'bb_registration_fields`
            WHERE id_bb_registration_fields = ' . (int) Tools::getValue('id') . '
            ORDER BY `position` ASC'
        )) {
            return false;
        }

        foreach ($res as $field) {
            if ((int) $field['id_bb_registration_fields'] == (int) $this->id) {
                $moved_field = $field;
            }
        }

        if (!isset($moved_field) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'bb_registration_fields`
            SET `position`= `position` ' . ($way ? '- 1' : '+ 1') . '
            WHERE `position`
            ' . ($way
            ? '> ' . (int) $moved_field['position'] . ' AND `position` <= ' . (int) $position
            : '< ' . (int) $moved_field['position'] . ' AND `position` >= ' . (int) $position))
            && Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'bb_registration_fields`
            SET `position` = ' . (int) $position . '
            WHERE `id_bb_registration_fields` = ' .
                (int) $moved_field['id_bb_registration_fields']);
    }

    public static function getHigherPosition()
    {
        $sql = 'SELECT MAX(`position`) FROM `' . _DB_PREFIX_ . 'bb_registration_fields`';
        $position = DB::getInstance()->getValue($sql);

        return (is_numeric($position)) ? $position : -1;
    }

    public static function positionOccupied($position)
    {
        if (!$position) {
            return false;
        }

        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'bb_registration_fields` WHERE position = ' . (int) $position;

        return (bool) DB::getInstance()->getRow($sql);
    }

    public static function getFieldValue($id_bb_registration_fields, $id_customer = 0)
    {
        if (!$id_bb_registration_fields) {
            return false;
        }

        $and = '';
        if ($id_customer) {
            $and = ' AND id_customer = ' . (int) $id_customer;
        }

        return (string) Db::getInstance()->getValue('SELECT `value`
            FROM `' . _DB_PREFIX_ . 'bb_registration_userdata`
            WHERE id_bb_registration_fields = ' .
            (int) $id_bb_registration_fields . '
            AND id_customer = ' . (int) $id_customer);
    }

    public static function updateUserValueByFieldId(
        $id_bb_registration_fields,
        $id_customer,
        $value = ''
    ) {
        if (!$id_bb_registration_fields || !$id_customer) {
            return false;
        }

        return (bool) Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'bb_registration_userdata`
            SET `value` = "' . pSQL($value) . '",
                `field_value_id` = "' . pSQL($value) . '"
            WHERE id_bb_registration_fields = ' . (int) $id_bb_registration_fields . '
            AND id_customer = ' . (int) $id_customer);
    }

    public static function filterCustomers($filters = [])
    {
        if (!isset($filters) || !$filters) {
            return false;
        } else {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('customer');
            foreach ($filters as $key => $value) {
                if (isset($value) && $value) {
                    $sql->where('`' . bqSQL($key) . '` = "' . pSQL($value) . '"');
                }
            }

            return Db::getInstance()->executeS($sql);
        }
    }

    public static function getfilterFields($filters = [])
    {
        $result = [];
        if (!isset($filters) || !$filters) {
            return false;
        } else {
            $sql = new DbQuery();
            $sql->select('id_customer');
            $sql->from('bb_registration_userdata');
            foreach ($filters as $key => $value) {
                if (isset($value) && $value) {
                    $sql->where('id_bb_registration_fields = ' . (int) $key);
                    $sql->where('value = "' . pSQL($value) . '"');
                }
            }
            $res = Db::getInstance()->executeS($sql);
            if (isset($res) && $res) {
                foreach ($res as $r) {
                    $result[$r['id_customer']] = $r['id_customer'];
                }
            }

            return $result;
        }
    }

    public static function filterFields(
        $where = null,
        $id_lang = null,
        $order_by = null,
        $way = 'ASC'
    ) {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        $sql = 'SELECT
        a.*,
        b.`field_name`,
        val.`field_value_id`,
        val.`value`,
        fvl.`field_value`,
        val.`id_customer`,
        c.*
            FROM ' . _DB_PREFIX_ . 'customer c
            RIGHT JOIN ' . _DB_PREFIX_ . 'bb_registration_userdata val
                ON (c.id_customer = val.id_customer)
            RIGHT JOIN ' . _DB_PREFIX_ . 'bb_registration_fields_values_lang fvl
                ON (val.field_value_id = fvl.field_value_id AND fvl.id_lang = ' . (int) $id_lang . ')
            RIGHT JOIN ' . _DB_PREFIX_ . 'bb_registration_fields a
                ON (a.id_bb_registration_fields = val.id_bb_registration_fields)
            RIGHT JOIN ' . _DB_PREFIX_ . 'bb_registration_fields_lang b
                ON (a.id_bb_registration_fields = b.id_bb_registration_fields AND b.id_lang = ' . (int) $id_lang . ')
            WHERE a.active = 1 ' . (($where) ? ' AND ' .
            ($where) : '') . (($order_by) ? ' ORDER BY ' .
            $order_by . (($way) ? ' ' . $way : '') : '');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
    }

    public static function downloadAttachment($id_file, $id_customer = null)
    {
        $full_path = self::getFieldValue($id_file, $id_customer);
        self::actionDownload($full_path);
    }

    public static function actionDownload($full_path)
    {
        if (headers_sent()) {
            exit(Tools::displayError('Headers Sent'));
        }
        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

        if (file_exists($full_path)) {
            $fsize = filesize($full_path);
            $path_parts = pathinfo($full_path);
            $ext = Tools::strtolower($path_parts['extension']);
            switch ($ext) {
                case 'pdf':
                    $content_type = 'application/pdf';
                    break;
                case 'exe':
                    $content_type = 'application/octet-stream';
                    break;
                case 'zip':
                    $content_type = 'application/zip';
                    break;
                case 'doc':
                    $content_type = 'application/msword';
                    break;
                case 'xls':
                    $content_type = 'application/vnd.ms-excel';
                    break;
                case 'ppt':
                    $content_type = 'application/vnd.ms-powerpoint';
                    break;
                case 'gif':
                    $content_type = 'image/gif';
                    break;
                case 'png':
                    $content_type = 'image/png';
                    break;
                case 'jpeg':
                case 'jpg':
                    $content_type = 'image/jpg';
                    break;
                default:
                    $content_type = mime_content_type($full_path);
            }

            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: ' . $content_type);
            header('Content-Disposition: attachment; filename="' .
                basename($full_path) . '";');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . $fsize);
            ob_clean();
            flush();
            readfile($full_path);
        } else {
            exit(Tools::displayError('File Not Found'));
        }
    }

    public static function getGuestId($idCustomer)
    {
        if (!Validate::isUnsignedId($idCustomer)) {
            return false;
        }

        return (int) Db::getInstance()->getValue('SELECT `id_guest`
        FROM `' . _DB_PREFIX_ . 'guest` WHERE `id_customer` = ' .
            (int) $idCustomer);
    }

    public function updateGuestFields($id_guest, $id_customer)
    {
        if (!$id_guest || !$id_customer) {
            return false;
        }

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'bb_registration_userdata`
            SET `id_customer` = ' . (int) $id_customer . '
            WHERE id_guest = ' . (int) $id_guest;
        if (Db::getInstance()->execute($sql)) {
            $imgTypes = self::getFieldIdByType('image');
            $attachmentTypes = self::getFieldIdByType('attachment');
            $fileType = array_merge($imgTypes, $attachmentTypes);
            if (isset($fileType) && $fileType) {
                foreach ($fileType as $id_bb_registration_fields) {
                    $oldPath = self::getFileValues(
                        $id_bb_registration_fields,
                        'id_guest = ' . (int) $id_guest
                    );
                    if (isset($oldPath) && file_exists($oldPath)) {
                        $newName = _PS_UPLOAD_DIR_ .
                            Module::getInstanceByName('b2bregistration')->name .
                            DIRECTORY_SEPARATOR . $id_customer;
                        $newValue = $newName . DIRECTORY_SEPARATOR .
                            $id_bb_registration_fields . DIRECTORY_SEPARATOR .
                            pathinfo($oldPath, PATHINFO_BASENAME);

                        // resetting directories
                        $oldPath = str_replace(DIRECTORY_SEPARATOR .
                            $id_bb_registration_fields, '', realpath(dirname($oldPath)));
                        $newName = str_replace(DIRECTORY_SEPARATOR .
                            $id_guest, DIRECTORY_SEPARATOR . $id_customer, $oldPath);

                        if (self::updateFolder($oldPath, $newName)) {
                            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'bb_registration_userdata`
                                SET `value` = "' . pSQL($newValue) . '"
                                WHERE id_customer = ' . (int) $id_customer . '
                                AND id_bb_registration_fields = ' . (int) $id_bb_registration_fields);
                        }
                    }
                }
            }
        }
    }

    public static function getFieldIdByType($type)
    {
        if (!$type) {
            return false;
        } else {
            $result = [];
            $sql = new DbQuery();
            $sql->select('id_bb_registration_fields');
            $sql->from('bb_registration_fields');
            $sql->where('field_type = "' . pSQL($type) . '"');

            $res = Db::getInstance()->executeS($sql);
            if (isset($res) && $res) {
                foreach ($res as $r) {
                    $result[] = $r['id_bb_registration_fields'];
                }
            }

            return $result;
        }
    }

    public static function getFileValues(
        $id_bb_registration_fields,
        $and = null,
        $field = 'value'
    ) {
        if (!$id_bb_registration_fields) {
            return false;
        } else {
            $sql = new DbQuery();
            $sql->select($field);
            $sql->from('bb_registration_userdata');
            $sql->where('id_bb_registration_fields = ' . (int) $id_bb_registration_fields);
            if ($and && !empty($and)) {
                $sql->where($and);
            }

            return Db::getInstance()->getValue($sql);
        }
    }

    public static function updateFolder($oldName, $newName)
    {
        return rename($oldName, $newName);
    }

    public static function deleteByCustomer($id_customer)
    {
        if (!$id_customer) {
            return false;
        } else {
            $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'bb_registration_userdata`
            WHERE id_customer = ' . (int) $id_customer;
            if (Db::getInstance()->execute($sql)) {
                $dirname = _PS_UPLOAD_DIR_ .
                    Module::getInstanceByName('b2bregistration')->name .
                    DIRECTORY_SEPARATOR . $id_customer;

                return self::deleteDir($dirname);
            } else {
                return false;
            }
        }
    }

    public static function deleteDir($dirname)
    {
        $dir_handle = null;
        if (is_dir($dirname)) {
            $dir_handle = opendir($dirname);
        }
        if (!$dir_handle) {
            return false;
        }

        while ($file = readdir($dir_handle)) {
            if ($file != '.' && $file != '..') {
                if (!is_dir($dirname . DIRECTORY_SEPARATOR . $file)) {
                    @unlink($dirname . DIRECTORY_SEPARATOR . $file);
                } else {
                    self::deleteDir($dirname . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        closedir($dir_handle);
        @rmdir($dirname);

        return true;
    }

    public static function getFormatedValueCustomerBase(
        $field,
        $id_customer,
        $id_lang = null,
        $id_guest = null
    ) {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        $where = null;
        if ($id_customer) {
            $where = 'id_customer = ' . (int) $id_customer;
        }
        if ($id_guest) {
            $where = 'id_guest = ' . (int) $id_guest;
        }

        if (in_array(
            $field['field_type'],
            ['multiselect', 'radio', 'checkbox', 'select']
        )) {
            $options = self::getFileValues(
                $field['id_bb_registration_fields'],
                $where,
                'field_value_id'
            );
            if ($field['field_type'] == 'select') {
                return $options;
            }

            if (isset($options) && $options) {
                $options = explode(',', $options);
            }

            return (isset($options) && $options) ? $options : [];
        } elseif (in_array($field['field_type'], ['image', 'attachment'])) {
            $value = (self::getFileValues(
                $field['id_bb_registration_fields'],
                $where
            )) ? self::getFileValues(
                $field['id_bb_registration_fields'],
                $where
            ) : $field['default_value'];
            if ($value) {
                $value = __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', $value);
            }

            return $value;
        } else {
            $value = self::getFileValues(
                $field['id_bb_registration_fields'],
                $where
            ) ? self::getFileValues(
                $field['id_bb_registration_fields'],
                $where
            ) : $field['default_value'];

            return $value;
        }
    }

    public static function getFieldsCollection(
        $id_lang = null,
        $order_by = null,
        $way = 'ASC'
    ) {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $sql = 'SELECT a.*, b.`field_name`, b.`default_value`
            FROM ' . _DB_PREFIX_ . 'bb_registration_fields a
            LEFT JOIN ' . _DB_PREFIX_ . 'bb_registration_fields_lang b
                ON (a.id_bb_registration_fields = b.id_bb_registration_fields and b.id_lang = ' . (int) $id_lang . ')
            WHERE a.active = 1' . (($order_by) ? ' ORDER BY ' .
            $order_by . (($way) ? ' ' . $way : '') : '');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
    }

    public static function getDependantCheckedVal($id)
    {
        return (int) Db::getInstance()->getValue('SELECT `dependant_field`
        FROM `' . _DB_PREFIX_ . 'bb_registration_fields`
        WHERE `id_bb_registration_fields` = ' . (int) $id);
    }

    public static function getDependantCheckedValue($id)
    {
        return (int) Db::getInstance()->getValue('SELECT `dependant_value`
        FROM `' . _DB_PREFIX_ . 'bb_registration_fields`
        WHERE `id_bb_registration_fields` = ' . (int) $id);
    }

    public static function renameTable($old_name, $new_name)
    {
        return (bool) Db::getInstance()->execute('ALTER TABLE ' .
            _DB_PREFIX_ . pSQL($old_name) . ' RENAME ' . _DB_PREFIX_ . pSQL($new_name));
    }

    public static function tableExists($table)
    {
        return (bool) Db::getInstance()->executeS('SHOW TABLES LIKE \'' .
            _DB_PREFIX_ . pSQL($table) . '\'');
    }

    public static function alterPKey($table, $auto_increment = false)
    {
        $sql = 'ALTER TABLE ' . _DB_PREFIX_ . bqSQL($table) .
            ' CHANGE `id_custom_field` `id_bb_registration_fields` INT( 11 ) NOT NULL';
        if ($auto_increment) {
            $sql .= ' AUTO_INCREMENT';
        }

        return (bool) Db::getInstance()->execute($sql);
    }

    public static function keyExists($table, $column)
    {
        $columns = Db::getInstance()->executeS('SELECT COLUMN_NAME FROM information_schema.columns
            WHERE table_schema = "' . _DB_NAME_ .
            '" AND table_name = "' . _DB_PREFIX_ . bqSQL($table) . '"');

        if (isset($columns) && $columns) {
            foreach ($columns as $col) {
                if ($col['COLUMN_NAME'] == $column) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function populateSingularEntry($table, $field_key, $value)
    {
        Db::getInstance()->insert(
            $table,
            [$field_key => pSQL($value)]
        );

        return (int) Db::getInstance()->Insert_ID();
    }

    public static function dropSingularEntry($table, $field_key, $value)
    {
        return Db::getInstance()->delete(
            $table,
            $field_key . ' = ' . (int) $value
        );
    }

    public static function getSingularEntry($table, $field_key, $value, $id)
    {
        $sql = new DbQuery();
        $sql->select($id);
        $sql->from($table);
        $sql->where($field_key . ' = ' . (int) $value);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function populateMultiEntry(
        $table,
        $first_field_key,
        $first_value,
        $sec_field_key,
        $sec_value
    ) {
        Db::getInstance()->insert(
            $table,
            [
                $first_field_key => pSQL($first_value),
                $sec_field_key => pSQL($sec_value),
            ]
        );
        $last_id = (int) Db::getInstance()->Insert_ID();

        return $last_id;
    }

    public static function getHeadingsCollection($id_lang)
    {
        $sql = new DbQuery();
        $sql->select('a.`id_bb_registration_fields_headings`,b.`title`');
        $sql->from('bb_registration_fields_headings', 'a');
        $sql->leftJoin(
            'bb_registration_fields_headings_lang',
            'b',
            'a.`id_bb_registration_fields_headings` = b.`id_bb_registration_fields_headings`'
        );
        $sql->where('b.`id_lang` = ' . (int) $id_lang);

        return Db::getInstance()->executeS($sql);
    }

    public static function getSubHeading($id, $id_lang)
    {
        $sql = new DbQuery();
        $sql->select('`title`');
        $sql->from('bb_registration_fields_headings_lang');
        $sql->where('`id_bb_registration_fields_headings` = ' .
            (int) $id . ' AND `id_lang` = ' . (int) $id_lang);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getProfiles($id_lang)
    {
        $sql = new DbQuery();
        $sql->select('a.`id_b2b_profile`,b.`b2b_profile_name`');
        $sql->from('b2b_profile', 'a');
        $sql->leftJoin(
            'b2b_profile_lang',
            'b',
            'a.`id_b2b_profile` = b.`id_b2b_profile`'
        );
        $sql->where('b.`id_lang` = ' . (int) $id_lang);

        return Db::getInstance()->executeS($sql);
    }
}

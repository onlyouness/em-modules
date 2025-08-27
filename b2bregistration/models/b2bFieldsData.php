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
 * @copyright Copyright 2022 © fmemodules All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  FMM Modules
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class BToBFieldsData extends ObjectModel
{
    public $id_b2b_fields_data;
    public $b2b_field_name;
    public $b2b_field_title;
    public $id_field;
    public $id_customer;

    public static $definition = [
        'table' => 'b2b_fields_data',
        'primary' => 'id_b2b_fields_data',
        'fields' => [
            'id_b2b_fields_data' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'b2b_field_name' => ['type' => self::TYPE_STRING],
            'b2b_field_title' => ['type' => self::TYPE_STRING],
            'id_customer' => ['type' => self::TYPE_INT],
            'id_field' => ['type' => self::TYPE_INT],
        ],
    ];

    public static function getCustomFieldsData($id_customer)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('b2b_fields_data', 'bd');
        $sql->leftJoin('b2b_custom_fields', 'f', 'f.`id_b2b_custom_fields` = bd.`id_field`');
        $sql->where('f.active =1');
        $sql->where('bd.id_customer =' . (int) $id_customer);

        return Db::getInstance()->ExecuteS($sql);
    }

    public static function customFieldsDeletion($id_customer)
    {
        return Db::getInstance()->delete('b2b_fields_data', 'id_customer =' . (int) $id_customer);
    }
}

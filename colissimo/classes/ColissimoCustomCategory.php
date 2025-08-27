<?php
/**
 * 2007-2024 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2024 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class ColissimoCustomCategory
 */
class ColissimoCustomCategory extends ObjectModel
{
    /** @var int */
    public $id_category;

    /** @var string */
    public $short_desc;

    /** @var int */
    public $id_country_origin;

    /** @var string */
    public $hs_code;

    /** @var array */
    public static $definition = [
        'table' => 'colissimo_custom_category',
        'primary' => 'id_colissimo_custom_category',
        'fields' => [
            'id_category' => ['type' => self::TYPE_INT, 'required' => false],
            'short_desc' => ['type' => self::TYPE_STRING, 'required' => false, 'size' => 64],
            'id_country_origin' => ['type' => self::TYPE_INT, 'required' => false],
            'hs_code' => ['type' => self::TYPE_STRING, 'required' => false, 'size' => 10],
        ],
    ];

    /**
     * @var array $webserviceParameters
     */
    protected $webserviceParameters = [
        'objectsNodeName' => 'colissimo_custom_categories',
        'fields' => [
            'id_category' => ['xlink_resource' => 'categories'],
            'id_country_origin' => ['xlink_resource' => 'countries'],
        ],
    ];

    /**
     * @param int $idCategory
     * @return ColissimoCustomCategory
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getByIdCategory($idCategory)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_custom_category')
            ->from('colissimo_custom_category')
            ->where('id_category = ' . (int) $idCategory);
        $id = Db::getInstance(_PS_USE_SQL_SLAVE_)
            ->getValue($dbQuery);

        return new self((int) $id);
    }
}

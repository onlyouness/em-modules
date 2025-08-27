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
 * Class ColissimoPickupPoint
 */
class ColissimoPickupPoint extends ObjectModel
{
    /** @var int */
    public $id_colissimo_pickup_point;

    /** @var string */
    public $colissimo_id;

    /** @var string */
    public $company_name;

    /** @var string */
    public $address1;

    /** @var string */
    public $address2;

    /** @var string */
    public $address3;

    /** @var string */
    public $city;

    /** @var string */
    public $zipcode;

    /** @var string */
    public $country;

    /** @var string */
    public $iso_country;

    /** @var string */
    public $product_code;

    /** @var string */
    public $network;

    /** @var string */
    public $date_add;

    /** @var string */
    public $date_upd;

    // @formatter:off
    /** @var array */
    public static $definition = [
        'table'   => 'colissimo_pickup_point',
        'primary' => 'id_colissimo_pickup_point',
        'fields'  => [
            'colissimo_id' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 8],
            'company_name' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 64],
            'address1'     => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 120],
            'address2'     => ['type' => self::TYPE_STRING, 'required' => false, 'size' => 120],
            'address3'     => ['type' => self::TYPE_STRING, 'required' => false, 'size' => 120],
            'city'         => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 80],
            'zipcode'      => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 10],
            'country'      => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 64],
            'iso_country'  => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 2],
            'product_code' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 3],
            'network'      => ['type' => self::TYPE_STRING, 'required' => false, 'size' => 10],
            'date_add'     => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd'     => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
     ];

    /** @var array Countries that allow pickup point (25 ISO) */
    public static $availableIso = ['IE', 'DK', 'IT', 'AD', 'AT', 'BE', 'DE', 'ES', 'FR', 'EE', 'HU', 'LT', 'LU', 'LV', 'MC', 'NL', 'PL', 'PT', 'SE', 'DK', 'FI', 'CZ', 'SK', 'SI'];

    /** @var array Available languages in the popup (7 languages) */
    public static $availableLanguages = ['fr', 'en', 'es', 'it', 'pt', 'nl', 'de'];

    /** @var array Product codes NOT TO USE -- Instead use BPR */
    public static $BPRAliases = ['ACP', 'CDI'];
    // formatter:on

    /**
     * @param string $colissimoId
     * @return ColissimoPickupPoint
     */
    public static function getPickupPointByIdColissimo($colissimoId)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_pickup_point')
                ->from('colissimo_pickup_point')
                ->where('colissimo_id = "'.pSQL($colissimoId).'"');
        $id = Db::getInstance(_PS_USE_SQL_SLAVE_)
                ->getValue($dbQuery);

        return new self((int) $id);
    }

    /**
     * @return string
     */
    public function getProductCodeForAffranchissement()
    {
        if (in_array($this->product_code, self::$BPRAliases)) {
            return 'BPR';
        }

        return $this->product_code;
    }
}

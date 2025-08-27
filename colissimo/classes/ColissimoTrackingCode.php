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
 * Class ColissimoTrackingCode
 */
class ColissimoTrackingCode extends ObjectModel
{
    const TYPO_DELIVERED = 'LIV';
    const TYPO_DELIVERED_PICKUP_POINT = 'LIVPICKUP';
    const TYPO_SHIPPED = 'SHI';
    const TYPO_ANOMALY = 'ANO';
    const EVENT_WAITING_SHIPMENT_HANDLING = 'COMCFM';

    /** @var int */
    public $id_colissimo_tracking_code;

    /** @var string */
    public $clp_code;

    /** @var string */
    public $inovert_code;

    /** @var string Typology of the event ("LIV" / "ANO"...) */
    public $typology;

    /** @var string */
    public $internal_text;

    /** @var array */
    public static $definition = [
        'table' => 'colissimo_tracking_code',
        'primary' => 'id_colissimo_tracking_code',
        'fields' => [
            'clp_code' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 10,
            ],
            'inovert_code' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => false,
                'size' => 10,
            ],
            'typology' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => false,
                'size' => 10,
            ],
            'internal_text' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 255,
            ],
        ],
    ];

    /**
     * @param string $inovertCode
     * @return mixed
     */
    public static function getTypologyByInovertCode($inovertCode)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('typology')
            ->from('colissimo_tracking_code')
            ->where('inovert_code = "' . pSQL($inovertCode) . '"');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)
            ->getValue($dbQuery);
    }

    /**
     * @param string $clpCode
     * @return ColissimoTrackingCode
     */
    public static function getByClpCode($clpCode)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_tracking_code')
            ->from('colissimo_tracking_code')
            ->where('clp_code = "' . pSQL($clpCode) . '"');

        $id = Db::getInstance(_PS_USE_SQL_SLAVE_)
            ->getValue($dbQuery);

        return new self((int) $id);
    }
}

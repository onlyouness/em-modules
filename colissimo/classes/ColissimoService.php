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
 * Class ColissimoService
 */
class ColissimoService extends ObjectModel
{
    const TYPE_RELAIS = 'RELAIS';
    const TYPE_RETOUR = 'RETOUR';
    const TYPE_SIGN = 'AVEC_SIGNATURE';
    const TYPE_NOSIGN = 'SANS_SIGNATURE';
    const TYPE_ECO_OM = 'ECO_OUTRE_MER';

    /** @var int */
    public $id_carrier;

    /** @var string */
    public $product_code;

    /** @var string */
    public $commercial_name;

    /** @var string Destination of the service (FRANCE, EUROPE, OM, WORLDWIDE) */
    public $destination_type;

    /** @var bool Flag to indicate if the service requires signature at the delivery */
    public $is_signature;

    /** @var bool Flag to indicate if the delivery is in Pickup-point */
    public $is_pickup;

    /** @var bool Flag to indicate if the service is related to return shipments */
    public $is_return;

    /** @var string */
    public $type;

    /** @var array */
    public static $definition = [
        'table' => 'colissimo_service',
        'primary' => 'id_colissimo_service',
        'fields' => [
            'id_carrier' => ['type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId'],
            'product_code' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 5],
            'commercial_name' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 50],
            'destination_type' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 10],
            'is_signature' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'is_pickup' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'is_return' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'type' => ['type' => self::TYPE_STRING, 'required' => true],
        ],
    ];

    /** @var array $insurableProducts */
    public static $insurableProducts = ['COL', 'BPR', 'A2P', 'CDS', 'CORE', 'CORI', 'COLI'];

    /** @var array $unavailableMachinableProductCodes */
    public static $unavailableMachinableProductCodes = ['BPR', 'A2P', 'CMT'];

    /**
     * @param string $isoCode
     * @return bool
     */
    public function isInsurable($isoCode)
    {
        // @formatter:off
        if ($isoCode !== 'AN') {
            return true;
        }
        // @formatter:off

        return false;
    }

    /**
     * @return bool
     */
    public function isMachinableOptionAvailable()
    {
        return !in_array($this->product_code, self::$unavailableMachinableProductCodes);
    }

    /**
     * @param bool $excludeReturn
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public static function getAll($excludeReturn = true)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('*')
                ->from('colissimo_service');
        if ($excludeReturn) {
            $dbQuery->where('is_return = 0');
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)
                 ->executeS($dbQuery);
    }

    /**
     * @param int    $idCarrierReference
     * @param string $destinationType
     * @return int
     */
    public static function getServiceIdByIdCarrierDestinationType($idCarrierReference, $destinationType)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_service')
                ->from('colissimo_service')
                ->where(
                    'id_carrier = '.(int) $idCarrierReference.' AND destination_type = "'.pSQL($destinationType).'"'
                );

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)
                       ->getValue($dbQuery);
    }

    /**
     * @param string $productCode
     * @param string $destinationType
     * @return int
     */
    public static function getServiceIdByProductCodeDestinationType($productCode, $destinationType)
    {
        // @formatter:off
        if (in_array($productCode, ColissimoPickupPoint::$BPRAliases)) {
            $productCode = 'BPR';
        }
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_service')
                ->from('colissimo_service')
                ->where('product_code = "'.pSQL($productCode).'" AND destination_type = "'.pSQL($destinationType).'"');
        // @formatter:on

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)
            ->getValue($dbQuery);
    }

    /**
     * @param int $idCarrier
     * @return false|null|string
     */
    public static function getServiceTypeByIdCarrier($idCarrier)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('type')
            ->from('colissimo_service')
            ->where('id_carrier = ' . (int) $idCarrier);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)
            ->getValue($dbQuery);
    }

    /**
     * @param string $destinationType
     * @param bool $includeReturn
     * @return array|false|string|null
     * @throws PrestaShopDatabaseException
     */
    public static function getServiceIdsByDestinationType($destinationType, $includeReturn = false)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_service')
            ->from('colissimo_service')
            ->where('destination_type = "' . pSQL($destinationType) . '"');
        if (!$includeReturn) {
            $dbQuery->where('is_return = 0');
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)
            ->executeS($dbQuery);
        if ($result && is_array($result)) {
            return array_map(
                function ($element) {
                    return $element['id_colissimo_service'];
                },
                $result
            );
        }

        return $result;
    }

    /**
     * @param string $isoCountryCustomer
     * @param array $accountType
     * @return bool
     */
    public function isEligibleToAccount($isoCountryCustomer, $accountType)
    {
        $availableIso = ColissimoPickupPoint::$availableIso;
        // @formatter:off
        $destinationWorldWideEU = array(ColissimoTools::DEST_EU, ColissimoTools::DEST_WORLD);
        if (in_array($this->destination_type, $destinationWorldWideEU) && !isset($accountType[$this->destination_type])) {
            return false;
        }
        // @formatter:on
        if ($this->type == ColissimoService::TYPE_RELAIS) {
            if (!in_array($isoCountryCustomer, $availableIso)) {
                return false;
            }
        }
        if ($this->destination_type == ColissimoTools::DEST_EU &&
            $this->is_signature == 0 &&
            $this->type != ColissimoService::TYPE_RELAIS
        ) {
            if (!in_array($isoCountryCustomer, ColissimoTools::$isoEUCountriesZone1Zone3)) {
                return false;
            }
        }
        if ($this->destination_type == ColissimoTools::DEST_OM) {
            if (!isset($accountType[$this->destination_type])) {
                return false;
            }
            if ($this->product_code == 'ECO' &&
                !isset($accountType['ECOOM'])
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int $id
     * @return false|string|null
     */
    public static function getServiceTypeById($id)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('type')
            ->from('colissimo_service')
            ->where('id_colissimo_service = ' . (int) $id);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)
            ->getValue($dbQuery);
    }
}

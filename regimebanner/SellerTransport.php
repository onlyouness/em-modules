<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    JA Modules <info@jamodules.com>
 * @copyright Since 2007 JA Modules
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class SellerTransport extends ObjectModel
{
    public $id_seller;
    public $id_carrier;

    public const PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE = 4;

    /**
     * Get all carriers in a given language
     *
     * @param int $id_lang Language id
     * @param $modules_filters, possible values:
     * @param bool $active Returns only active carriers when true
     *
     * @return array Carriers
     */
    public static function getCarriers($id_lang, $active = false, $id_seller = 0, $delete = false, $id_zone = false, $ids_group = null)
    {
        // Filter by groups and no groups => return empty array
        /*if ($ids_group && (!is_array($ids_group) || !count($ids_group))) {
            return [];
        }*/

        $sql_groups = ' = 1';
        if (is_array($ids_group) && count($ids_group) > 0) {
            $sql_groups = 'IN(';
            foreach ($ids_group as $id_group) {
                $sql_groups .= $id_group . ',';
            }
            $sql_groups = substr($sql_groups, 0, -1);
            $sql_groups .= ')';
        }

        $sql = 'SELECT c.*, cl.delay
        FROM `' . _DB_PREFIX_ . 'carrier` c
        LEFT JOIN `' . _DB_PREFIX_ . 'carrier_lang` cl ON (c.`id_carrier` = cl.`id_carrier` AND cl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('cl') . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'carrier_zone` cz ON (cz.`id_carrier` = c.`id_carrier`)' .
        ($id_zone ? 'LEFT JOIN `' . _DB_PREFIX_ . 'zone` z ON (z.`id_zone` = ' . (int) $id_zone . ')' : '') . '
        ' . Shop::addSqlAssociation('carrier', 'c') . '
        LEFT JOIN `' . _DB_PREFIX_ . 'seller_carrier` sc ON (sc.`id_carrier` = c.`id_carrier`)
        WHERE c.`deleted` = ' . ($delete ? '1' : '0');
        if ($active) {
            $sql .= ' AND c.`active` = 1 ';
        }
        if ($id_zone) {
            $sql .= ' AND cz.`id_zone` = ' . (int) $id_zone . ' AND z.`active` = 1 ';
        }
        if ($ids_group) {
            $sql .= ' AND c.id_carrier IN (SELECT id_carrier FROM ' . _DB_PREFIX_ . 'carrier_group WHERE id_group ' . $sql_groups . ') ';
        }

        /*switch ($modules_filters) {
            case 1 :
                $sql .= ' AND c.is_module = 0 ';
                break;
            case 2 :
                $sql .= ' AND c.is_module = 1 ';
                break;
            case 3 :
                $sql .= ' AND c.is_module = 1 AND c.need_range = 1 ';
                break;
            case 4 :
                $sql .= ' AND (c.is_module = 0 OR c.need_range = 1) ';
                break;
        }*/

        $sql .= ' AND sc.`id_seller` = ' . (int) $id_seller;
        $sql .= ' GROUP BY c.`id_carrier` ORDER BY c.`position` ASC';

        $carriers = Db::getInstance()->executeS($sql);
        Tools::dieObject($sql);
        foreach ($carriers as $key => $carrier) {
            if ($carrier['name'] == '0') {
                $carriers[$key]['name'] = Configuration::get('PS_SHOP_NAME');
            }
        }

        return $carriers;
    }

    /**
     * @param int $id_zone
     * @param array $groups group of the customer
     * @param array &$error contain an error message if an error occurs
     *
     * @return array
     */
    public static function getCarriersForOrder($id_seller, $id_zone, $groups = null, $cart = null, &$error = [])
    {
        $context = Context::getContext();
        $id_lang = $context->language->id;
        if (is_null($cart)) {
            $cart = $context->cart;
        }
        if (isset($context->currency)) {
            $id_currency = $context->currency->id;
        }

        if (is_array($groups) && !empty($groups)) {
            $result = SellerTransport::getCarriers($id_lang, true, $id_seller, false, (int) $id_zone, $groups, self::PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
        } else {
            $result = SellerTransport::getCarriers($id_lang, true, $id_seller, false, (int) $id_zone, [Configuration::get('PS_UNIDENTIFIED_GROUP')], self::PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
        }
        $results_array = [];

        foreach ($result as $k => $row) {
            $carrier = new Carrier((int) $row['id_carrier']);
            $shipping_method = $carrier->getShippingMethod();
            if ($shipping_method != Carrier::SHIPPING_METHOD_FREE) {
                // Get only carriers that are compliant with shipping method
                if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && $carrier->getMaxDeliveryPriceByWeight($id_zone) === false) {
                    $error[$carrier->id] = Carrier::SHIPPING_WEIGHT_EXCEPTION;
                    unset($result[$k]);
                    continue;
                }
                if ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && $carrier->getMaxDeliveryPriceByPrice($id_zone) === false) {
                    $error[$carrier->id] = Carrier::SHIPPING_PRICE_EXCEPTION;
                    unset($result[$k]);
                    continue;
                }

                // If out-of-range behavior carrier is set on "Desactivate carrier"
                if ($row['range_behavior']) {
                    // Get id zone
                    if (!$id_zone) {
                        // $id_zone = (int)Country::getIdZone(Country::getDefaultCountryId());
                        $id_zone = (int) Country::getIdZone(Configuration::get('PS_COUNTRY_DEFAULT'));
                    }

                    // Get only carriers that have a range compatible with cart
                    if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT
                        && (!Carrier::checkDeliveryPriceByWeight($row['id_carrier'], $cart->getTotalWeight(), $id_zone))) {
                        $error[$carrier->id] = Carrier::SHIPPING_WEIGHT_EXCEPTION;
                        unset($result[$k]);
                        continue;
                    }

                    if ($shipping_method == Carrier::SHIPPING_METHOD_PRICE
                        && (!Carrier::checkDeliveryPriceByPrice($row['id_carrier'], $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING), $id_zone, $id_currency))) {
                        $error[$carrier->id] = Carrier::SHIPPING_PRICE_EXCEPTION;
                        unset($result[$k]);
                        continue;
                    }
                }
            }

            $row['name'] = $row['name'] != '0' ? $row['name'] : Carrier::getCarrierNameFromShopName();
            $row['price'] = (($shipping_method == Carrier::SHIPPING_METHOD_FREE) ? 0 : $cart->getPackageShippingCost((int) $row['id_carrier'], true, null, null, $id_zone));
            $row['price_tax_exc'] = (($shipping_method == Carrier::SHIPPING_METHOD_FREE) ? 0 : $cart->getPackageShippingCost((int) $row['id_carrier'], false, null, null, $id_zone));
            $row['img'] = file_exists(_PS_SHIP_IMG_DIR_ . (int) $row['id_carrier']) . '.jpg' ? _THEME_SHIP_DIR_ . (int) $row['id_carrier'] . '.jpg' : '';

            // If price is false, then the carrier is unavailable (carrier module)
            if ($row['price'] === false) {
                unset($result[$k]);
                continue;
            }
            $results_array[] = $row;
        }

        // if we have to sort carriers by price
        $prices = [];
        if (Configuration::get('PS_CARRIER_DEFAULT_SORT') == Carrier::SORT_BY_PRICE) {
            foreach ($results_array as $r) {
                $prices[] = $r['price'];
            }
            if (Configuration::get('PS_CARRIER_DEFAULT_ORDER') == Carrier::SORT_BY_ASC) {
                array_multisort($prices, SORT_ASC, SORT_NUMERIC, $results_array);
            } else {
                array_multisort($prices, SORT_DESC, SORT_NUMERIC, $results_array);
            }
        }

        return $results_array;
    }

    public static function getAllSellerCarriers($id_lang, $active = false, $delete = false, $id_zone = false, $ids_group = null)
    {
        // Filter by groups and no groups => return empty array
        /*if ($ids_group && (!is_array($ids_group) || !count($ids_group))) {
            return [];
        }*/

        $sql_groups = ' = 1';
        if (is_array($ids_group) && count($ids_group) > 0) {
            $sql_groups = 'IN(';
            foreach ($ids_group as $id_group) {
                $sql_groups .= $id_group . ',';
            }
            $sql_groups = substr($sql_groups, 0, -1);
            $sql_groups .= ')';
        }

        $sql = 'SELECT c.*, cl.delay
        FROM `' . _DB_PREFIX_ . 'carrier` c
        LEFT JOIN `' . _DB_PREFIX_ . 'carrier_lang` cl ON (c.`id_carrier` = cl.`id_carrier` AND cl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('cl') . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'carrier_zone` cz ON (cz.`id_carrier` = c.`id_carrier`)' .
        ($id_zone ? 'LEFT JOIN `' . _DB_PREFIX_ . 'zone` z ON (z.`id_zone` = ' . (int) $id_zone . ')' : '') . '
        ' . Shop::addSqlAssociation('carrier', 'c') . '
        LEFT JOIN `' . _DB_PREFIX_ . 'seller_carrier` sc ON (sc.`id_carrier` = c.`id_carrier`)
        WHERE c.`deleted` = ' . ($delete ? '1' : '0');
        if ($active) {
            $sql .= ' AND c.`active` = 1 ';
        }
        if ($id_zone) {
            $sql .= ' AND cz.`id_zone` = ' . (int) $id_zone . ' AND z.`active` = 1 ';
        }
        if ($ids_group) {
            $sql .= ' AND c.id_carrier IN (SELECT id_carrier FROM ' . _DB_PREFIX_ . 'carrier_group WHERE id_group ' . $sql_groups . ') ';
        }

        $sql .= ' AND sc.`id_seller` != 0';
        $sql .= ' GROUP BY c.`id_carrier` ORDER BY c.`position` ASC';

        $carriers = Db::getInstance()->executeS($sql);
        foreach ($carriers as $key => $carrier) {
            if ($carrier['name'] == '0') {
                $carriers[$key]['name'] = Configuration::get('PS_SHOP_NAME');
            }
        }

        return $carriers;
    }

    public static function isCarrierSeller($id_carrier)
    {
        return Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'seller_carrier 
            WHERE id_carrier = ' . (int) $id_carrier
        );
    }

    public static function isSellerCarrier($id_seller, $id_carrier)
    {
        return Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'seller_carrier 
            WHERE id_seller = ' . (int) $id_seller . ' AND id_carrier = ' . (int) $id_carrier
        );
    }

    public static function updateSellerCarrier($id_carrier_old, $id_carrier)
    {
        Db::getInstance()->Execute(
            'UPDATE ' . _DB_PREFIX_ . 'seller_carrier 
            SET id_carrier = ' . (int) $id_carrier . ' 
            WHERE id_carrier = ' . (int) $id_carrier_old
        );
    }

    public static function getNumCarriersBySeller($id_seller)
    {
        return Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'seller_carrier sc 
            LEFT JOIN ' . _DB_PREFIX_ . 'carrier c ON (sc.id_carrier = c.id_carrier)
            WHERE deleted = 0 AND id_seller = ' . (int) $id_seller
        );
    }
}

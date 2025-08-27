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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2023 PrestaShop SA
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class B2BVatNumber extends TaxManagerModule
{
    public static function getCountryPrefixIso()
    {
        $fmm_prefixes = [
            'AT' => 'AT',
            'BE' => 'BE',
            'DK' => 'DK',
            'FI' => 'FI',
            'FR' => 'FR',
            'FX' => 'FR',
            'DE' => 'DE',
            'GR' => 'EL',
            'IE' => 'IE',
            'IT' => 'IT',
            'LU' => 'LU',
            'NL' => 'NL',
            'PT' => 'PT',
            'ES' => 'ES',
            'SE' => 'SE',
            'GB' => 'GB',
            'CY' => 'CY',
            'EE' => 'EE',
            'HU' => 'HU',
            'LV' => 'LV',
            'LT' => 'LT',
            'MT' => 'MT',
            'PL' => 'PL',
            'SK' => 'SK',
            'CZ' => 'CZ',
            'SI' => 'SI',
            'RO' => 'RO',
            'BG' => 'BG',
            'HR' => 'HR',
        ];

        return $fmm_prefixes;
    }

    public static function fmmWebServiceCheck($vat_number)
    {
        if (empty($vat_number)) {
            return [];
        }
        $enable_vat = (int) Configuration::get(
            'B2BREGISTRATION_ENABLE_DISABLE_VAT_VALIDATION',
            false,
            Context::getContext()->shop->id_shop_group,
            Context::getContext()->shop->id
        );
        if ($enable_vat == 1) {
            $vat_number = str_replace(' ', '', $vat_number);
            $prefix = Tools::substr($vat_number, 0, 2);
            if (array_search($prefix, self::getCountryPrefixIso()) === false) {
                return 2;
            }
            $vat = Tools::substr($vat_number, 2);
            $client = new SoapClient(
                'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl'
            );
            $check_vat = $client->checkVat([
                'countryCode' => $prefix,
                'vatNumber' => $vat,
            ]);
            if ($check_vat->valid == true) {
                $flag = 1;
            } else {
                $flag = 0;
            }

            return $flag;
        }
    }
}

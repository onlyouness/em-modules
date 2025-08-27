<?php
/**
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author     PrestaShop SA <contact@prestashop.com>
 * @copyright  2007-2024 PrestaShop SA
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Upgrade to 2.1.0
 *
 * @param Colissimo $module
 * @return bool
 * @throws PrestaShopDatabaseException
 * @throws PrestaShopException
 */
function upgrade_module_2_1_0($module)
{
    Configuration::updateValue('COLISSIMO_WIDGET_REMOTE_MOBILE', 1);
    Configuration::updateValue('COLISSIMO_WIDGET_ENDPOINT_MOBILE', 'https://ws.colissimo.fr/widget-point-retrait/rest/authenticate.rest');
    Configuration::updateValue('COLISSIMO_WIDGET_COLOR_1_MOBILE', '#333333');
    Configuration::updateValue('COLISSIMO_WIDGET_COLOR_2_MOBILE', '#EA690A');
    Configuration::updateValue('COLISSIMO_WIDGET_FONT_MOBILE', 'Arial');
    Configuration::updateValue('COLISSIMO_WIDGET_NATIVE', 1);
    Configuration::updateValue('COLISSIMO_WIDGET_NATIVE_MOBILE', 1);
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_MAP_MODAL', 1);
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_MAP_MODAL_MOBILE', 1);
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_TYPE_POINT', '');
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_TYPE_POINT_MOBILE', '');
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_BPR', 0);
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_BPR_MOBILE', 0);
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_A2P', 0);
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_A2P_MOBILE', 0);
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_CMT', 0);
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_CMT_MOBILE', 0);
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_PCS', 0);
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_PCS_MOBILE', 0);
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_BDP', 0);
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_BDP_MOBILE', 0);
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_NUMBER_POINT', 20);
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_NUMBER_POINT_MOBILE', 10);
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_DISPLAY_MAP_MOBILE', 1);
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_DISPLAY_SUPERPOSED', 0);
    Configuration::updateValue('COLISSIMO_WIDGET_OSM_FIRST_DISPLAY', 'map');

    return true;
}

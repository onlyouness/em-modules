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
 * Upgrade to 2.1.1
 *
 * @param $module
 * @return true
 * @throws PrestaShopException
 */
function upgrade_module_2_1_1($module)
{
    Configuration::updateValue('COLISSIMO_LAST_DISPLAY_SIGNATURE_MODAL', '');
    $configPartner = Configuration::get('COLISSIMO_POSTAL_PARTNER');
    if ($configPartner) {
        Configuration::updateValue('COLISSIMO_POSTAL_PARTNER_LU', 1);
        Configuration::updateValue('COLISSIMO_POSTAL_PARTNER_AT', 1);
        Configuration::updateValue('COLISSIMO_POSTAL_PARTNER_DE', 1);
        Configuration::updateValue('COLISSIMO_POSTAL_PARTNER_IT', 1);
    } else {
        Configuration::updateValue('COLISSIMO_POSTAL_PARTNER_LU', 0);
        Configuration::updateValue('COLISSIMO_POSTAL_PARTNER_AT', 0);
        Configuration::updateValue('COLISSIMO_POSTAL_PARTNER_DE', 0);
        Configuration::updateValue('COLISSIMO_POSTAL_PARTNER_IT', 0);
    }
    Configuration::deleteByName('COLISSIMO_POSTAL_PARTNER');
    return true;
}

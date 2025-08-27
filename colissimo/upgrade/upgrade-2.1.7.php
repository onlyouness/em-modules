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
 * Upgrade to 2.1.7
 *
 * @param $module Colissimo
 * @return bool
 */
function upgrade_module_2_1_7($module)
{
    $logsEnabled = Configuration::get('COLISSIMO_LOGS');
    Configuration::updateValue('COLISSIMO_LOGS', 1);
    $module->initLogger();
    $module->logger->setChannel('ModuleUpgrade');
    $module->logger->info('Module upgrade. Version 2.1.7');
    Configuration::updateValue('COLISSIMO_ENABLE_SECURE_RETURN', 0);
    Configuration::deleteByName('COLISSIMO_LAST_DISPLAY_SATISFACTION_MODAL');
    Configuration::deleteByName('COLISSIMO_DISPLAY_SATISFACTION_MODAL');
    Configuration::deleteByName('COLISSIMO_DATE_END_SATISFACTION_MODAL');
    // delete printer kit zip
    $zipPath = realpath(_PS_MODULE_DIR_ . 'colissimo/documents/printing_kit/kit-imprimante-thermique.zip');
    if (file_exists($zipPath)) {
        return unlink($zipPath);
    }
    $module->logger->info('Module upgraded.');
    Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

    return true;
}

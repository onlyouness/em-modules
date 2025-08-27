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
 * Upgrade to 2.1.2
 *
 * @param $module
 * @return true
 * @throws PrestaShopException
 */
function upgrade_module_2_1_2($module)
{
    $logsEnabled = Configuration::get('COLISSIMO_LOGS');
    Configuration::updateValue('COLISSIMO_LOGS', 1);
    $module->initLogger();
    $module->logger->setChannel('ModuleUpgrade');
    $module->logger->info('Module upgrade. Version 2.1.2');
    $module->registerHook('displayAdminColissimoOrdersListBefore');
    $module->registerHook('displayAdminColissimoOrdersListAfter');
    $module->registerHook('displayAdminColissimoAssignOrdersListAfter');
    $module->registerHook('displayHeader');
    Configuration::updateValue('COLISSIMO_CN23_FORMAT', 'PDF_A4_300dpi');
    Configuration::updateValue('COLISSIMO_CN23_NUMBER', 4);
    Configuration::updateValue('COLISSIMO_CUSTOMS_REFERENCE', '');
    Configuration::updateValue('COLISSIMO_USE_DELIVERED_PICKUP_ORDER', 0);
    $ordersTab = Tab::getInstanceFromClassName('AdminColissimoOrders');
    if (!Validate::isLoadedObject($ordersTab)) {
        try {
            $module->installMenu(
                [
                    'visible' => true,
                    'class_name' => 'AdminColissimoOrders',
                    'parent_class_name' => 'AdminParentOrders',
                    'ParentClassName' => 'AdminParentOrders',
                    'name' => [
                        'fr' => 'Colissimo - Commandes',
                        'en' => 'Colissimo - Orders',
                    ],
                ]
            );
            $module->installMenu(
                [
                    'visible' => false,
                    'class_name' => 'AdminColissimoAssignOrders',
                    'parent_class_name' => 'AdminParentOrders',
                    'ParentClassName' => 'AdminParentOrders',
                    'name' => [
                        'fr' => 'Colissimo - Commandes non associés à Colissimo',
                        'en' => 'Colissimo - Orders not assigned to Colissimo',
                    ],
                ]
            );
        } catch (Exception $e) {
            $module->logger->error($e->getMessage());
            $module->logger->error('Cannot install Order menu.');
            Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

            return false;
        }
    }

    // Add new column in colissimo_label table
    try {
        $columnExists = Db::getInstance()
            ->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'colissimo_label` LIKE "cn23_format"');
    } catch (Exception $e) {
        $module->logger->error($e->getMessage());
        Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

        return false;
    }
    if (empty($columnExists)) {
        $result = Db::getInstance()
            ->execute(
                'ALTER TABLE `' . _DB_PREFIX_ . 'colissimo_label` ADD COLUMN `cn23_format` VARCHAR(3) NOT NULL AFTER `label_format`'
            );
        if (!$result) {
            $module->logger->error('Cannot add column in colissimo_label table.');
            Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

            return false;
        }
        $module->logger->info('New column in colissimo_label created.');
    } else {
        $module->logger->info('Column cn23_format already exists.');
    }
    // add new order states
    $module->installOrderStates();
    // add new tracking codes
    $module->createTrackingCodes(ColissimoTools::getColissimoTrackingCodesSource());
    Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

    return true;
}

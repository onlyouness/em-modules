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
 * Class ColissimoWidgetModuleFrontController
 * Ajax processes:
 *  - selectPickupPoint
 */
class ColissimoWidgetModuleFrontController extends ModuleFrontController
{
    /** @var Colissimo */
    public $module;

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function displayAjaxSelectPickupPoint()
    {
        $data = json_decode(Tools::getValue('infoPoint'), true);
        $colissimoId = $data['colissimo_id'];
        $mobilePhone = str_replace(['(', '_', ')'], '', $data['mobilePhone']);
        $pickupPoint = ColissimoPickupPoint::getPickupPointByIdColissimo($colissimoId);
        unset($data['mobilePhone']);
        $pickupPoint->hydrate(array_map('pSQL', $data));
        $needMobileValidation = Configuration::get('PS_ORDER_PROCESS_TYPE');
        $deliveryAddress = new Address((int) $this->context->cart->id_address_delivery);
        if (!$mobilePhone && !$needMobileValidation) {
            $mobilePhone = $deliveryAddress->phone_mobile;
        }
        try {
            $pickupPoint->save();
            ColissimoCartPickupPoint::updateCartPickupPoint(
                (int) $this->context->cart->id,
                (int) $pickupPoint->id,
                $mobilePhone
            );
        } catch (Exception $e) {
            $this->module->logger->error($e->getMessage());
            $this->context->smarty->assign(
                'colissimo_pickup_point_error',
                $this->module->l('An unexpected error occurred. Please refresh the window.')
            );
            $tpl = $this->module->getTemplatePath(
                'front/_partials/pickup-point-address.tpl'
            );
            $html = $this->context->smarty->fetch($tpl);
            $this->ajaxDie(json_encode(['html_result' => $html]));
        }
        $this->context->smarty->assign(
            [
                'colissimo_pickup_point' => $pickupPoint,
                'mobile_phone' => $mobilePhone,
                'need_mobile_validation' => (int) $needMobileValidation,
                'colissimo_img_path' => $this->module->getPathUri() . 'views/img/',
            ]
        );
        $tpl = $this->module->getTemplatePath('front/_partials/pickup-point-address.tpl');
        $html = $this->context->smarty->fetch($tpl);
        $this->ajaxDie(json_encode(['html_result' => $html]));
    }
}

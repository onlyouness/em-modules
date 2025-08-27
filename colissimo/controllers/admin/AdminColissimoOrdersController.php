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
 * Class AdminColissimoOrdersController
 */
class AdminColissimoOrdersController extends ModuleAdminController
{
    /** @var Colissimo */
    public $module;

    /** @var string */
    private $header;

    /**
     * AdminColissimoOrdersController constructor
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        parent::__construct();
        $this->module->logger->setChannel('ColissimoOrders');
        $this->getColissimoOrderList();
        $this->bulk_actions = [
            'deleteLabels' => [
                'text' => $this->l('Delete Labels'),
                'icon' => 'icon-power-off text-success',
            ],
        ];
        $this->header = $this->module->setColissimoControllerHeader();
        $this->context->smarty->assign('img_path', $this->module->getPathUri() . 'views/img/');
    }

    /**
     * @param $isNewTheme
     * @return void
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS($this->module->getLocalPath() . 'views/js/colissimo.orders.js');
    }

    /**
     * @throws SmartyException
     */
    public function initModal()
    {
        parent::initModal();
        $this->modals[] = $this->module->setModal();
    }

    /**
     * @return false|string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function renderList()
    {
        $ordersHeader = $this->createTemplate('orders-header.tpl')->fetch();
        if (!($this->fields_list && is_array($this->fields_list))) {
            return false;
        }
        $this->getList($this->context->language->id);
        $helper = new HelperList();
        $this->setHelperDisplay($helper);
        $helper->_default_pagination = $this->_default_pagination;
        $helper->_pagination = $this->_pagination;
        $helper->force_show_bulk_actions = true;
        $helper->sql = $this->_listsql;
        $list = $helper->generateList($this->_list, $this->fields_list);

        return $this->header . $ordersHeader . $list;
    }

    /**
     * @return bool|ObjectModel|void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('submitProcessColissimoDeleteLabels')) {
            $this->deleteColissimoLabels();
        }

        if (Tools::isSubmit('submitProcessColissimotRedirectAssign')) {
            Tools::redirectAdmin($this->context->link->getAdminLink(
                'AdminColissimoAssignOrders',
                true
            ));
        }

    }

    /**
     * @return void
     * @throws PrestaShopDatabaseException
     */
    public function getColissimoOrderList()
    {
        $selectFields = [
            'a.id_order',
            'o.date_add',
            'o.reference',
            'CONCAT(LEFT(c.`firstname`, 1), ". ", c.`lastname`) AS customer',
            'os.color',
            'osl.name AS status_name',
            'a.id_colissimo_order',
            'cs.`commercial_name`',
            'cl.`name` AS `country`',
            '"--"',
        ];
        $idLang = $this->context->language->id;
        $joins = [
            'LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON (o.`id_order` = a.`id_order`)',
            'LEFT JOIN `' . _DB_PREFIX_ . 'address` ad ON ad.`id_address` = o.`id_address_delivery`',
            'LEFT JOIN `' . _DB_PREFIX_ . 'country` co ON co.`id_country` = ad.`id_country`',
            'LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (cl.`id_country` = ad.`id_country` AND cl.`id_lang` = ' . $idLang . ')',
            'LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = o.`id_customer`)',
            'LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = o.`current_state`)',
            'LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state`
            AND osl.`id_lang` = ' . (int) $this->context->language->id . ')',
            'LEFT JOIN `' . _DB_PREFIX_ . 'carrier` ca ON (ca.`id_carrier` = o.`id_carrier`)',
            'LEFT JOIN `' . _DB_PREFIX_ . 'carrier_lang` cal ON (cal.`id_carrier` = ca.`id_carrier`
            AND cal.`id_lang` = ' . (int) $this->context->language->id . ' 
            AND cal.`id_shop` = ' . (int) $this->context->shop->id . ')',
            'LEFT JOIN `' . _DB_PREFIX_ . 'colissimo_service` cs ON cs.`id_colissimo_service` = a.`id_colissimo_service`',
        ];
        $this->_select = implode(', ', $selectFields);
        $this->identifier = 'id_colissimo_order';
        $this->table = 'colissimo_order';
        $this->_join = implode(' ', $joins);
        $this->_orderBy = 'o.date_add';
        $this->_orderWay = 'desc';
        $this->addRowAction('');
        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        $statusesArray = [];
        $colissimoServicesList = [];
        $countriesList = [];
        foreach ($statuses as $status) {
            $statusesArray[$status['id_order_state']] = $status['name'];
        }
        $colissimoServices = ColissimoService::getAll();
        foreach ($colissimoServices as $colissimoService) {
            $colissimoServicesList[$colissimoService['commercial_name']] = $colissimoService['commercial_name'];
        }
        ksort($colissimoServicesList, SORT_ASC);
        $countries = Country::getCountries((int) $idLang);
        foreach ($countries as $country) {
            $countriesList[$country['id_country']] = $country['name'];
        }
        $this->fields_list = [
            'reference' => [
                'title' => $this->module->l('Reference', 'AdminColissimoOrders'),
                'callback' => 'linkRef',
                'remove_onclick' => true,
            ],
            'id_order' => [
                'title' => $this->module->l('ID', 'AdminColissimoOrders'),
                'havingFilter' => true,
                'type' => 'int',
                'filter_key' => 'a!id_order',
                'remove_onclick' => true,
            ],
            'customer' => [
                'title' => $this->module->l('Customer', 'AdminColissimoOrders'),
                'havingFilter' => true,
                'remove_onclick' => true,
            ],
            'status_name' => [
                'title' => $this->module->l('Status', 'AdminColissimoOrders'),
                'type' => 'select',
                'color' => 'color',
                'list' => $statusesArray,
                'filter_key' => 'os!id_order_state',
                'filter_type' => 'int',
                'order_key' => 'status_name',
                'remove_onclick' => true,
            ],
            'date_add' => [
                'title' => $this->module->l('Date', 'AdminColissimoOrders'),
                'type' => 'datetime',
                'filter_key' => 'o!date_add',
                'remove_onclick' => true,
            ],
            'commercial_name' => [
                'title' => $this->module->l('Colissimo Service', 'AdminColissimoOrders'),
                'remove_onclick' => true,
                'type' => 'select',
                'list' => $colissimoServicesList,
                'filter_key' => 'cs!commercial_name',
                'filter_type' => 'string',
                'order_key' => 'commercial_name',
            ],
            'country' => [
                'title' => $this->module->l('Delivery country', 'AdminColissimoOrders'),
                'remove_onclick' => true,
                'type' => 'select',
                'list' => $countriesList,
                'filter_key' => 'co!id_country',
                'filter_type' => 'int',
                'order_key' => 'country',
            ],
        ];
    }

    /**
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getData()
    {
        $idColissimoOrder = (int) Tools::getValue('id_colissimo_order');
        $colissimoOrder = new ColissimoOrder((int) $idColissimoOrder);
        $colissimoService = new ColissimoService((int) $colissimoOrder->id_colissimo_service);
        $order = new Order((int) $colissimoOrder->id_order);
        $customerAddress = new Address((int) $order->id_address_delivery);
        $customerInvoiceAddress = new Address((int) $order->id_address_invoice);
        $merchantAddress = new ColissimoMerchantAddress('sender');
        $isoCustomerAddr = Country::getIsoById((int) $customerAddress->id_country);
        $weight = ColissimoTools::getOrderTotalWeightInKg($order);
        $productsDetail = [];
        $productsPrice = 0;
        foreach ($order->getProductsDetail() as $product) {
            $productsDetail[$product['product_id']][$product['product_attribute_id']] = $product['product_quantity'];
            $productsPrice += (float) $product['unit_price_tax_excl'] * (int) $product['product_quantity'];
        }
        if (Configuration::get('COLISSIMO_USE_WEIGHT_TARE') == '1') {
            $weight += Configuration::get('COLISSIMO_DEFAULT_WEIGHT_TARE');
        }
        $id_address = (int) ColissimoAddress::getAddressByCartId((int) $order->id_cart);
        $colissimoAddress = new ColissimoAddress((int) $id_address);
        $isBlockingCodeActive = $this->module->checkColissimoBlockingCode();
        $maxBlockingAmount = (float)Configuration::get('COLISSIMO_BLOCKING_CODE_TOTAL_MAX');
        $disabledBlockingCode = 1;
        if ($isBlockingCodeActive && $colissimoService->type == ColissimoService::TYPE_SIGN && $isoCustomerAddr == 'FR') {
            if ((float)$order->total_paid_tax_incl < $maxBlockingAmount) {
                $disabledBlockingCode = 0;
            }
        }
        $data = [
            'order' => $order,
            'products_detail' => $productsDetail,
            'products_price' => $productsPrice,
            'version' => $this->module->version,
            'cart' => new Cart((int) $order->id_cart),
            'customer' => new Customer((int) $order->id_customer),
            'colissimo_order' => $colissimoOrder,
            'colissimo_service' => $colissimoService,
            'customer_addr' => $customerAddress,
            'customer_addr_inv' => $customerInvoiceAddress,
            'merchant_addr' => $merchantAddress,
            'additional_address' => $colissimoAddress,
            'customs_reference' => Configuration::get('COLISSIMO_CUSTOMS_REFERENCE'),
            'cn23_number' => Configuration::get('COLISSIMO_CN23_NUMBER'),
            'label_dimensions' => [
                'length' => '',
                'width' => '',
                'height' => '',
            ],
            'label_articles_desc' => '',
            'cn23_category' => '',
            'form_options' => [
                'include_return' => 0,
                'insurance' => 0,
                'ta' => 0,
                'd150' => 0,
                'weight' => $weight,
                'mobile_phone' => $customerAddress->phone_mobile,
                'postal_partner' => Configuration::get('COLISSIMO_POSTAL_PARTNER_' . $isoCustomerAddr),
                'ddp' => $colissimoOrder->ddp,
                'disabled_blocking_code' => $disabledBlockingCode,
            ],
        ];

        return $data;
    }

    /**
     * @return void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function deleteColissimoLabels()
    {
        $ids = Tools::getValue('colissimo_orderBox');
        if (!$ids || empty($ids)) {
            $this->errors[] = $this->module->l('Please select at least one order.', 'AdminColissimoOrders');
        }
        foreach ($ids as $orderId) {
            $this->module->deleteOrderLabels((int) $orderId);
        }
        $this->confirmations[] = $this->module->l('labels deleted successfully.', 'AdminColissimoOrders');
    }

    /**
     * @return void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function ajaxProcessCreateColissimoLabels()
    {
        $data = $this->getData();
        /** @var Order $order */
        $order = $data['order'];
        try {
            $this->module->labelGenerator->setData($data);
            $this->module->checkEori($data);
            $colissimoLabel = $this->module->labelGenerator->generate();
        } catch (Exception $e) {
            $this->module->logger->error(sprintf('Exception throw while generating label for order #%d :' . $e->getMessage(), $order->id));
            $return = [
                'id_label' => 0,
                'id_return_label' => 0,
            ];
            $this->ajaxDie(json_encode($return));
            die();
        }
        $orderCarrier = ColissimoOrderCarrier::getByIdOrder($order->id);
        if (Validate::isLoadedObject($orderCarrier) && !$orderCarrier->tracking_number) {
            $orderCarrier->tracking_number = pSQL($colissimoLabel->shipping_number);
            $orderCarrier->save();
            $hash = md5($order->reference . $order->secure_key);
            $link = $this->context->link->getModuleLink(
                'colissimo',
                'tracking',
                ['order_reference' => $order->reference, 'hash' => $hash],
                null,
                $order->id_lang,
                $order->id_shop
            );
            $isoLangOrder = Language::getIsoById($order->id_lang);
            if (isset($this->module->PNAMailObject[$isoLangOrder])) {
                $object = $this->module->PNAMailObject[$isoLangOrder];
            } else {
                $object = $this->module->PNAMailObject['en'];
            }
            ColissimoTools::sendHandlingShipmentMail(
                $order,
                sprintf($object, $order->reference),
                $link
            );
            $this->module->logger->info('Send tracking mail for shipment ' . $colissimoLabel->shipping_number);
        }
        if (Configuration::get('COLISSIMO_USE_SHIPPING_IN_PROGRESS')) {
            $idShippingInProgressOS = Configuration::get('COLISSIMO_OS_SHIPPING_IN_PROGRESS');
            $shippingInProgressOS = new OrderState((int) $idShippingInProgressOS);
            if (Validate::isLoadedObject($shippingInProgressOS)) {
                if (!$order->getHistory($this->context->language->id, (int) $idShippingInProgressOS)) {
                    $history = new OrderHistory();
                    $history->id_order = (int) $order->id;
                    $history->changeIdOrderState($idShippingInProgressOS, (int) $order->id);
                    try {
                        $history->add();
                    } catch (Exception $e) {
                        $this->module->logger->error(sprintf('Cannot change status of order #%d', $order->id));
                    }
                }
            } else {
                $this->module->logger->error('Shipping in Progress order state is not valid');
            }
        }
        $return = [
            'id_label' => $colissimoLabel->id,
        ];
        // return labels
        if (Configuration::get('COLISSIMO_ENABLE_RETURN') && !Configuration::get('COLISSIMO_ENABLE_SECURE_RETURN')) {
            $customerCountry = $data['customer_addr']->id_country;
            $returnDestinationType = ColissimoTools::getReturnDestinationTypeByIdCountry($customerCountry);
            if ($returnDestinationType === false) {
                $this->module->logger->error($this->module->l('You cannot edit return label to this destination.', 'AdminColissimoOrders'));
            } else {
                $idService = ColissimoService::getServiceIdByIdCarrierDestinationType(0, $returnDestinationType);
                $data['colissimo_service_initial'] = $data['colissimo_service'];
                $data['colissimo_service'] = new ColissimoService((int) $idService);
                $data['merchant_addr'] = ColissimoMerchantAddress::getMerchantReturnAddress();
                try {
                    $this->module->labelGenerator->setData($data);
                    $colissimoReturnLabel = $this->module->labelGenerator->generateReturn($colissimoLabel);
                } catch (Exception $e) {
                    $this->module->logger->error('Exception throw while generating return label.', $e->getMessage());
                }
                if (isset($colissimoReturnLabel)) {
                    $return['id_return_label'] = $colissimoReturnLabel->id;
                }
            }
        }
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @return void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function ajaxProcessDisplayHeaderResult()
    {
        $summary = [];
        $labelIds = json_decode(Tools::getValue('label_ids'), true);
        foreach ($labelIds as $labelId) {
            $label = new ColissimoLabel((int) $labelId);
            if ($label->return_label) {
                $summary['return_label'][] = $label->id;
            } else {
                $summary['label'][] = $label->id;
            }
            if ($label->cn23) {
                $summary['cn23'][] = $label->id;
            }
        }
        $this->context->smarty->assign(
            [
                'input_label' => isset($summary['label']) ? json_encode($summary['label']) : false,
                'input_return_label' => isset($summary['return_label']) ? json_encode($summary['return_label']) : false,
                'input_cn23' => isset($summary['cn23']) ? json_encode($summary['cn23']) : false,
            ]
        );
        $html = $this->createTemplate('_partials/download-result.tpl')
            ->fetch();
        $return = [
            'result_html' => $html,
            'labels_ids' => isset($summary['label']) ? json_encode($summary['label']) : [],
        ];
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @param $ref
     * @param $col
     * @return false|string
     * @throws SmartyException
     */
    public function linkRef($ref, $col)
    {
        $link = $this->context->link->getAdminLink('AdminOrders', true, [], ['id_order' => (int) $col['id_order'], 'vieworder' => 1]) . '&id_order=' . (int) $col['id_order'] . '&vieworder';
        $this->context->smarty->assign([
            'url' => $link,
            'reference' => $ref,
        ]);

        return $this->createTemplate('_partials/order_link.tpl')->fetch();
    }
}

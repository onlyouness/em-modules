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
 * Class AdminColissimoAssignOrdersController
 */
class AdminColissimoAssignOrdersController extends ModuleAdminController {
    /** @var Colissimo */
    public $module;

    /** @var string */
    private $header;

    /**
     * AdminColissimoAssignOrdersController constructor
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
        $this->module->logger->setChannel('ColissimoAssignOrders');
        $this->getColissimoOrderList();
        $this->bulk_actions = [
            'AssignColissimoSignature' => [
                'text' => $this->l('Assign to Colissimo with signature'),
                'icon' => 'icon-power-off text-success',
            ],
            'AssignColissimoWithoutSignature' => [
                'text' => $this->l('Assign Colissimo withtout Signature'),
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
        if(Tools::isSubmit('submitProcessColissimotAssignSignature') ||
            Tools::isSubmit('submitBulkAssignColissimoSignatureorders')){
            $this->processOrdersMassAssignment(true);
        }
        if(Tools::isSubmit('submitProcessColissimotAssignWithoutSignature') ||
        Tools::isSubmit('submitBulkAssignColissimoWithoutSignatureorders')){
            $this->processOrdersMassAssignment(false);
        }
        if(Tools::isSubmit('submitProcessColissimotRedirectOrdersColissimo')){
            Tools::redirectAdmin($this->context->link->getAdminLink(
                'AdminColissimoOrders',
                true
            ));
        }
    }


    /**
     *  Assign order to colissimo
     * @param int $idOrder
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function orderAssignment(int $orderId, bool $isSignature)
    {
        $order = new Order($orderId);
        $serviceType = ColissimoService::TYPE_SIGN;
        if (!$isSignature) {
            $serviceType = ColissimoService::TYPE_NOSIGN;
        }

        if (ColissimoOrder::exists($orderId) == 0) {
            $eligibleServices = $this->module->getEligibleServiceByOrder($order);
            if(!empty($eligibleServices)){
                foreach ($eligibleServices as $id => $service) {
                    if (ColissimoService::getServiceTypeById($id) == $serviceType) {
                        $colissimoOrder = new ColissimoOrder();
                        $colissimoOrder->id_order = (int) $orderId;
                        $colissimoOrder->id_colissimo_service = (int) $id;
                        $colissimoOrder->id_colissimo_pickup_point = 0;
                        $colissimoOrder->migration = 0;
                        $colissimoOrder->hidden = 0;
                        try {
                            $colissimoOrder->save();
                            $this->module->logger->info(
                                sprintf($this->module->l(
                                    'The Order %s has been successfully associated to Colissimo %s'),
                                    $order->reference,
                                    $serviceType
                                )
                            );
                            return true;
                        }catch (Exception $e){
                            $this->module->logger->error(
                                sprintf($this->module->l('Cannot associate the order %s to Colissimo %s'),
                                    $order->reference,
                                    $serviceType
                                )
                            );
                            $this->module->logger->error($e->getMessage());
                        }
                    }
                        $this->module->logger->error(
                            sprintf($this->module->l('Cannot associate the order %s to Colissimo %s, Service not found'),
                                $order->reference,
                                $serviceType
                            )
                        );
                }
            }
                $this->module->logger->error(
                    sprintf($this->module->l('Cannot associate the order %s to Colissimo %s'),
                        $order->reference,
                        $serviceType
                    )
                );
        }
            $this->module->logger->error(
                sprintf($this->module->l('Cannot associate the order %s to Colissimo %s , Service empty'),
                    $order->reference,
                    $serviceType
                )
            );

        return false;
    }

    public function processOrdersMassAssignment(bool $isSignature)
    {
        $this->module->logger->setChannel('OrdersMassAssignment');
        $ids = Tools::getValue('ordersBox');
        if (empty($ids)) {
            $this->errors[] = $this->module->l('Please select at least one order.', 'ColissimoAssignOrders');
        }else {
            foreach ($ids as $orderId) {
                $success = $this->orderAssignment((int) $orderId, $isSignature);
            }
            if($success){
                $this->confirmations[] = $this->module->l('Orders successfully associated to colissimo.',
                    'ColissimoAssignOrders');
            }else{
                $this->errors[] = $this->module->l('One or more orders could not be associated to Colissimo',
                    'ColissimoAssignOrders');
            }
        }

    }

    /**
     * @return void
     * @throws PrestaShopDatabaseException
     */
    public function getColissimoOrderList()
    {
        $selectFields = [
            'CONCAT(LEFT(c.`firstname`, 1), ". ", c.`lastname`) AS customer',
            'os.color',
            'osl.name AS status_name',
            'cl.`name` AS `country`',
        ];
        $idLang = $this->context->language->id;
        $joins = [
            'LEFT JOIN `' . _DB_PREFIX_ . 'address` ad ON ad.`id_address` = a.`id_address_delivery`',
            'LEFT JOIN `' . _DB_PREFIX_ . 'country` cou ON cou.`id_country` = ad.`id_country`',
            'LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (cl.`id_country` = ad.`id_country` AND cl.`id_lang` = ' . $idLang . ')',
            'LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)',
            'LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = a.`current_state`)',
            'LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state`
            AND osl.`id_lang` = ' . (int) $this->context->language->id . ')',
            'LEFT JOIN `' . _DB_PREFIX_ . 'carrier` ca ON (ca.`id_carrier` = a.`id_carrier`)',
            'LEFT JOIN `' . _DB_PREFIX_ . 'carrier_lang` cal ON (cal.`id_carrier` = ca.`id_carrier`
            AND cal.`id_lang` = ' . (int) $this->context->language->id . '
            AND cal.`id_shop` = ' . (int) $this->context->shop->id . ')'
        ];
        $where = 'AND a.`id_order` NOT IN (SELECT co.`id_order` FROM ' . _DB_PREFIX_ . 'colissimo_order as co)';
        $this->_select = implode(', ', $selectFields);
        $this->identifier = 'id_order';
        $this->table = 'orders';
        $this->_join = implode(' ', $joins);
        $this->_where = $where;
        $this->_orderBy = 'a.date_add';
        $this->_orderWay = 'desc';
        $this->addRowAction('');
        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        $statusesArray = [];
        $countriesList = [];
        foreach ($statuses as $status) {
            $statusesArray[$status['id_order_state']] = $status['name'];
        }

        $countries = Country::getCountries((int) $idLang);
        foreach ($countries as $country) {
            $countriesList[$country['id_country']] = $country['name'];
        }
        $this->fields_list = [
            'reference' => [
                'title' => $this->module->l('Reference', 'AdminColissimoAssignOrders'),
                'callback' => 'linkRef',
                'remove_onclick' => true,
            ],
            'id_order' => [
                'title' => $this->module->l('ID', 'AdminColissimoAssignOrders'),
                'havingFilter' => true,
                'type' => 'int',
                'filter_key' => 'a!id_order',
                'remove_onclick' => true,
            ],
            'customer' => [
                'title' => $this->module->l('Customer', 'AdminColissimoAssignOrders'),
                'havingFilter' => true,
                'remove_onclick' => true,
            ],
            'status_name' => [
                'title' => $this->module->l('Status', 'AdminColissimoAssignOrders'),
                'type' => 'select',
                'color' => 'color',
                'list' => $statusesArray,
                'filter_key' => 'os!id_order_state',
                'filter_type' => 'int',
                'order_key' => 'status_name',
                'remove_onclick' => true,
            ],
            'date_add' => [
                'title' => $this->module->l('Date', 'AdminColissimoAssignOrders'),
                'type' => 'datetime',
                'filter_key' => 'o!date_add',
                'remove_onclick' => true,
            ],
            'country' => [
                'title' => $this->module->l('Delivery country', 'AdminColissimoAssignOrders'),
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
            ],
        ];

        return $data;
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
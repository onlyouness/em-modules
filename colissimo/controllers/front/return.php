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
 * Class ColissimoReturnModuleFrontController
 *
 * Ajax processes:
 *  - showReturnAddress
 *  - checkAvailability
 *  - confirmPickup
 *  - showSecureReturnForm
 *  - sendSecureReturnRequest
 *
 */
class ColissimoReturnModuleFrontController extends ModuleFrontController
{
    /** @var bool */
    public $auth = true;

    /** @var string */
    public $authRedirection = 'module-colissimo-return';

    /** @var Colissimo */
    public $module;

    /** @var array */
    public $conf;

    /**
     * ColissimoReturnModuleFrontController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->conf = [
            1000 => $this->module->l('Return label has been generated successfully'),
        ];
    }

    /**
     * @return array
     */
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        $page['meta']['robots'] = 'noindex';
        $page['meta']['title'] = $this->module->l('Colissimo returns');

        return $page;
    }

    /**
     * @return bool
     */
    public function checkAccess()
    {
        if (!Configuration::get('COLISSIMO_ENABLE_RETURN') ||
            !Configuration::get('COLISSIMO_DISPLAY_RETURN_LABEL_CUSTOMER')
        ) {
            $this->redirect_after = $this->context->link->getPageLink('my-account');
            $this->redirect();
        }

        return parent::checkAccess();
    }

    /**
     * @return bool|void
     */
    public function setMedia()
    {
        parent::setMedia();
        $this->module->registerJs(
            'colissimo-module-front-return',
            'front.return.js',
            ['position' => 'bottom', 'priority' => 150]
        );
        $this->module->registerCSS('colissimo-module-front-css', 'colissimo.front.css');
    }

    /**
     * @param string $template
     * @param array $params
     * @param null $locale
     * @throws PrestaShopException
     */
    public function setTemplate($template, $params = [], $locale = null)
    {
        parent::setTemplate('module:colissimo/views/templates/front/' . $template, $params, $locale);
    }

    /**
     * @return array
     */
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }

    /**
     * @throws PrestaShopException
     */
    public function initContent()
    {
        parent::initContent();
        $shipments = $this->getColissimoOrdersByCustomer();
        if (Tools::getValue('conf') > 0) {
            $this->success[] = $this->conf[Tools::getValue('conf')];
        } elseif (Tools::getValue('conf') < 0) {
            $this->errors[] = $this->module->l('An error occurred while generating the return label.');

        }
        $this->context->smarty->assign(
            [
                'shipments' => $shipments,
                'colissimo_img_path' => $this->module->getPathUri() . 'views/img/',
            ]
        );
        $this->setTemplate('return.tpl');
    }

    /**
     * @return void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function postProcess()
    {
        $idLabel = Tools::getValue('id_label');
        if (Tools::getValue('action') == 'downloadLabel' && $idLabel) {
            $this->downloadLabel($idLabel);
        }
        if (Tools::getValue('action') == 'downloadSecureReturn' && $idLabel) {
            $this->downloadLabel($idLabel, true);
        }
        if (Tools::getValue('action') == 'generateLabel' && $idLabel) {
            $conf = $this->generateReturnLabel($idLabel) ? 1000 : -1;
            $this->redirect_after = $this->context->link->getModuleLink('colissimo', 'return', ['conf' => $conf]);
            $this->redirect();
        }
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getColissimoOrdersByCustomer()
    {
        $ids = ColissimoOrder::getCustomerColissimoOrderIds($this->context->customer->id, $this->context->shop->id);
        $mailboxReturn = Configuration::get('COLISSIMO_ENABLE_MAILBOX_RETURN');
        $data = [];
        foreach ($ids as $id) {
            $colissimoOrder = new ColissimoOrder((int) $id);
            $labels = $colissimoOrder->getShipments($this->context->language->id);
            if (empty($labels)) {
                continue;
            }
            foreach ($labels as $label) {
                $mailboxReturnText = '';
                if (isset($label['id_return_label'])) {
                    $colissimoReturnLabel = new ColissimoLabel((int) $label['id_return_label']);
                    if ($colissimoReturnLabel->hasMailboxPickup()) {
                        $details = $colissimoReturnLabel->getMailboxPickupDetails();
                        if (isset($details['pickup_date']) &&
                            $details['pickup_date'] &&
                            isset($details['pickup_before']) &&
                            $details['pickup_before']
                        ) {
                            $mailboxReturnText = sprintf(
                                $this->module->l('Pickup on %s before %s'),
                                Tools::displayDate(date('Y-m-d', $details['pickup_date'])),
                                $details['pickup_before']
                            );
                        }
                    }
                } else {
                    if (Configuration::get('COLISSIMO_GENERATE_RETURN_LABEL_CUSTOMER')) {
                        $colissimoReturnLabel = new ColissimoLabel();
                    } else {
                        continue;
                    }
                }
                $colissimoOrder = new ColissimoOrder((int) $id);
                $order = new Order((int) $colissimoOrder->id_order);
                $orderState = new OrderState((int) $order->current_state, $this->context->language->id);
                if (ColissimoService::getServiceTypeById($colissimoOrder->id_colissimo_service) != ColissimoService::TYPE_RELAIS) {
                    $customerAddr = new Address((int) $order->id_address_delivery);
                } else {
                    $customerAddr = new Address((int) $order->id_address_invoice);
                }
                $isoCustomerAddr = Country::getIsoById($customerAddr->id_country);
                if (!ColissimoTools::getReturnDestinationTypeByIsoCountry($isoCustomerAddr)) {
                    continue;
                }
                $colissimoLabel = new ColissimoLabel((int) $label['id_label']);
                $secureReturnPath = $colissimoLabel->getFilePathSecureReturn();
                $secureReturnRealpath = realpath($secureReturnPath);
                $data[] = [
                    'reference' => $order->reference,
                    'date' => Tools::displayDate($order->date_add, null, false),
                    'status' => [
                        'name' => $orderState->name,
                        'contrast' => (Tools::getBrightness($orderState->color) > 128) ? 'dark' : 'bright',
                        'color' => $orderState->color,
                    ],
                    'label' => [
                        'id' => $label['id_label'],
                        'secure_return_path' => $secureReturnRealpath,
                    ],
                    'return_available' => (bool) ColissimoTools::getReturnDestinationTypeByIsoCountry($isoCustomerAddr),
                    'return_label' => [
                        'id' => $colissimoReturnLabel->id_colissimo_label,
                        'shipping_number' => $colissimoReturnLabel->shipping_number,
                    ],
                    'return_file_deleted' => $colissimoReturnLabel->file_deleted,
                    'mailbox_return' => $mailboxReturn && $colissimoReturnLabel->isFranceReturnLabel(),
                    'mailbox_return_text' => $mailboxReturnText,
                    'secure_return' => Configuration::get('COLISSIMO_ENABLE_SECURE_RETURN'),
                ];
            }
        }

        return $data;
    }

    /**
     * @param ColissimoLabel $colissimoLabel
     * @return bool
     */
    public function checkLabelAccess($colissimoLabel)
    {
        $colissimoOrder = new ColissimoOrder((int) $colissimoLabel->id_colissimo_order);
        $order = new Order((int) $colissimoOrder->id_order);
        if ($order->id_customer != $this->context->customer->id) {
            return false;
        }

        return true;
    }

    /**
     * @param $idLabel
     * @param $secureReturn
     * @return void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function downloadLabel($idLabel, $secureReturn = false)
    {
        $this->module->logger->setChannel('FrontReturn');
        $label = new ColissimoLabel((int) $idLabel);
        $colissimoOrder = new ColissimoOrder((int) $label->id_colissimo_order);
        $order = new Order((int) $colissimoOrder->id_order);
        if ($order->id_customer != $this->context->customer->id) {

            return;
        }
        try {
            $secureReturn ? $label->downloadSecureReturn() : $label->download();
        } catch (Exception $e) {
            $this->module->logger->error(
                sprintf('Error while downloading return label: %s', $e->getMessage()),
                [
                    'id_customer' => $this->context->customer->id,
                    'id_colissimo_order' => $colissimoOrder->id_colissimo_order,
                    'id_order' => $colissimoOrder->id_order,
                    'id_label' => $label->id,
                ]
            );
            //@formatter:off
            $this->context->controller->errors[] = $this->module->l('An error occurred while downloading the return label. Please try again or contact our support.');
            //@formatter:on
        }
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function displayAjaxShowReturnAddress()
    {
        $idColissimoLabel = Tools::getValue('id_colissimo_label');
        $colissimoLabel = new ColissimoLabel((int) $idColissimoLabel);
        if (!Validate::isLoadedObject($colissimoLabel) || !$this->checkLabelAccess($colissimoLabel)) {
            $return = [
                'error' => true,
                'message' => $this->module->l(
                    'An unexpected error occurred. Please try again or contact our customer service'
                ),
            ];
            $this->ajaxDie(json_encode($return));
        }
        $colissimoOrder = new ColissimoOrder((int) $colissimoLabel->id_colissimo_order);
        $colissimoService = new ColissimoService((int) $colissimoOrder->id_colissimo_service);
        $order = new Order((int) $colissimoOrder->id_order);
        if ($colissimoService->is_pickup) {
            $address = new Address((int) $order->id_address_invoice);
        } else {
            $address = new Address((int) $order->id_address_delivery);
        }
        $this->context->smarty->assign(['address' => $address, 'id_colissimo_label' => $idColissimoLabel]);
        $tpl = $this->module->getTemplatePath('/_partials/colissimo-return-modal-body-address.tpl');
        $html = $this->context->smarty->fetch($tpl);
        $return = [
            'error' => false,
            'message' => '',
            'html' => $html,
        ];
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function displayAjaxCheckAvailability()
    {
        $this->module->logger->setChannel('MailboxReturn');
        $idColissimoLabel = Tools::getValue('id_colissimo_label');
        $colissimoLabel = new ColissimoLabel((int) $idColissimoLabel);
        if (!$this->checkLabelAccess($colissimoLabel)) {
            $return = [
                'error' => true,
                'message' => $this->module->l(
                    'An unexpected error occurred. Please try again or contact our customer service'
                ),
            ];
            $this->ajaxDie(json_encode($return));
        }
        $senderAddress = [
            'line0' => '',
            'line1' => '',
            'line2' => Tools::getValue('colissimo-address1'),
            'line3' => Tools::getValue('colissimo-address2'),
            'countryCode' => 'FR',
            'zipCode' => Tools::getValue('colissimo-postcode'),
            'city' => Tools::getValue('colissimo-city'),
        ];
        $mailboxDetailsRequest = new ColissimoMailboxDetailsRequest(ColissimoTools::getCredentials());
        $mailboxDetailsRequest->setSenderAddress($senderAddress)
            ->buildRequest();
        $client = new ColissimoClient();
        $this->module->logger->info(
            'Request mailbox details',
            ['request' => json_decode($mailboxDetailsRequest->getRequest(true), true)]
        );
        $client->setRequest($mailboxDetailsRequest);
        try {
            /** @var ColissimoMailboxDetailsResponse $response */
            $response = $client->request();
        } catch (Exception $e) {
            $this->module->logger->error($e->getMessage());
            $return = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
            $this->ajaxDie(json_encode($return));
        }
        if ($response->messages[0]['id']) {
            foreach ($response->messages as $message) {
                $this->module->logger->error(
                    'Error found',
                    sprintf('(%s) - %s', $message['id'], $message['messageContent'])
                );
            }
            $return = [
                'error' => true,
                'message' => $this->module->l('An error occurred. Please check your address and try again.'),
            ];
            $this->ajaxDie(json_encode($return));
        }
        $pickingDate = date('Y-m-d', $response->pickingDates[0]) . ' 00:00:00';

        $this->context->smarty->assign(
            [
                'max_picking_hour' => $response->maxPickingHour,
                'validity_time' => $response->validityTime,
                'picking_date' => $response->pickingDates[0],
                'picking_date_display' => Tools::displayDate($pickingDate),
                'id_colissimo_label' => $idColissimoLabel,
                'picking_address' => [
                    'company' => Tools::getValue('colissimo-company'),
                    'lastname' => Tools::getValue('colissimo-lastname'),
                    'firstname' => Tools::getValue('colissimo-firstname'),
                    'address1' => Tools::getValue('colissimo-address1'),
                    'address2' => Tools::getValue('colissimo-address2'),
                    'postcode' => Tools::getValue('colissimo-postcode'),
                    'city' => Tools::getValue('colissimo-city'),
                ],
            ]
        );
        //@formatter:off
        $tpl = $this->module->getTemplatePath('/_partials/colissimo-return-modal-body-dates.tpl');
        $html = $this->context->smarty->fetch($tpl);
        //@formatter:on
        $return = [
            'error' => false,
            'html' => $html,
        ];
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @throws Exception
     * @throws PrestaShopDatabaseException
     * @throws SmartyException
     */
    public function displayAjaxConfirmPickup()
    {
        $this->module->logger->setChannel('MailboxReturn');
        $idColissimoLabel = Tools::getValue('id_colissimo_label');
        $pickupDate = Tools::getValue('mailbox_date');
        $pickupBefore = Tools::getValue('mailbox_hour');
        $colissimoLabel = new ColissimoLabel((int) $idColissimoLabel);
        $errorMessage = '';
        if (Validate::isLoadedObject($colissimoLabel) && $this->checkLabelAccess($colissimoLabel)) {
            if (!$colissimoLabel->hasMailboxPickup()) {
                $senderAddress = [
                    'companyName' => Tools::getValue('mailbox_company'),
                    'lastName' => Tools::getValue('mailbox_lastname'),
                    'firstName' => Tools::getValue('mailbox_firstname'),
                    'line2' => Tools::getValue('mailbox_address1'),
                    'line3' => Tools::getValue('mailbox_address2'),
                    'zipCode' => Tools::getValue('mailbox_postcode'),
                    'city' => Tools::getValue('mailbox_city'),
                    'countryCode' => 'FR',
                    'email' => Tools::getValue('mailbox_email'),
                ];
                $date = date('Y-m-d', Tools::getValue('mailbox_date'));
                $pickupRequest = new ColissimoPlanPickupRequest(ColissimoTools::getCredentials());
                $pickupRequest->setParcelNumber($colissimoLabel->shipping_number)
                    ->setSenderAddress($senderAddress)
                    ->setMailboxPickingDate($date)
                    ->buildRequest();
                $client = new ColissimoClient();
                $client->setRequest($pickupRequest);
                $this->module->logger->info(
                    'Mailbox pickup request',
                    ['request' => json_decode($pickupRequest->getRequest(true), true)]
                );
                try {
                    /** @var ColissimoPlanPickupResponse $response */
                    $response = $client->request();
                } catch (Exception $e) {
                    $this->module->logger->error('Error thrown: ' . $e->getMessage());
                }
                if (isset($response) && !$response->messages[0]['id']) {
                    $this->module->logger->info('Mailbox pickup response', $response->response);
                    $insert = Db::getInstance()
                        ->insert(
                            'colissimo_mailbox_return',
                            [
                                'id_colissimo_label' => (int) $idColissimoLabel,
                                'pickup_date' => pSQL($pickupDate),
                                'pickup_before' => pSQL($pickupBefore),
                            ]
                        );
                    if ($insert) {
                        $hasError = false;
                    } else {
                        // Cannot insert
                        $this->module->logger->error('Cannot insert mailbox request in DB.');
                        $hasError = true;
                        $errorMessage = '';
                    }
                } else {
                    // Error thrown or error found
                    $this->module->logger->error('Errors found.', ['messages' => $response->messages]);
                    $hasError = true;
                    $errorMessage = '';
                }
            } else {
                // Pickup request already sent
                $this->module->logger->error('A pickup request has already been sent for this return.');
                $hasError = true;
                $errorMessage = $this->module->l('A pickup request has already been sent for this return.');
            }
        } else {
            // Invalid label
            $this->module->logger->error('Invalid label');
            $hasError = true;
            $errorMessage = '';
        }
        $this->context->smarty->assign(
            [
                'has_error' => $hasError,
                'error_message' => $errorMessage,
            ]
        );
        // @formatter:off
        $tpl = $this->module->getTemplatePath('/_partials/colissimo-return-modal-body-result.tpl');
        $html = $this->context->smarty->fetch($tpl);
        // @formatter:on
        $htmlText = sprintf(
            $this->module->l('Pickup on %s before %s'),
            Tools::displayDate(date('Y-m-d', $pickupDate)),
            $pickupBefore
        );
        $return = [
            'error' => $hasError,
            'html' => $html,
            'text_result' => $htmlText,
            'id_colissimo_label' => $idColissimoLabel,
        ];
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @return void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function displayAjaxShowSecureReturnForm()
    {
        $idColissimoLabel = Tools::getValue('id_colissimo_label');
        $colissimoLabel = new ColissimoLabel((int) $idColissimoLabel);
        if (!Validate::isLoadedObject($colissimoLabel) || !$this->checkLabelAccess($colissimoLabel)) {
            $return = [
                'error' => true,
                'message' => $this->module->l(
                    'An unexpected error occurred. Please try again or contact our customer service'
                ),
            ];
            $this->ajaxDie(json_encode($return));
        }
        $colissimoOrder = new ColissimoOrder((int) $colissimoLabel->id_colissimo_order);
        $order = new Order((int) $colissimoOrder->id_order);
        $detailProducts = $colissimoLabel->getRelatedProducts();
        foreach ($detailProducts as $key => $product) {
            foreach ($order->getOrderDetailList() as $item) {
                if ($item['product_id'] == $product['id_product'] && $item['product_attribute_id'] == $product['id_product_attribute']) {
                    $detailProducts[$key]['name'] = $item['product_name'];
                    $detailProducts[$key]['weight'] = $item['product_weight'];
                }
            }
        }
        $this->context->smarty->assign(['products' => $detailProducts, 'id_colissimo_label' => $idColissimoLabel]);
        $tpl = $this->module->getTemplatePath('/_partials/colissimo-return-secure-modal-body.tpl');
        $html = $this->context->smarty->fetch($tpl);
        $return = [
            'error' => false,
            'message' => '',
            'html' => $html,
        ];
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @return void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function displayAjaxSendSecureReturnRequest()
    {
        $this->module->logger->setChannel('SecureReturn');
        $idColissimoLabel = Tools::getValue('id_colissimo_label');
        $colissimoLabel = new ColissimoLabel((int) $idColissimoLabel);
        if (!$this->checkLabelAccess($colissimoLabel)) {
            $return = [
                'error' => true,
                'message' => $this->module->l(
                    'An unexpected error occurred. Please try again or contact our customer service'
                ),
            ];
            $this->ajaxDie(json_encode($return));
        }
        $labelProducts = $colissimoLabel->getRelatedProducts();
        $productsDetail = [];
        $totalWeight = 0;
        foreach ($labelProducts as $product) {
            if (Tools::getValue('colissimo_order_' . $product['id_product'] . '_' . $product['id_product_attribute'])) {
                $newQuantity = Tools::getValue('quantity_' . $product['id_product'] . '_' . $product['id_product_attribute']);
                $returnQuantity = ($newQuantity > $product['quantity'] || $newQuantity == 0) ? $product['quantity'] : $newQuantity;
                $productsDetail[$product['id_product']][$product['id_product_attribute']] = $returnQuantity;
                $productWeight = Tools::getValue('weight_' . $product['id_product'] . '_' . $product['id_product_attribute']) ? : 0.05;
                $totalWeight += (float) ($productWeight * $returnQuantity);
            }
        }
        if (empty($productsDetail)) {
            $return = [
                'error' => true,
                'message' => $this->module->l(
                    'Please select at least one product'
                ),
            ];
            $this->ajaxDie(json_encode($return));
        }
        $colissimoOrder = new ColissimoOrder((int) $colissimoLabel->id_colissimo_order);
        $order = new Order((int) $colissimoOrder->id_order);
        $customerAddress = new Address((int) $order->id_address_delivery);
        $merchantAddress = ColissimoMerchantAddress::getMerchantReturnAddress();
        $customerCountry = $customerAddress->id_country;
        $returnDestinationType = ColissimoTools::getReturnDestinationTypeByIdCountry($customerCountry);
        if ($returnDestinationType === false) {
            $return = [
                'error' => true,
                'message' => $this->module->l('Cannot edit return label for this destination.'),
            ];
            $this->ajaxDie(json_encode($return));
        }
        $idService = ColissimoService::getServiceIdByIdCarrierDestinationType(0, $returnDestinationType);
        $data = [
            'order' => $order,
            'version' => $this->module->version,
            'cart' => new Cart((int) $order->id_cart),
            'customer' => new Customer((int) $order->id_customer),
            'colissimo_order' => $colissimoOrder,
            'colissimo_service' => new ColissimoService((int) $idService),
            'colissimo_service_initial' => new ColissimoService((int) $colissimoOrder->id_colissimo_service),
            'customer_addr' => $customerAddress,
            'products_detail' => $productsDetail,
            'merchant_addr' => $merchantAddress,
            'cn23_number' => Configuration::get('COLISSIMO_CN23_NUMBER'),
            'form_options' => [
                'insurance' => 0,
                'ta' => 0,
                'd150' => 0,
                'weight' => $totalWeight,
                'mobile_phone' => $merchantAddress->phoneNumber,
            ],
        ];
        try {
            $this->module->labelGenerator->setData($data);
            $parcelNumber = $this->module->labelGenerator->generateSecureReturn($colissimoLabel);
        } catch (Exception $e) {
            $this->module->logger->error('Exception throw while generating secure return label.', $e->getMessage());
            $return = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
            $this->ajaxDie(json_encode($return));
        }
        $this->context->smarty->assign(['parcel_number' => $parcelNumber]);
        $tpl = $this->module->getTemplatePath('/_partials/colissimo-secure-return-confirmation.tpl');
        $html = $this->context->smarty->fetch($tpl);
        $return = [
            'error' => false,
            'message' => $this->module->l('Secure code generated.'),
            'html' => $html,
        ];
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @param int $idLabel
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function generateReturnLabel($idLabel)
    {
        $colissimoLabel = new ColissimoLabel($idLabel);
        $colissimoOrder = new ColissimoOrder((int) $colissimoLabel->id_colissimo_order);
        $order = new Order((int) $colissimoOrder->id_order);
        $customerAddress = new Address((int) $order->id_address_delivery);
        $merchantAddress = ColissimoMerchantAddress::getMerchantReturnAddress();
        $customerCountry = $customerAddress->id_country;
        $returnDestinationType = ColissimoTools::getReturnDestinationTypeByIdCountry($customerCountry);
        if ($returnDestinationType === false) {
            $this->module->logger->error('Cannot edit return label for this destination.');

            return false;
        }
        $idService = ColissimoService::getServiceIdByIdCarrierDestinationType(0, $returnDestinationType);
        $productsDetail = [];
        $shippedProducts = $colissimoLabel->getRelatedProducts();
        foreach ($shippedProducts as $product) {
            $productsDetail[$product['id_product']][$product['id_product_attribute']] = $product['quantity'];
        }
        $data = [
            'order' => $order,
            'products_detail' => $productsDetail,
            'version' => $this->module->version,
            'cart' => new Cart((int) $order->id_cart),
            'customer' => new Customer((int) $order->id_customer),
            'colissimo_order' => $colissimoOrder,
            'colissimo_service' => new ColissimoService((int) $idService),
            'colissimo_service_initial' => new ColissimoService((int) $colissimoOrder->id_colissimo_service),
            'customer_addr' => $customerAddress,
            'merchant_addr' => $merchantAddress,
            'cn23_number' => Configuration::get('COLISSIMO_CN23_NUMBER'),
            'form_options' => [
                'insurance' => 0,
                'ta' => 0,
                'd150' => 0,
                'weight' => ColissimoTools::getOrderTotalWeightInKg($order),
                'mobile_phone' => $merchantAddress->phoneNumber,
            ],
        ];
        try {
            $this->module->labelGenerator->setData($data);
            $this->module->labelGenerator->generateReturn($colissimoLabel);
        } catch (Exception $e) {
            $this->module->logger->error('Exception throw while generating return label.', $e->getMessage());

            return false;
        }

        return true;
    }
}

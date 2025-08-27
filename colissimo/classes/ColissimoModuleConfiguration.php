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
 * Class ColissimoModuleConfiguration
 */
class ColissimoModuleConfiguration
{
    /** @var Context */
    private $context;

    /** @var string */
    private $localPath;

    /** @var string */
    private $pathUri;

    /** @var string */
    private $version;

    /** @var Colissimo */
    private $module;

    /** @var array */
    private $migrateModuleFromList = [
        'colissimo_simplicite',
        'socolissimo',
        'sonice_etiquetage',
        'soflexibilite',
    ];

    /** @var array */
    public $modulesToMigrate = [];

    /** @var array */
    public $senderAddressFields = [
        'sender_company',
        'sender_lastname',
        'sender_firstname',
        'sender_address1',
        'sender_address2',
        'sender_address3',
        'sender_address4',
        'sender_city',
        'sender_zipcode',
        'sender_country',
        'sender_phone',
        'sender_email',
    ];

    /** @var array */
    public $returnAddressFields = [
        'return_company',
        'return_lastname',
        'return_firstname',
        'return_address1',
        'return_address2',
        'return_address3',
        'return_address4',
        'return_city',
        'return_zipcode',
        'return_country',
        'return_phone',
        'return_email',
    ];

    /** @var array */
    public $accountFields = [
        'COLISSIMO_LOGS',
        'COLISSIMO_ACCOUNT_LOGIN',
        'COLISSIMO_ACCOUNT_PASSWORD',
        'COLISSIMO_ACCOUNT_KEY',
        'COLISSIMO_ACCOUNT_PARENT_ID',
        'COLISSIMO_ACCOUNT_CONTRACT_NUMBER',
        'COLISSIMO_CONNEXION_KEY',
    ];

    /** @var array */
    public $widgetFields = [
        'COLISSIMO_WIDGET_REMOTE',
        'COLISSIMO_WIDGET_ENDPOINT',
        'COLISSIMO_WIDGET_COLOR_1',
        'COLISSIMO_WIDGET_COLOR_2',
        'COLISSIMO_WIDGET_FONT',
        'COLISSIMO_WIDGET_NATIVE',
        'COLISSIMO_WIDGET_OSM_MAP_MODAL',
        'COLISSIMO_WIDGET_OSM_BPR',
        'COLISSIMO_WIDGET_OSM_A2P',
        'COLISSIMO_WIDGET_OSM_CMT',
        'COLISSIMO_WIDGET_OSM_PCS',
        'COLISSIMO_WIDGET_OSM_BDP',
        'COLISSIMO_WIDGET_OSM_NUMBER_POINT'
    ];

    /** @var array */
    public $widgetFieldsMobile = [
        'COLISSIMO_WIDGET_REMOTE_MOBILE',
        'COLISSIMO_WIDGET_ENDPOINT_MOBILE',
        'COLISSIMO_WIDGET_COLOR_1_MOBILE',
        'COLISSIMO_WIDGET_COLOR_2_MOBILE',
        'COLISSIMO_WIDGET_FONT_MOBILE',
        'COLISSIMO_WIDGET_NATIVE_MOBILE',
        'COLISSIMO_WIDGET_OSM_MAP_MODAL_MOBILE',
        'COLISSIMO_WIDGET_OSM_BPR_MOBILE',
        'COLISSIMO_WIDGET_OSM_A2P_MOBILE',
        'COLISSIMO_WIDGET_OSM_CMT_MOBILE',
        'COLISSIMO_WIDGET_OSM_PCS_MOBILE',
        'COLISSIMO_WIDGET_OSM_BDP_MOBILE',
        'COLISSIMO_WIDGET_OSM_NUMBER_POINT_MOBILE',
        'COLISSIMO_WIDGET_OSM_DISPLAY_MAP_MOBILE',
        'COLISSIMO_WIDGET_OSM_FIRST_DISPLAY',
        'COLISSIMO_WIDGET_OSM_DISPLAY_SUPERPOSED'
    ];

    /** @var array */
    public $backOrdersFields = [
        'COLISSIMO_ORDER_PREPARATION_TIME',
        'COLISSIMO_USE_SHIPPING_IN_PROGRESS',
        'COLISSIMO_USE_HANDLED_BY_CARRIER',
        'COLISSIMO_USE_DELIVERED_PICKUP_ORDER',
        'COLISSIMO_ENABLE_PNA_MAIL',
        'COLISSIMO_DISPLAY_TRACKING_NUMBER',
    ];

    /** @var array */
    public $backPrintFields = [
        'COLISSIMO_GENERATE_LABEL_PRESTASHOP',
        'COLISSIMO_POSTAGE_MODE_MANUAL',
        'COLISSIMO_USE_THERMAL_PRINTER',
        'COLISSIMO_USE_ETHERNET',
        'COLISSIMO_USB_PROTOCOLE',
        'COLISSIMO_PRINTER_IP_ADDR',
        'COLISSIMO_LABEL_FORMAT',
        'COLISSIMO_CN23_FORMAT',
        'COLISSIMO_CN23_NUMBER',
        'COLISSIMO_LABEL_DISPLAY_REFERENCE',
    ];

    /** @var array */
    public $backShippingFields = [
        'COLISSIMO_DEFAULT_ORIGIN_COUNTRY',
        'COLISSIMO_DEFAULT_HS_CODE',
        'COLISSIMO_EORI_NUMBER',
        'COLISSIMO_EORI_NUMBER_UK',
        'COLISSIMO_CUSTOMS_REFERENCE',
        'COLISSIMO_POSTAL_PARTNER_LU',
        'COLISSIMO_POSTAL_PARTNER_AT',
        'COLISSIMO_POSTAL_PARTNER_DE',
        'COLISSIMO_POSTAL_PARTNER_IT',
    ];

    /** @var array */
    public $backPostageFields = [
        'COLISSIMO_USE_WEIGHT_TARE',
        'COLISSIMO_DEFAULT_WEIGHT_TARE',
        'COLISSIMO_INSURE_SHIPMENTS',
        'COLISSIMO_BLOCKING_CODE_TOTAL_MIN',
        'COLISSIMO_BLOCKING_CODE_TOTAL_MAX',
    ];

    /** @var array */
    public $backFields = [
        'COLISSIMO_USE_RETURN_ADDRESS',
        'COLISSIMO_WIDGET_PHONE',
    ];

    /** @var array */
    public $frontConfigFields = [
        'COLISSIMO_ENABLE_DDP',
        'COLISSIMO_DDP_COST',
        'COLISSIMO_DDP_GB_COST',
        'COLISSIMO_ENABLE_DOOR_CODES',
        'COLISSIMO_WEIGHTING_PRICES',
        'COLISSIMO_WEIGHTING_TYPE',
        'COLISSIMO_WEIGHTING_VALUE_PERCENT',
        'COLISSIMO_WEIGHTING_VALUE_AMOUNT'
    ];

    /** @var array */
    public $defaultShipmentsFields = [
        'COLISSIMO_ENABLE_RETURN',
        'COLISSIMO_AUTO_PRINT_RETURN_LABEL',
        'COLISSIMO_DISPLAY_RETURN_LABEL_CUSTOMER',
        'COLISSIMO_GENERATE_RETURN_LABEL_CUSTOMER',
        'COLISSIMO_ENABLE_MAILBOX_RETURN',
        'COLISSIMO_ENABLE_SECURE_RETURN',
    ];

    /** @var array */
    public $filesFields = [
        'COLISSIMO_FILES_LIMIT',
        'COLISSIMO_FILES_LIFETIME',
    ];

    /** @var array */
    public static $widgetFonts = [
        'Arial',
        'Arial Black',
        'Comic Sans MS',
        'Courrier New',
        'Georgia',
        'Impact',
        'Lucida Console',
        'Lucida Sans Unicode',
        'Tahoma',
        'Times New Roman',
        'Trebuchet MS',
        'Verdana',
        'MS Sans Serif',
        'MS Serif',
    ];

    /** @var array */
    public $labelFormats = [
        'PDF_A4_300dpi' => 'PDF A4 300dpi',
        'PDF_10x15_300dpi' => 'PDF 10x15 300dpi',
        'ZPL_10x15_203dpi' => 'ZPL 10x15 203dpi',
        'ZPL_10x15_300dpi' => 'ZPL 10x15 300dpi',
        'DPL_10x15_203dpi' => 'DPL 10x15 203dpi',
        'DPL_10x15_300dpi' => 'DPL 10x15 300dpi',
    ];

    /** @var array */
    public $cn23Formats = [
        'PDF_A4_300dpi' => 'PDF A4 300dpi',
        'PDF_10x12_300dpi' => 'PDF 10x12 300dpi',
        'ZPL_10x12_203dpi' => 'ZPL 10x12 203dpi',
        'ZPL_10x12_300dpi' => 'ZPL 10x12 300dpi',
        'DPL_10x12_203dpi' => 'DPL 10x12 203dpi',
        'DPL_10x12_300dpi' => 'DPL 10x12 300dpi',
    ];

    /** @var array  */
    public $usbPrinterProtocoles = [
        'DATAMAX' => 'DATAMAX',
        'INTERMEC' => 'INTERMEC',
        'ZEBRA' => 'ZEBRA',
    ];

    /** @var array  */
    public $colissimoLinks = [
        'en' => [
            'delivery_details' => 'https://www.colissimo.entreprise.laposte.fr/en/possibilites',
            'subscribe' => 'https://www.colissimo.entreprise.laposte.fr/en/our-contracts',
            'download_kit' => 'https://www.colissimo.entreprise.laposte.fr/en/tools-and-services/e-commerce-modules-and-solutions/prestashop-solution',
        ],
        'fr' => [
            'delivery_details' => 'https://www.colissimo.entreprise.laposte.fr/fr/possibilites',
            'subscribe' => 'https://www.colissimo.entreprise.laposte.fr/nos-contrats',
            'download_kit' => 'https://www.colissimo.entreprise.laposte.fr/outils-et-services/modules-et-solutions-e-commerce/solutions-e-commerce-prestashop',
        ],
    ];

    /**
     * ColissimoModuleConfiguration constructor.
     * @param Context $context
     * @param string $localPath
     * @param string $pathUri
     * @param string $version
     * @param Colissimo $module
     */
    public function __construct(Context $context, $localPath, $pathUri, $version = '', $module)
    {
        $this->context = $context;
        $this->localPath = $localPath;
        $this->pathUri = $pathUri;
        $this->version = $version;
        $this->module = $module;
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function getContent()
    {
        $this->context->controller->addJS($this->localPath . 'views/js/intlTelInput.min.js');
        $this->context->controller->addCSS($this->localPath . 'views/css/intlTelInput.css');
        $this->context->smarty->assign('link', $this->context->link);
        $merchantSenderAddress = new ColissimoMerchantAddress('sender');
        $senderAddress = array_merge(
            array_fill_keys($this->senderAddressFields, ''),
            $merchantSenderAddress->toArray()
        );
        $merchantReturnAddress = new ColissimoMerchantAddress('return');
        $returnAddress = array_merge(
            array_fill_keys($this->returnAddressFields, ''),
            $merchantReturnAddress->toArray()
        );
        $orderStatuses = OrderState::getOrderStates($this->context->language->id);
        $states = [];
        foreach ($orderStatuses as $orderStatus) {
            $states[(int)$orderStatus['id_order_state']] = $orderStatus['name'];
        }

        $accountFieldsValue = Configuration::getMultiple($this->accountFields);
        $accountFieldsValue['COLISSIMO_ACCOUNT_TYPE'] = json_decode(Configuration::get('COLISSIMO_ACCOUNT_TYPE'), true);
        $widgetFieldsValue = Configuration::getMultiple($this->widgetFields);
        $widgetFieldsValueMobile = Configuration::getMultiple($this->widgetFieldsMobile);
        $backOrdersFieldsValue = Configuration::getMultiple($this->backOrdersFields);
        $backOrdersFieldsValue['COLISSIMO_GENERATE_LABEL_STATUSES'] = json_decode(
            Configuration::get('COLISSIMO_GENERATE_LABEL_STATUSES'),
            true
        );

        $backPrintFieldsValue = Configuration::getMultiple($this->backPrintFields);
        $backShippingFieldsValue = Configuration::getMultiple($this->backShippingFields);
        $backPostageFieldsValue = Configuration::getMultiple($this->backPostageFields);
        $backPostageFieldsValue['COLISSIMO_DELIVERY_BLOCKING_CODE'] = $this->module->checkColissimoBlockingCode();
        $backFieldsValue = Configuration::getMultiple($this->backFields);
        $frontConfigFields = Configuration::getMultiple($this->frontConfigFields);
        $defaultShipmentsFieldsValue = Configuration::getMultiple($this->defaultShipmentsFields);
        $filesFieldsValue = ColissimoTools::getMultipleGlobal($this->filesFields);
        $formData = array_merge(
            $senderAddress,
            $returnAddress,
            $accountFieldsValue,
            $widgetFieldsValue,
            $widgetFieldsValueMobile,
            $backOrdersFieldsValue,
            $backPrintFieldsValue,
            $backShippingFieldsValue,
            $backPostageFieldsValue,
            $backFieldsValue,
            $frontConfigFields,
            $defaultShipmentsFieldsValue,
            $filesFieldsValue
        );
        $isoLang = Language::getIsoById($this->context->language->id);
        // @formatter:off
        $colissimoLinks = isset($this->colissimoLinks[$isoLang]) ? $this->colissimoLinks[$isoLang] : $this->colissimoLinks['en'];
        // @formatter:on
        $showMigration = $this->mustShowMigration();
        if ($showMigration) {
            $this->context->controller->addJS($this->localPath . 'views/js/colissimo.migration.js');
            if (Shop::getContext() != Shop::CONTEXT_ALL) {
                Shop::setContext(Shop::CONTEXT_ALL);
            }
        }
        $documentsDirData = ColissimoTools::getDocumentsDirDetails(dirname(__FILE__) . '/../documents/');
        $formData['documents_dir_size'] = ColissimoTools::formatDirectorySize($documentsDirData['total_size']);
        $formData['documents_dir_count'] = $documentsDirData['count'];
        $countriesList = [];
        $isoSender = array_merge(ColissimoTools::$isoSender, ColissimoTools::$isoOutreMer);
        foreach ($isoSender as $iso) {
            $countriesList[$iso] = Country::getNameById($this->context->language->id, Country::getByIso($iso));
        }
        $countries = Country::getCountries((int)$this->context->cookie->id_lang);
        $formData['default_origin_country'] = Configuration::get('COLISSIMO_DEFAULT_ORIGIN_COUNTRY');
        $this->context->smarty->assign(
            [
                'form_data' => $formData,
                'defaultCurrency' => Currency::getDefaultCurrency(),
                'address_countries' => $countriesList,
                'countries' => $countries,
                'widget_fonts' => self::$widgetFonts,
                'osm_number_pdr' => range(1, 20),
                'label_formats' => $this->labelFormats,
                'cn23_formats' => $this->cn23Formats,
                'usb_protocoles' => $this->usbPrinterProtocoles,
                'order_states' => $states,
                'colissimo_img_path' => $this->pathUri . 'views/img/',
                'colissimo_links' => $colissimoLinks,
                'show_migration' => $showMigration,
                'modules_to_migrate' => array_reverse($this->modulesToMigrate),
                'module_version' => $this->version,
            ]
        );
        $tpl = $this->context->smarty->fetch($this->localPath . 'views/templates/admin/configuration/layout.config.tpl');

        return $tpl;
    }

    /**
     * @return bool
     */
    public function mustShowMigration()
    {
        if ((int)Configuration::getGlobalValue('COLISSIMO_SHOW_MIGRATION') === -1) {
            return false;
        }
        foreach ($this->migrateModuleFromList as $module) {
            if (Module::isInstalled($module) && Module::isEnabled($module)) {
                $this->modulesToMigrate[] = $module;
            }
        }

        return !empty($this->modulesToMigrate);
    }
}

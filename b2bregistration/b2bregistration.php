<?php
/**
 * DISCLAIMER.
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FME Modules
 *  @copyright 2023 FME Modules
 *  @license   Comerical Licence
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__) . '/models/businessAccountModel.php';
include_once dirname(__FILE__) . '/models/b2bCustomFields.php';
include_once dirname(__FILE__) . '/models/b2bFieldsData.php';
include_once dirname(__FILE__) . '/models/BBProfile.php';
include_once dirname(__FILE__) . '/classes/b2bvatnumber.php';

class B2bregistration extends Module
{
    protected $config_form = false;
    protected $tab_parent_class;
    private $tab_class = 'B2BRegistration';
    private $tab_module = 'b2bregistration';
    public $markedRequired = [];
    public $id_shop;

    public $id_shop_group;

    public function __construct()
    {
        $this->name = 'b2bregistration';
        $this->tab = 'front_office_features';
        $this->version = '2.0.1';
        $this->author = 'FMM Modules';
        $this->need_instance = 0;
        $this->controllers = ['business'];
        $this->module_key = '6440dbe808c1bfe3b8a16dfc0ac664ec';
        $this->author_address = '0xcC5e76A6182fa47eD831E43d80Cd0985a14BB095';
        /*
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('B2B Registration');
        $this->description = $this->l('Offers a custom signup form for B2B customers or wholesalers');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall my module?');
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        $this->trans = $this->getTranslatableText();
        $this->file_errors = $this->getFileErrors();
        $this->markedRequired = BusinessAccountModel::getFieldsRequiredDB();
        $this->translations = [
            'first_name_required' => $this->l('Please enter first name'),
            'first_name_valid' => $this->l('Please enter valid first name'),
            'last_name_required' => $this->l('Please enter last name'),
            'last_name_valid' => $this->l('Please enter valid last name'),
            'address_alias_required' => $this->l('Please enter address alias e.g Home'),
            'address_required' => $this->l('Please enter address'),
            'address_valid' => $this->l('Please enter valid address'),
            'city_required' => $this->l('Please enter city name'),
            'city_valid' => $this->l('Please enter valid city name'),
            'website_required' => $this->l('Please enter website link'),
            'company_required' => $this->l('Please enter company name'),
            'siret_required' => $this->l('Please enter identification/siret number'),
            'siret_valid' => $this->l('Please enter valid identification/siret number'),
            'siret_max_numbers' => $this->l('Please enter identification number between 1 to 16'),
            'email_required' => $this->l('Please enter email address'),
            'email_valid' => $this->l('Please enter valid email address'),
            'email_exist' => $this->l('Email already exists. Choose another one'),
            'password_required' => $this->l('Please enter password'),
            'vat_valid' => $this->l('Wrong  vat formate. Please use correct one.'),
            'vat_not_found' => $this->l('Vat number not found.'),
            'vat_api_error' => $this->l('VAT number validation service unavailable.'),
            'password_valid' => $this->l('Password length must be 5 or greater'),
            'confirm_required' => $this->l('Please enter confirmation password'),
            'confirm_valid' => $this->l('Both password does not match'),
            'invalid_birthday' => $this->l('Please Enter Valid Birth Date (E.g.: 1970-12-31)'),
            'empty_birthday' => $this->l('Please Enter Birth Date (E.g.: 1970-12-31)'),
            'email_send' => $this->l('Email sent successfully'),
            'b2b_link_text' => $this->l('Register as B2B'),
            'validate_account' => $this->l('Your account is pending for validation and will be activated soon'),
            'update_account' => $this->l('Your information is updated successfully.'),
            'country_required' => $this->l('Please select country.'),
            'state_required' => $this->l('please select state.'),
            'is_required' => $this->l('is required'),
        ];
        $this->setShopContextIds();
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update.
     */
    public function install()
    {
        include dirname(__FILE__) . '/sql/install.php';
        if (!BusinessAccountModel::existsTab($this->tab_class)) {
            if (!$this->addTab($this->tab_class, 0)) {
                return false;
            }
        }
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        Configuration::updateValue('B2BREGISTRATION_TOKEN', Tools::substr(Tools::encrypt($this->name . '-' . date('c')), 0, 32));
        Configuration::updateValue('B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR', '#25b9d7', false, $this->id_shop_group, $this->id_shop);
        Configuration::updateValue('B2BREGISTRATION_TOPLINK_TEXT_COLOR', '#fff', false, $this->id_shop_group, $this->id_shop);
        Configuration::updateValue('B2BREGISTRATION_TOPLINK_POSITION', 'header', false, $this->id_shop_group, $this->id_shop);

        return parent::install()
        && $this->registerHook('displayheader')
        && $this->registerHook('displayBackOfficeHeader')
        && $this->registerHook('displayTop')
        && $this->registerHook('displayNav2')
        && $this->registerHook('displayNav')
        && $this->registerHook('ModuleRoutes')
        && $this->registerHook('actionDeleteGDPRCustomer')
        && $this->registerHook('registerGDPRConsent')
        && $this->registerHook('actionExportGDPRData')
        && $this->registerHook('actionObjectCustomerDeleteAfter')
        && $this->registerHook('actionObjectCustomerUpdateAfter')
        && $this->registerHook('displayCustomerAccount')
        && $this->registerHook('displayBanner')
        && $this->addDefaultValues()
        && $this->createB2BGroup();
    }

    public function uninstall()
    {
        include dirname(__FILE__) . '/sql/uninstall.php';
        if (_PS_VERSION_ < 1.7) {
            $this->removeTab($this->tab_class);
        }

        return parent::uninstall()
        && BusinessAccountModel::deleteDefaultValues();
    }

    public function setShopContextIds()
    {
        if ($this->id_shop === null || !Shop::isFeatureActive()) {
            $this->id_shop = Shop::getContextShopID();
        } else {
            $this->id_shop = Context::getContext()->shop->id;
        }
        if ($this->id_shop_group === null || !Shop::isFeatureActive()) {
            $this->id_shop_group = Shop::getContextShopGroupID();
        } else {
            $this->id_shop_group = Context::getContext()->shop->id_shop_group;
        }
    }

    protected function getTranslatableText()
    {
        return [
            'invalid' => $this->l('field value is invalid.'),
            'required' => $this->l('field is required.'),
            'type' => $this->l('invalid file type.'),
            'size' => $this->l('size exceeds the limit.'),
            'limit' => $this->l('character size exceeds the limit.'),
            'update_success' => $this->l('Registration fields updated successfully.'),
            'upload_error' => $this->l('An error occurred while attempting to upload the file.'),
        ];
    }

    /**
     * return file errors
     *
     * @return array
     */
    protected function getFileErrors()
    {
        return [
            'UPLOAD_ERR_NO_FILE' => $this->l('No file was uploaded.'),
            'UPLOAD_ERR_NO_TMP_DIR' => $this->l('Missing a temporary folder.'),
            'UPLOAD_ERR_CANT_WRITE' => $this->l('Failed to write file to disk.'),
            'UPLOAD_ERR_EXTENSION' => $this->l('A PHP extension stopped the file upload.'),
            'UPLOAD_ERR_PARTIAL' => $this->l('The uploaded file was only partially uploaded.'),
            'UPLOAD_ERR_INI_SIZE' => $this->l('The uploaded file exceeds the upload_max_filesize directive in php.ini'),
            'UPLOAD_ERR_FORM_SIZE' => $this->l('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form'),
        ];
    }

    protected function addTab($tab_class, $id_parent)
    {
        $tab = new Tab();
        $tab->class_name = $tab_class;
        $tab->id_parent = $id_parent;
        $tab->module = $this->tab_module;
        $tab->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('B2B Registration');
        $tab->enabled = false;
        $tab->add();

        $subtab1 = new Tab();
        $subtab1->class_name = 'AdminB2BCustomers';
        $subtab1->id_parent = Tab::getIdFromClassName($tab_class);
        $subtab1->module = $this->tab_module;
        $subtab1->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Manage B2B Customers');
        $subtab1->add();

        $subtab2 = new Tab();
        $subtab2->class_name = 'AdminB2BCustomFields';
        $subtab2->id_parent = Tab::getIdFromClassName($tab_class);
        $subtab2->module = $this->tab_module;
        $subtab2->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Add B2B Custom Fields');
        $subtab2->add();

        $subtab3 = new Tab();
        $subtab3->class_name = 'AdminB2BProfiles';
        $subtab3->id_parent = Tab::getIdFromClassName($tab_class);
        $subtab3->module = $this->tab_module;
        $subtab3->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('B2B Profiles');
        $subtab3->add();

        return true;
    }

    private function removeTab($tabClass)
    {
        $idTab = Tab::getIdFromClassName($tabClass);
        if ($idTab != 0) {
            $tab = new Tab($idTab);
            $tab->delete();

            return true;
        }

        return false;
    }

    /**
     * Load the configuration form.
     */
    public function getContent()
    {
        if (Tools::getvalue('action') == 'savePrefix') {
            $ajax_token = pSQL(Tools::getValue('ajax_token'));
            $token = Configuration::get('B2BREGISTRATION_TOKEN');
            if ($ajax_token == $token) {
                $obj = new Gender();
                $gender = (int) Tools::getValue('gender');
                $languages = Language::getLanguages();
                $obj->type = $gender;
                foreach ($languages as $lang) {
                    $prefix_name = pSQL(Tools::getValue('prefix_text_' . $lang['id_lang']));
                    $obj->name[$lang['id_lang']] = $prefix_name;
                }
                $result = $obj->save();
                exit(json_encode($result));
            }
        }
        if (((bool) Tools::isSubmit('submitB2bregistrationModule')) == true) {
            $action = $this->postProcess();
        }
        $this->context->smarty->assign('module_dir', $this->_path);

        return $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $this->html = $this->display(__FILE__, 'views/templates/hook/info.tpl');
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $action = 'general_settings';
        if (Tools::getValue('action')) {
            $action = Tools::getValue('action');
        }
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitB2bregistrationModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&action=' . $action;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $groups = Group::getGroups($this->context->language->id, $this->context->shop->id);
        // List of CMS Pages
        $cms_pages = [];
        foreach (CMS::listCms($this->context->language->id) as $cms_page) {
            $cms_pages[] = ['id' => $cms_page['id_cms'], 'name' => $cms_page['meta_title']];
        }
        $cms_page_rule = [];
        foreach (CMS::listCms($this->context->language->id) as $cms_page) {
            $cms_page_rule[] = ['id' => $cms_page['id_cms'], 'name' => $cms_page['meta_title']];
        }
        $cpGroups = (Configuration::get(
            'B2BREGISTRATION_GROUPS',
            false,
            $this->id_shop_group,
            $this->id_shop
        )) ? explode(',', Configuration::get(
            'B2BREGISTRATION_GROUPS',
            false,
            $this->id_shop_group,
            $this->id_shop
        )) : [];
        $cpGender = (Configuration::get(
            'B2BREGISTRATION_NAME_PREFIX_OPTIONS',
            false,
            $this->id_shop_group,
            $this->id_shop
        )) ? explode(',', Configuration::get(
            'B2BREGISTRATION_NAME_PREFIX_OPTIONS',
            false,
            $this->id_shop_group,
            $this->id_shop
        )) : [];
        $admin_email_sender = pSQL(Configuration::get(
            'B2BREGISTRATION_ADMIN_EMAIL_SENDER',
            false,
            $this->id_shop_group,
            $this->id_shop
        ));
        $selected_page = pSQL(Configuration::get(
            'B2BREGISTRATION_CMS_PAGES',
            false,
            $this->id_shop_group,
            $this->id_shop
        ));
        $selected_page_rule = pSQL(Configuration::get(
            'B2BREGISTRATION_CMS_PAGES_RULE',
            false,
            $this->id_shop_group,
            $this->id_shop
        ));
        $genders = businessAccountModel::getAllGenders($this->context->language->id);
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'groups' => $groups,
            'genders' => $genders,
            'cpGroups' => $cpGroups,
            'cpGender' => $cpGender,
            'selected_page' => $selected_page,
            'selected_page_rule' => $selected_page_rule,
            'cms_pages' => $cms_pages,
            'cms_page_rule' => $cms_page_rule,
            'ps_version' => _PS_VERSION_,
            'admin_email_sender' => $admin_email_sender,
            'ajax_token' => Configuration::get('B2BREGISTRATION_TOKEN'),
        ];
        $this->context->smarty->assign([
            'custom_fields' => $this->context->link->getAdminLink('AdminB2BCustomFields'),
            'manage_b2b_customers' => $this->context->link->getAdminLink('AdminB2BCustomers'),
            'b2b_profiles' => $this->context->link->getAdminLink('AdminB2BProfiles'),
            'general_settings' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&action=general_settings',
            'default_profile' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&action=default_profile',
        ]);
        $fields_form = $this->getConfigForm();
        if (Shop::isFeatureActive()) {
            $fields_form['form']['input'][] = [
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
                'col' => '6',
            ];
        }

        return $this->html . $helper->generateForm([$fields_form]);
    }

    public function init()
    {
        parent::init();
        $this->ajax = (bool) Tools::getValue('ajax', false);
    }

    /**
     * Handle Request for opening fancybox for new prefixes.
     */
    public function ajaxProcessOpenPrefixesDialog()
    {
        $token = Configuration::get('B2BREGISTRATION_TOKEN');
        $ajax_token = pSQL(Tools::getValue('ajax_token'));
        if ($ajax_token == $token) {
            $languages = Language::getLanguages();
            $defaultFormLanguage = (int) $this->context->employee->id_lang;
            $current_index = $this->context->link->getAdminLink('AdminModules', false);
            $current_token = Tools::getAdminTokenLite('AdminModules');
            $action_url = $current_index . '&configure=' . $this->name . '&token=' . $current_token;
            $this->context->smarty->assign([
                'languages' => $languages,
                'defaultFormLanguage' => $defaultFormLanguage,
                'action_url' => $action_url,
                'ps_version' => _PS_VERSION_,
                'ajax_token' => Configuration::get('B2BREGISTRATION_TOKEN'),
            ]);
            $res = $this->context->smarty->fetch(
                _PS_MODULE_DIR_ .
                'b2bregistration/views/templates/admin/prefix/new_prefix.tpl'
            );
            exit(json_encode($res));
        }
    }

    /**
     * Handle Request for deleting prefixes.
     */
    public function ajaxProcessDeletePrefix()
    {
        $token = Configuration::get('B2BREGISTRATION_TOKEN');
        $ajax_token = pSQL(Tools::getValue('ajax_token'));
        if ($token == $ajax_token) {
            $id = (int) Tools::getValue('id_prefix');
            $obj = new Gender($id);
            $result = $obj->delete();
            exit(json_encode($result));
        }
    }

    protected function getGeneralSettings()
    {
        $switch_option = (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) ? 'switch' : 'radio';

        return [[
            'type' => $switch_option,
            'label' => $this->l('Disable Normal Registration'),
            'name' => 'B2BREGISTRATION_NORMAL_REGISTRATION',
            'is_bool' => true,
            'desc' => $this->l('Use this to enable and disable normal registration'),
            'values' => [
                [
                    'id' => 'normal_on',
                    'value' => true,
                    'label' => $this->l('Enabled'),
                ],
                [
                    'id' => 'normal_off',
                    'value' => false,
                    'label' => $this->l('Disabled'),
                ],
            ],
            'tab' => 'general_settings',
        ],

            [
                'type' => 'color',
                'label' => $this->l('Top link background color'),
                'name' => 'B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR',
                'tab' => 'general_settings',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Top link Text color'),
                'name' => 'B2BREGISTRATION_TOPLINK_TEXT_COLOR',
                'tab' => 'general_settings',
            ],
            [
                'type' => 'select',
                'label' => $this->l('Top Link Position:'),
                'name' => 'B2BREGISTRATION_TOPLINK_POSITION',
                'options' => [
                    'query' => [
                        ['id' => 'header', 'name' => $this->l('header')],
                        ['id' => 'nav', 'name' => $this->l('Navbar')],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
                'tab' => 'general_settings',
            ],
            [
                'type' => $switch_option,
                'label' => $this->l('Vat Validation'),
                'name' => 'B2BREGISTRATION_ENABLE_DISABLE_VAT_VALIDATION',
                'is_bool' => true,
                'desc' => $this->l('Use this to enable and disable vat validation for only European countries'),
                'values' => [
                    [
                        'id' => 'vat_link_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'vat_link_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'general_settings',
            ],
            [
                'type' => $switch_option,
                'label' => $this->l('Country Required'),
                'name' => 'B2BREGISTRATION_COUNTRY_ENABLE_DISABLE',
                'is_bool' => true,
                'desc' => $this->l('Enable this to set country as a required field in default address.'),
                'values' => [
                    [
                        'id' => 'country_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'country_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'general_settings',
            ],
            [
                'type' => $switch_option,
                'label' => $this->l('State Required'),
                'name' => 'B2BREGISTRATION_STATE_ENABLE_DISABLE',
                'is_bool' => true,
                'desc' => $this->l('Enable this to set state as a required field in default'),
                'values' => [
                    [
                        'id' => 'state_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'state_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'general_settings',
            ],
        ];
    }

    protected function getDefaultProfileSettings()
    {
        $switch_option = (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) ? 'switch' : 'radio';

        return [
            [
                'type' => $switch_option,
                'label' => $this->l('Enable Default Profile'),
                'name' => 'B2BREGISTRATION_ENABLE_DISABLE',
                'is_bool' => true,
                'desc' => $this->l('Use this to enable and disable default profile'),
                'values' => [
                    [
                        'id' => 'auto_onn',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'auto_offf',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'basic_profile',
            ],
            [
                'label' => 'Registration Form URL Key',
                'type' => 'text',
                'name' => 'B2BREGISTRATION_URL_KEY',
                'desc' => $this->l('Frontend Default: b2b-customer-create'),
                'col' => '5',
                'lang' => true,
                'required' => true,
                'tab' => 'basic_profile',
            ],
            [
                'type' => $switch_option,
                'label' => $this->l('Enable Top Link in Header'),
                'name' => 'B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE',
                'is_bool' => true,
                'desc' => $this->l('Use this to enable and disable top link in header at front office'),
                'values' => [
                    [
                        'id' => 'link_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'link_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'basic_profile',
            ],
            [
                'label' => $this->l('Top Link Text'),
                'type' => 'text',
                'name' => 'B2BREGISTRATION_URL_TEXT',
                'col' => '5',
                'lang' => true,
                'required' => true,
                'tab' => 'basic_profile',
            ],
            [
                'type' => $switch_option,
                'label' => $this->l('B2B Customer Auto Approvel'),
                'name' => 'B2BREGISTRATION_AUTO_APPROVEL',
                'is_bool' => true,
                'desc' => $this->l('Use this to enable and disable b2b customer auto approvel'),
                'values' => [
                    [
                        'id' => 'auto_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'auto_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'basic_profile',
            ],
            [
                'type' => $switch_option,
                'label' => $this->l('Enable Custom Fields'),
                'name' => 'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS',
                'is_bool' => true,
                'desc' => $this->l('Use this to enable and disable custom fileds'),
                'values' => [
                    [
                        'id' => 'cs_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'cs_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'basic_profile',
            ],

            [
                'label' => $this->l('Personal Data Heading'),
                'type' => 'text',
                'name' => 'B2BREGISTRATION_PERSONAL_TEXT',
                'col' => '5',
                'lang' => true,
                'required' => true,
                'tab' => 'advance_profile',
            ],
            [
                'label' => $this->l('Company Data Heading'),
                'type' => 'text',
                'name' => 'B2BREGISTRATION_COMPANY_TEXT',
                'col' => '5',
                'lang' => true,
                'required' => true,
                'tab' => 'advance_profile',
            ],
            [
                'label' => $this->l('Signin Data Heading'),
                'type' => 'text',
                'name' => 'B2BREGISTRATION_SIGNIN_TEXT',
                'col' => '5',
                'lang' => true,
                'required' => true,
                'tab' => 'advance_profile',
            ],
            [
                'label' => $this->l('Address Data Heading'),
                'type' => 'text',
                'name' => 'B2BREGISTRATION_ADDRESS_TEXT',
                'col' => '5',
                'lang' => true,
                'required' => true,
                'tab' => 'advance_profile',
            ],
            [
                'label' => $this->l('Custom Field Heading'),
                'type' => 'text',
                'name' => 'B2BREGISTRATION_CUSTOM_FIELD_TEXT',
                'col' => '5',
                'lang' => true,
                'required' => true,
                'tab' => 'advance_profile',
            ],
            [
                'label' => $this->l('Pending Account Message Text for Email'),
                'type' => 'textarea',
                'name' => 'B2BREGISTRATION_ERROR_MSG_TEXT',
                'col' => 8,
                'autoload_rte' => true,
                'lang' => true,
                'required' => true,
                'tab' => 'basic_profile',
            ],
            [
                'type' => $switch_option,
                'label' => $this->l('CMS Page Rule'),
                'name' => 'B2BREGISTRATION_CMS_PAGES_RULE',
                'is_bool' => true,
                'desc' => $this->l('Use this to enable and disable cms page rule'),
                'values' => [
                    [
                        'id' => 'cms_link_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'cms_link_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'basic_profile',
            ],
            [
                'label' => $this->l('Choose CMS Page for Terms and Conditions'),
                'type' => 'B2BREGISTRATION_CMS_PAGES',
                'name' => 'B2BREGISTRATION_CMS_PAGES',
                'tab' => 'basic_profile',
            ],
            [
                'type' => 'textarea',
                'label' => $this->l('Pending Account Message Page for New Register customers'),
                'lang' => true,
                'name' => 'B2BREGISTRATION_CUSTOM_TEXT',
                'cols' => 40,
                'rows' => 10,
                'class' => 'rte',
                'autoload_rte' => true,
                'tab' => 'basic_profile',
            ],
            [
                'type' => $switch_option,
                'label' => $this->l('Enable Name Prefix'),
                'name' => 'B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE',
                'is_bool' => true,
                'values' => [
                    [
                        'id' => 'prefix_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'prefix_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'basic_profile',
            ],
            [
                'label' => $this->l('Name Prefix Dropdown Options'),
                'type' => 'B2BREGISTRATION_NAME_PREFIX_OPTIONS',
                'name' => 'B2BREGISTRATION_NAME_PREFIX_OPTIONS',
                'required' => true,
                'tab' => 'basic_profile',
            ],
            [
                'type' => $switch_option,
                'label' => $this->l('Enable Name Suffix'),
                'name' => 'B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE',
                'is_bool' => true,
                'values' => [
                    [
                        'id' => 'suffix_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'suffix_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'basic_profile',
            ],
            [
                'label' => $this->l('Name Suffix Dropdown Options'),
                'type' => 'text',
                'name' => 'B2BREGISTRATION_NAME_SUFFIX_OPTIONS',
                'desc' => $this->l('Comma (,) separated values.e.g MD,PHD'),
                'col' => '5',
                'required' => true,
                'tab' => 'basic_profile',
            ],
            [
                'type' => $switch_option,
                'label' => $this->l('Enable Middle Name'),
                'name' => 'B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE',
                'is_bool' => true,
                'values' => [
                    [
                        'id' => 'middle_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'middle_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'basic_profile',
            ],
            [
                'type' => $switch_option,
                'label' => $this->l('Enable Group Selection'),
                'name' => 'B2BREGISTRATION_GROUP_ENABLE_DISABLE',
                'is_bool' => true,
                'values' => [
                    [
                        'id' => 'group_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'group_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'basic_profile',
            ],
            [
                'type' => 'group',
                'label' => $this->l('Selected Groups'),
                'desc' => $this->l('Enable groups for front form'),
                'name' => 'groupBox',
                'values' => Group::getGroups(Context::getContext()->language->id, $this->context->shop->id),
                'tab' => 'basic_profile',
            ],
            [
                'label' => $this->l('Assign Groups'),
                'type' => 'B2BREGISTRATION_GROUPS',
                'name' => 'B2BREGISTRATION_GROUPS',
                'required' => true,
                'tab' => 'basic_profile',
            ],
            [
                'type' => $switch_option,
                'label' => $this->l('Enable Date of Birth'),
                'name' => 'B2BREGISTRATION_DOB_ENABLE_DISABLE',
                'is_bool' => true,
                'values' => [
                    [
                        'id' => 'dob_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'dob_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'basic_profile',
            ],
            [
                'type' => $switch_option,
                'label' => $this->l('Enable IDENTIFICATION/Siret Number'),
                'name' => 'B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE',
                'is_bool' => true,
                'values' => [
                    [
                        'id' => 'identification_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'identification_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'basic_profile',
            ],
            [
                'type' => $switch_option,
                'label' => $this->l('Enable Website'),
                'name' => 'B2BREGISTRATION_WEBSITE_ENABLE_DISABLE',
                'is_bool' => true,
                'values' => [
                    [
                        'id' => 'gender_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'gender_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'basic_profile',
            ],
            [
                'type' => $switch_option,
                'label' => $this->l('Enable Address'),
                'name' => 'B2BREGISTRATION_ADDRESS_ENABLE_DISABLE',
                'is_bool' => true,
                'values' => [
                    [
                        'id' => 'address_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'address_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'basic_profile',
            ],
        ];
    }

    protected function getFieldSettings()
    {
        $switch_option = (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) ? 'switch' : 'radio';

        return [
        ];
    }

    protected function getNotificationSettings()
    {
        $switch_option = (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) ? 'switch' : 'radio';

        return [
            [
                'type' => $switch_option,
                'label' => $this->l('Send Email Notification to Admin'),
                'name' => 'B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE',
                'is_bool' => true,
                'desc' => $this->l('Use this to enable and disable email notifications for admin'),
                'values' => [
                    [
                        'id' => 'admin_e_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'admin_e_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'notification_settings',
            ],
            [
                'type' => 'text',
                'label' => $this->l('Admin Email ID'),
                'name' => 'B2BREGISTRATION_ADMIN_EMAIL_ID',
                'col' => '5',
                'required' => true,
                'tab' => 'notification_settings',
            ],
            [
                'label' => $this->l('Email Sender'),
                'type' => 'B2BREGISTRATION_ADMIN_EMAIL_SENDER',
                'name' => 'B2BREGISTRATION_ADMIN_EMAIL_SENDER',
                'tab' => 'notification_settings',
            ],
            [
                'type' => $switch_option,
                'label' => $this->l('Send Email Notification to Customer'),
                'name' => 'B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE',
                'is_bool' => true,
                'desc' => $this->l('Use this to enable and disable email notifications for customer'),
                'values' => [
                    [
                        'id' => 'customer_e_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'customer_e_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'notification_settings',
            ],
        ];
    }

    protected function getRecaptchaSettings()
    {
        $switch_option = (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) ? 'switch' : 'radio';

        return [
            [
                'type' => $switch_option,
                'label' => $this->l('Google reCAPTCHA'),
                'name' => 'B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE',
                'is_bool' => true,
                'values' => [
                    [
                        'id' => 'captcha_e_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'captcha_e_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
                'tab' => 'recaptcha_settings',
            ],
            [
                'label' => $this->l('Site Key'),
                'type' => 'B2BREGISTRATION_SITE_KEY',
                'name' => 'B2BREGISTRATION_SITE_KEY',
                'tab' => 'recaptcha_settings',
            ],
            [
                'label' => $this->l('Secret key'),
                'type' => 'B2BREGISTRATION_SECRET_KEY',
                'name' => 'B2BREGISTRATION_SECRET_KEY',
                'tab' => 'recaptcha_settings',
            ],
        ];
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        if (Tools::getValue('action') == 'default_profile') {
            return [
                'form' => [
                    'menu' => [],
                    'tinymce' => true,
                    'tabs' => [
                        'basic_profile' => $this->l('Basic'),
                        'advance_profile' => $this->l('Headings'),
                    ],

                    'input' => array_merge(
                        $this->getDefaultProfileSettings()
                    ),
                    'submit' => [
                        'title' => $this->l('Save'),
                        'name' => 'submitdefault' . $this->name,
                    ],
                ],
            ];
        } else {
            return [
                'form' => [
                    'menu' => [],
                    'tinymce' => true,
                    'tabs' => [
                        'general_settings' => $this->l('General Settings'),
                        'notification_settings' => $this->l('Notification Settings'),
                        'recaptcha_settings' => $this->l('Recaptcha Settings'),
                    ],

                    'input' => array_merge(
                        $this->getGeneralSettings(),
                        $this->getNotificationSettings(),
                        $this->getRecaptchaSettings()
                    ),
                    'submit' => [
                        'title' => $this->l('Save'),
                    ],
                ],
            ];
        }
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $languages = Language::getLanguages(false);
        $field = [];
        foreach ($languages as $lang) {
            $field['B2BREGISTRATION_URL_KEY'][$lang['id_lang']] = pSQL(Tools::getValue(
                'B2BREGISTRATION_URL_KEY_' . $lang['id_lang'],
                Configuration::get(
                    'B2BREGISTRATION_URL_KEY',
                    (int) $lang['id_lang'],
                    $this->id_shop_group,
                    $this->id_shop
                )
            ));
            $field['B2BREGISTRATION_URL_TEXT'][$lang['id_lang']] = pSQL(Tools::getValue(
                'B2BREGISTRATION_URL_TEXT_' . $lang['id_lang'],
                Configuration::get(
                    'B2BREGISTRATION_URL_TEXT',
                    (int) $lang['id_lang'],
                    $this->id_shop_group,
                    $this->id_shop
                )
            ));

            $field['B2BREGISTRATION_PERSONAL_TEXT'][$lang['id_lang']] = pSQL(Tools::getValue(
                'B2BREGISTRATION_PERSONAL_TEXT_' . $lang['id_lang'],
                Configuration::get(
                    'B2BREGISTRATION_PERSONAL_TEXT',
                    (int) $lang['id_lang'],
                    $this->id_shop_group,
                    $this->id_shop
                )
            ));
            $field['B2BREGISTRATION_SIGNIN_TEXT'][$lang['id_lang']] = pSQL(Tools::getValue(
                'B2BREGISTRATION_SIGNIN_TEXT_' . $lang['id_lang'],
                Configuration::get(
                    'B2BREGISTRATION_SIGNIN_TEXT',
                    (int) $lang['id_lang'],
                    $this->id_shop_group,
                    $this->id_shop
                )
            ));
            $field['B2BREGISTRATION_ADDRESS_TEXT'][$lang['id_lang']] = pSQL(Tools::getValue(
                'B2BREGISTRATION_ADDRESS_TEXT_' . $lang['id_lang'],
                Configuration::get(
                    'B2BREGISTRATION_ADDRESS_TEXT',
                    (int) $lang['id_lang'],
                    $this->id_shop_group,
                    $this->id_shop
                )
            ));
            $field['B2BREGISTRATION_COMPANY_TEXT'][$lang['id_lang']] = pSQL(Tools::getValue(
                'B2BREGISTRATION_COMPANY_TEXT_' . $lang['id_lang'],
                Configuration::get(
                    'B2BREGISTRATION_COMPANY_TEXT',
                    (int) $lang['id_lang'],
                    $this->id_shop_group,
                    $this->id_shop
                )
            ));
            $field['B2BREGISTRATION_CUSTOM_FIELD_TEXT'][$lang['id_lang']] = pSQL(Tools::getValue(
                'B2BREGISTRATION_CUSTOM_FIELD_TEXT_' . $lang['id_lang'],
                Configuration::get(
                    'B2BREGISTRATION_CUSTOM_FIELD_TEXT',
                    (int) $lang['id_lang'],
                    $this->id_shop_group,
                    $this->id_shop
                )
            ));

            $field['B2BREGISTRATION_CUSTOM_TEXT'][$lang['id_lang']] = Tools::getValue(
                'B2BREGISTRATION_CUSTOM_TEXT' . $lang['id_lang'],
                Configuration::get(
                    'B2BREGISTRATION_CUSTOM_TEXT',
                    (int) $lang['id_lang'],
                    $this->id_shop_group,
                    $this->id_shop
                )
            );
            $field['B2BREGISTRATION_ERROR_MSG_TEXT'][$lang['id_lang']] = pSQL(Tools::getValue(
                'B2BREGISTRATION_ERROR_MSG_TEXT_' . $lang['id_lang'],
                Configuration::get(
                    'B2BREGISTRATION_ERROR_MSG_TEXT',
                    (int) $lang['id_lang'],
                    $this->id_shop_group,
                    $this->id_shop
                )
            ));
        }
        $field['B2BREGISTRATION_ENABLE_DISABLE'] = (int) Configuration::get(
            'B2BREGISTRATION_ENABLE_DISABLE',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_GROUP_ENABLE_DISABLE'] = (int) Configuration::get(
            'B2BREGISTRATION_GROUP_ENABLE_DISABLE',
            false,
            $this->id_shop_group,
            $this->id_shop
        );

        $field['B2BREGISTRATION_CMS_PAGES'] = (int) Configuration::get(
            'B2BREGISTRATION_CMS_PAGES',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_CMS_PAGES_RULE'] = (int) Configuration::get(
            'B2BREGISTRATION_CMS_PAGES_RULE',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_ENABLE_DISABLE_VAT_VALIDATION'] = (int) Configuration::get(
            'B2BREGISTRATION_ENABLE_DISABLE_VAT_VALIDATION',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE'] = (int) Configuration::get(
            'B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_AUTO_APPROVEL'] = (int) Configuration::get(
            'B2BREGISTRATION_AUTO_APPROVEL',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE'] = (int) Configuration::get(
            'B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_NAME_PREFIX_OPTIONS'] = pSQL(Configuration::get(
            'B2BREGISTRATION_NAME_PREFIX_OPTIONS',
            false,
            $this->id_shop_group,
            $this->id_shop
        ));
        $field['B2BREGISTRATION_GROUPS'] = pSQL(Configuration::get(
            'B2BREGISTRATION_GROUPS',
            false,
            $this->id_shop_group,
            $this->id_shop
        ));
        $field['B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE'] = (int) Configuration::get(
            'B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_NAME_SUFFIX_OPTIONS'] = pSQL(Configuration::get(
            'B2BREGISTRATION_NAME_SUFFIX_OPTIONS',
            false,
            $this->id_shop_group,
            $this->id_shop
        ));
        $field['B2BREGISTRATION_NORMAL_REGISTRATION'] = (int) Configuration::get(
            'B2BREGISTRATION_NORMAL_REGISTRATION',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR'] = (string) Configuration::get(
            'B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_TOPLINK_TEXT_COLOR'] = (string) Configuration::get(
            'B2BREGISTRATION_TOPLINK_TEXT_COLOR',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_TOPLINK_POSITION'] = (string) Configuration::get(
            'B2BREGISTRATION_TOPLINK_POSITION',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE'] = (int) Configuration::get(
            'B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_GROUPS'] = (int) Configuration::get(
            'B2BREGISTRATION_GROUPS',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_ADMIN_EMAIL_ID'] = pSQL(Configuration::get(
            'B2BREGISTRATION_ADMIN_EMAIL_ID',
            false,
            $this->id_shop_group,
            $this->id_shop
        ));
        $field['B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE'] = (int) Configuration::get(
            'B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_ADMIN_EMAIL_SENDER'] = (int) Configuration::get(
            'B2BREGISTRATION_ADMIN_EMAIL_SENDER',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE'] = (int) Configuration::get(
            'B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE'] = (int) Configuration::get(
            'B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE',
            false,
            $this->id_shop_group,
            $this->id_shop
        );
        $field['B2BREGISTRATION_SITE_KEY'] = pSQL(Configuration::get(
            'B2BREGISTRATION_SITE_KEY',
            false,
            $this->id_shop_group,
            $this->id_shop
        ));
        $field['B2BREGISTRATION_SECRET_KEY'] = pSQL(Configuration::get(
            'B2BREGISTRATION_SECRET_KEY',
            false,
            $this->id_shop_group,
            $this->id_shop
        ));
        $field['B2BREGISTRATION_DOB_ENABLE_DISABLE'] = pSQL(Configuration::get(
            'B2BREGISTRATION_DOB_ENABLE_DISABLE',
            false,
            $this->id_shop_group,
            $this->id_shop
        ));
        $field['B2BREGISTRATION_ENABLE_CUSTOM_FIELDS'] = pSQL(Configuration::get(
            'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS',
            false,
            $this->id_shop_group,
            $this->id_shop
        ));
        $field['B2BREGISTRATION_ADDRESS_ENABLE_DISABLE'] = pSQL(Configuration::get(
            'B2BREGISTRATION_ADDRESS_ENABLE_DISABLE',
            false,
            $this->id_shop_group,
            $this->id_shop
        ));
        $field['B2BREGISTRATION_COUNTRY_ENABLE_DISABLE'] = pSQL(Configuration::get(
            'B2BREGISTRATION_COUNTRY_ENABLE_DISABLE',
            false,
            $this->id_shop_group,
            $this->id_shop
        ));
        $field['B2BREGISTRATION_STATE_ENABLE_DISABLE'] = pSQL(Configuration::get(
            'B2BREGISTRATION_STATE_ENABLE_DISABLE',
            false,
            $this->id_shop_group,
            $this->id_shop
        ));
        $field['B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE'] = pSQL(Configuration::get(
            'B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE',
            false,
            $this->id_shop_group,
            $this->id_shop
        ));
        $field['B2BREGISTRATION_WEBSITE_ENABLE_DISABLE'] = pSQL(Configuration::get(
            'B2BREGISTRATION_WEBSITE_ENABLE_DISABLE',
            false,
            $this->id_shop_group,
            $this->id_shop
        ));
        // work for group selection
        $groups = Group::getGroups($this->context->language->id);

        $selectedGroups = Configuration::get(
            'B2BREGISTRATION_GROUP_SELECTION',
            null,
            $this->id_shop_group,
            $this->id_shop
        );
        $groups_ids = explode(',', $selectedGroups);

        foreach ($groups as $group) {
            $field['groupBox_' . $group['id_group']] = Tools::getValue(
                'groupBox_' . $group['id_group'],
                in_array($group['id_group'], $groups_ids)
            );
        }

        return $field;
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        if (Tools::isSubmit('submitdefault' . $this->name)) {
            $B2BREGISTRATION_ENABLE_DISABLE = (int) Tools::getValue('B2BREGISTRATION_ENABLE_DISABLE');
            $B2BREGISTRATION_GROUP_ENABLE_DISABLE = (int) Tools::getValue('B2BREGISTRATION_GROUP_ENABLE_DISABLE');
            $B2BREGISTRATION_AUTO_APPROVEL = (int) Tools::getValue('B2BREGISTRATION_AUTO_APPROVEL');
            $B2BREGISTRATION_CMS_PAGES = (int) Tools::getValue('B2BREGISTRATION_CMS_PAGES');
            $B2BREGISTRATION_NAME_SUFFIX_OPTIONS = pSQL(Tools::getValue('B2BREGISTRATION_NAME_SUFFIX_OPTIONS'));
            $B2BREGISTRATION_GROUP_SELECTION = Tools::getValue('groupBox');
            $B2BREGISTRATION_ENABLE_CUSTOM_FIELDS = (int) Tools::getValue(
                'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS'
            );
            $B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE = (int) Tools::getValue(
                'B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE'
            );
            $B2BREGISTRATION_GROUPS = (int) Tools::getValue('B2BREGISTRATION_GROUPS');
            $B2BREGISTRATION_CMS_PAGES_RULE = (int) Tools::getValue(
                'B2BREGISTRATION_CMS_PAGES_RULE'
            );
            $B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE = (int) Tools::getValue(
                'B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE'
            );

            $B2BREGISTRATION_NAME_PREFIX_OPTIONS = Tools::getValue('B2BREGISTRATION_NAME_PREFIX_OPTIONS');
            $B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE = (int) Tools::getValue(
                'B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE'
            );
            $languages = Language::getLanguages(false);
            if ($B2BREGISTRATION_NAME_PREFIX_OPTIONS != null) {
                $B2BREGISTRATION_NAME_PREFIX_OPTIONS = implode(
                    ',',
                    Tools::getValue('B2BREGISTRATION_NAME_PREFIX_OPTIONS')
                );
            }
            $B2BREGISTRATION_DOB_ENABLE_DISABLE = pSQL(Tools::getValue('B2BREGISTRATION_DOB_ENABLE_DISABLE'));
            $B2BREGISTRATION_ADDRESS_ENABLE_DISABLE = pSQL(Tools::getValue('B2BREGISTRATION_ADDRESS_ENABLE_DISABLE'));
            $B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE = pSQL(Tools::getValue(
                'B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE'
            ));
            $B2BREGISTRATION_WEBSITE_ENABLE_DISABLE = pSQL(Tools::getValue('B2BREGISTRATION_WEBSITE_ENABLE_DISABLE'));
            $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
            $lang = $lang->id;
            $B2BREGISTRATION_URL_TEXT = pSQL(Tools::getValue('B2BREGISTRATION_URL_TEXT_' . $lang));
            $B2BREGISTRATION_PERSONAL_TEXT = pSQL(Tools::getValue('B2BREGISTRATION_PERSONAL_TEXT_' . $lang));
            $B2BREGISTRATION_COMPANY_TEXT = pSQL(Tools::getValue('B2BREGISTRATION_COMPANY_TEXT_' . $lang));
            $B2BREGISTRATION_SIGNIN_TEXT = pSQL(Tools::getValue('B2BREGISTRATION_SIGNIN_TEXT_' . $lang));
            $B2BREGISTRATION_ADDRESS_TEXT = pSQL(Tools::getValue('B2BREGISTRATION_ADDRESS_TEXT_' . $lang));
            $B2BREGISTRATION_URL_KEY = pSQL(Tools::getValue('B2BREGISTRATION_URL_KEY_' . $lang));
            $B2BREGISTRATION_CUSTOM_FIELD_TEXT = pSQL(Tools::getValue(
                'B2BREGISTRATION_CUSTOM_FIELD_TEXT_' . $lang
            ));
            $B2BREGISTRATION_CUSTOM_TEXT = Tools::getValue(
                'B2BREGISTRATION_CUSTOM_TEXT_' . $lang
            );
            $B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE = (int) Tools::getValue('B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE');
            $B2BREGISTRATION_ERROR_MSG_TEXT = pSQL(Tools::getValue(
                'B2BREGISTRATION_ERROR_MSG_TEXT_' . $lang
            ));
            if (!empty($B2BREGISTRATION_GROUP_SELECTION)) {
                $B2BREGISTRATION_GROUP_SELECTION = implode(
                    ',',
                    Tools::getValue('groupBox')
                );
            }

            if (empty($B2BREGISTRATION_URL_KEY)) {
                $this->context->controller->errors[] = $this->l('Please enter the url key');
            } elseif (empty($B2BREGISTRATION_URL_TEXT)) {
                $this->context->controller->errors[] = $this->l('Please enter the text of top link');
            } elseif (empty($B2BREGISTRATION_PERSONAL_TEXT)) {
                $this->context->controller->errors[] = $this->l('Please enter the text for personal data heading');
            } elseif (empty($B2BREGISTRATION_COMPANY_TEXT)) {
                $this->context->controller->errors[] = $this->l('Please enter the text for company data heading');
            } elseif (empty($B2BREGISTRATION_SIGNIN_TEXT)) {
                $this->context->controller->errors[] = $this->l('Please enter the text for signin data heading');
            } elseif (empty($B2BREGISTRATION_ADDRESS_TEXT)) {
                $this->context->controller->errors[] = $this->l('Please enter the text for address data heading');
            } elseif (empty($B2BREGISTRATION_CUSTOM_FIELD_TEXT)) {
                $this->context->controller->errors[] = $this->l('Please enter the text for Custom Field heading');
            } elseif (empty($B2BREGISTRATION_CUSTOM_TEXT)) {
                $this->context->controller->errors[] = $this->l('Please enter the content for custom page');
            } elseif (empty($B2BREGISTRATION_ERROR_MSG_TEXT)) {
                $this->context->controller->errors[] = $this->l('Please enter the text for error message');
            } elseif ($B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE == 1 && empty($B2BREGISTRATION_NAME_PREFIX_OPTIONS)) {
                $this->context->controller->errors[] = $this->l('Please check name prefix options');
            } elseif (Tools::getValue('B2BREGISTRATION_GROUP_ENABLE_DISABLE') == 1 && !Tools::getValue('groupBox')) {
                $this->context->controller->errors[] = $this->l('Selected groups cannot be empty when enabled');
            } else {
                $default_lang_id = (int) Configuration::get('PS_LANG_DEFAULT');

                $fields = [
                    'B2BREGISTRATION_URL_KEY',
                    'B2BREGISTRATION_PERSONAL_TEXT',
                    'B2BREGISTRATION_COMPANY_TEXT',
                    'B2BREGISTRATION_SIGNIN_TEXT',
                    'B2BREGISTRATION_ADDRESS_TEXT',
                    'B2BREGISTRATION_CUSTOM_FIELD_TEXT',
                    'B2BREGISTRATION_CUSTOM_TEXT',
                    'B2BREGISTRATION_URL_TEXT',
                    'B2BREGISTRATION_ERROR_MSG_TEXT',
                ];

                $values = [];

                foreach ($languages as $lang) {
                    $lang_id = (int) $lang['id_lang'];

                    foreach ($fields as $field) {
                        $value = Tools::getValue($field . '_' . $lang_id);
                        if ($value) {
                            $values[$field][$lang_id] = $value;
                        } else {
                            $values[$field][$lang_id] = Tools::getValue($field . '_' . $default_lang_id);
                        }
                    }

                    $meta = Meta::getMetaByPage('module-b2bregistration-business', (int) $lang_id);
                    $id_meta = $meta['id_meta'];
                    $meta_url = new Meta($id_meta, (int) $lang_id);
                    $meta_url->url_rewrite = $values['B2BREGISTRATION_URL_KEY'][$lang_id];
                    $meta_url->update();
                }
                Configuration::updateValue(
                    'B2BREGISTRATION_DOB_ENABLE_DISABLE',
                    $B2BREGISTRATION_DOB_ENABLE_DISABLE,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_ADDRESS_ENABLE_DISABLE',
                    $B2BREGISTRATION_ADDRESS_ENABLE_DISABLE,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE',
                    $B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE',
                    $B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_WEBSITE_ENABLE_DISABLE',
                    $B2BREGISTRATION_WEBSITE_ENABLE_DISABLE,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                // MultiLang Fields
                Configuration::updateValue(
                    'B2BREGISTRATION_URL_KEY',
                    $values['B2BREGISTRATION_URL_KEY'],
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_URL_TEXT',
                    $values['B2BREGISTRATION_URL_TEXT'],
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_PERSONAL_TEXT',
                    $values['B2BREGISTRATION_PERSONAL_TEXT'],
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_COMPANY_TEXT',
                    $values['B2BREGISTRATION_COMPANY_TEXT'],
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_SIGNIN_TEXT',
                    $values['B2BREGISTRATION_SIGNIN_TEXT'],
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_ADDRESS_TEXT',
                    $values['B2BREGISTRATION_ADDRESS_TEXT'],
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_CUSTOM_FIELD_TEXT',
                    $values['B2BREGISTRATION_CUSTOM_FIELD_TEXT'],
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_CUSTOM_TEXT',
                    $values['B2BREGISTRATION_CUSTOM_TEXT'],
                    true,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_ERROR_MSG_TEXT',
                    $values['B2BREGISTRATION_ERROR_MSG_TEXT'],
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE',
                    $B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_NAME_SUFFIX_OPTIONS',
                    $B2BREGISTRATION_NAME_SUFFIX_OPTIONS,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE',
                    $B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE',
                    $B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_GROUPS',
                    $B2BREGISTRATION_GROUPS,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_ENABLE_DISABLE',
                    $B2BREGISTRATION_ENABLE_DISABLE,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_GROUP_SELECTION',
                    $B2BREGISTRATION_GROUP_SELECTION,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_GROUP_ENABLE_DISABLE',
                    $B2BREGISTRATION_GROUP_ENABLE_DISABLE,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_AUTO_APPROVEL',
                    $B2BREGISTRATION_AUTO_APPROVEL,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS',
                    $B2BREGISTRATION_ENABLE_CUSTOM_FIELDS,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_CMS_PAGES_RULE',
                    $B2BREGISTRATION_CMS_PAGES_RULE,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE',
                    $B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_CMS_PAGES',
                    $B2BREGISTRATION_CMS_PAGES,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_NAME_PREFIX_OPTIONS',
                    $B2BREGISTRATION_NAME_PREFIX_OPTIONS,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                $this->context->controller->confirmations[] = $this->l('Update Successfully');
            }

            return 'default_profile';
        } else {
            $B2BREGISTRATION_ENABLE_DISABLE_VAT_VALIDATION = (int) Tools::getValue(
                'B2BREGISTRATION_ENABLE_DISABLE_VAT_VALIDATION'
            );
            $B2BREGISTRATION_COUNTRY_ENABLE_DISABLE = pSQL(Tools::getValue('B2BREGISTRATION_COUNTRY_ENABLE_DISABLE'));
            $B2BREGISTRATION_STATE_ENABLE_DISABLE = pSQL(Tools::getValue('B2BREGISTRATION_STATE_ENABLE_DISABLE'));

            $B2BREGISTRATION_NORMAL_REGISTRATION = (int) Tools::getValue(
                'B2BREGISTRATION_NORMAL_REGISTRATION'
            );
            $B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR = Tools::getValue(
                'B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR'
            );
            $B2BREGISTRATION_TOPLINK_TEXT_COLOR = Tools::getValue(
                'B2BREGISTRATION_TOPLINK_TEXT_COLOR'
            );
            $B2BREGISTRATION_TOPLINK_POSITION = Tools::getValue(
                'B2BREGISTRATION_TOPLINK_POSITION'
            );
            $B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE = (int) Tools::getValue(
                'B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE'
            );
            $B2BREGISTRATION_ADMIN_EMAIL_ID = pSQL(Tools::getValue('B2BREGISTRATION_ADMIN_EMAIL_ID'));
            $B2BREGISTRATION_ADMIN_EMAIL_SENDER = pSQL(Tools::getValue('B2BREGISTRATION_ADMIN_EMAIL_SENDER'));
            $B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE = (int) Tools::getValue(
                'B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE'
            );
            $B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE = (int) Tools::getValue('B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE');

            $B2BREGISTRATION_SITE_KEY = pSQL(Tools::getValue('B2BREGISTRATION_SITE_KEY'));
            $B2BREGISTRATION_SECRET_KEY = pSQL(Tools::getValue('B2BREGISTRATION_SECRET_KEY'));
            if (empty($B2BREGISTRATION_ADMIN_EMAIL_ID) && $B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE) {
                $this->context->controller->errors[] = $this->l('Please enter email for Admin');
            } elseif (!Validate::isEmail($B2BREGISTRATION_ADMIN_EMAIL_ID) && $B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE) {
                $this->context->controller->errors[] = $this->l('Please enter valid email for Admin');
            } else {
                Configuration::updateValue(
                    'B2BREGISTRATION_ENABLE_DISABLE_VAT_VALIDATION',
                    $B2BREGISTRATION_ENABLE_DISABLE_VAT_VALIDATION,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );

                Configuration::updateValue(
                    'B2BREGISTRATION_NORMAL_REGISTRATION',
                    $B2BREGISTRATION_NORMAL_REGISTRATION,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR',
                    $B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_TOPLINK_TEXT_COLOR',
                    $B2BREGISTRATION_TOPLINK_TEXT_COLOR,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_TOPLINK_POSITION',
                    $B2BREGISTRATION_TOPLINK_POSITION,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_COUNTRY_ENABLE_DISABLE',
                    $B2BREGISTRATION_COUNTRY_ENABLE_DISABLE,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_STATE_ENABLE_DISABLE',
                    $B2BREGISTRATION_STATE_ENABLE_DISABLE,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE',
                    $B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_ADMIN_EMAIL_SENDER',
                    $B2BREGISTRATION_ADMIN_EMAIL_SENDER,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_ADMIN_EMAIL_ID',
                    $B2BREGISTRATION_ADMIN_EMAIL_ID,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE',
                    $B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE',
                    $B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_SITE_KEY',
                    $B2BREGISTRATION_SITE_KEY,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );
                Configuration::updateValue(
                    'B2BREGISTRATION_SECRET_KEY',
                    $B2BREGISTRATION_SECRET_KEY,
                    false,
                    $this->id_shop_group,
                    $this->id_shop
                );

                $this->context->controller->confirmations[] = $this->l('Update Successfully');
            }
        }

        return 'general_settings';
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (Module::isInstalled('b2bregistration')
        ) {
            $this->context->smarty->assign([
                'custom_fields' => $this->context->link->getAdminLink('AdminB2BCustomFields'),
                'manage_b2b_customers' => $this->context->link->getAdminLink('AdminB2BCustomers'),
                'b2b_profiles' => $this->context->link->getAdminLink('AdminB2BProfiles'),
                'general_settings' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&action=general_settings',
                'default_profile' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&action=default_profile',
            ]);

            $current_index = $this->context->link->getAdminLink('AdminModules', false);
            $current_token = Tools::getAdminTokenLite('AdminModules');
            $action_url = $current_index .
            '&configure=' .
            $this->name .
            '&token=' .
            $current_token .
            '&tab_module=' .
            $this->tab .
            '&module_name=' .
            $this->name;
            Media::addJsDef([
                'config_url' => $action_url,
                'admin_url' => $this->context->link->getAdminLink('AdminB2BCustomers', true),
                'ajax_token' => Configuration::get('B2BREGISTRATION_TOKEN'),
            ]);
            $this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookDisplayHeader()
    {
        $enable_module = (int) Configuration::get(
            'B2BREGISTRATION_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );

        if (!$enable_module) {
            $b2b_links = BBProfile::getTopLinks();
            if ($b2b_links) {
                foreach ($b2b_links as $link) {
                    if ($link['active'] == 1) {
                        $enable_module = true;
                        break;
                    }
                }
            }
        }

        if ($enable_module) {
            $controller = Dispatcher::getInstance()->getController();
            $site_key = pSQL(Configuration::get(
                'B2BREGISTRATION_SITE_KEY',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            ));
            $normal_form = (int) Configuration::get(
                'B2BREGISTRATION_NORMAL_REGISTRATION',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $action = Tools::getValue('registration');
            if ($normal_form == 1) {
                if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
                    if ($controller == 'registration') {
                        Tools::redirect($this->context->link->getModuleLink('b2bregistration', 'business'));
                    }
                } else {
                    if ($controller == 'authentication') {
                        Tools::redirect($this->context->link->getModuleLink('b2bregistration', 'business'));
                    }
                }
            }
            Media::addJsDef([
                'controller_link' => $this->context->link->getModuleLink('b2bregistration', 'business'),
                'site_key' => $site_key,
                'controller' => $controller,
                'ps_version' => _PS_VERSION_,
                'create_account' => $this->l('Now you can create account as B2B'),
                'register_as_b2b' => $this->l('Register as B2B'),
                'normal_form' => $normal_form,
                'b2bregisteration_action_url' => Context::getContext()->link->getModuleLink('b2bregistration', 'business', ['action' => 'saveData', 'ajax' => true]),
                'state_selection_text' => $this->l('Please Choose'),
                'id_state' => (int) Tools::getValue('id_state'),
            ]);
            if ($controller == 'business' || $controller == 'b2b') {
                $this->context->controller->addJS($this->_path . '/views/js/front.js');
            }
            $this->context->controller->addJS($this->_path . '/views/js/block_normal_reg.js');
            $this->context->controller->addCSS($this->_path . '/views/css/front.css');
        }
    }

    public function hookDisplayBBFields($params)
    {
        $id_profile = (isset($params['id_profile']) && $params['id_profile']) ? (int) $params['id_profile'] : 0;
        $id_lang = $this->context->language->id;
        $objModel = new BToBCustomFields();
        $registration_fields = $objModel->getCustomFields($id_lang, $this->context->shop->id, $id_profile);
        $id_customer = (isset(
            $this->context->customer->id
        )) ? $this->context->customer->id : $this->context->cookie->id_customer;
        $id_guest = (!$id_customer) ? $this->context->cookie->id_guest : 0;
        $field = new BToBCustomFields();
        $fields = [];
        if ($id_customer || $id_guest) {
            $fields = $field->getAllFields(
                ($id_customer) ? 'val.id_customer = ' .
                    (int) $id_customer : 'val.id_guest = ' . (int) $id_guest,
                $id_lang,
                'a.position'
            );
            if (isset($fields) && $fields) {
                foreach ($fields as &$field) {
                    if (in_array(
                        $field['field_type'],
                        ['multiselect', 'radio', 'checkbox']
                    ) && $field['field_value_id']) {
                        $field['field_value'] = explode(',', $field['field_value_id']);
                    } elseif (in_array(
                        $field['field_type'],
                        ['message', 'select']
                    ) && $field['field_value_id']) {
                        $field['field_value'] = $field['field_value_id'];
                    }
                }
            }
        }

        $registration_fields_options = [];
        foreach ($registration_fields as $sf) {
            $registration_fields_options[$sf['id_bb_registration_fields']] = $objModel->getCustomFieldsValues(
                $sf['id_bb_registration_fields']
            );
        }

        if ($registration_fields_options) {
            $this->context->smarty->assign('version', _PS_VERSION_);
            $this->context->smarty->assign('id_guest', $id_guest);
            $this->context->smarty->assign('id_customer', $id_customer);
            $this->context->smarty->assign('id_lang', $this->context->cookie->id_lang);
            $this->context->smarty->assign(
                'ajax_token',
                Configuration::get('B2BREGISTRATION_TOKEN')
            );
            $this->context->smarty->assign(
                'summary_fields_values',
                $registration_fields_options
            );
            $this->context->smarty->assign('summary_fields', $registration_fields);
            $this->context->smarty->assign('value_reg_fields', $fields);
            $this->context->smarty->assign(
                'is_psgdpr',
                Module::isInstalled('psgdpr') && Module::isEnabled('psgdpr')
            );
            $this->context->smarty->assign(
                'REGISTRATION_FIELDS_HEADING',
                Configuration::get(
                    'REGISTRATION_FIELDS_HEADING',
                    $this->context->language->id,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            );
            $this->context->smarty->assign(
                'actionLink',
                $this->context->link->getModuleLink(
                    $this->name,
                    'ajax',
                    ['action' => 'download', 'me' => base64_encode($id_customer)],
                    true
                )
            );
            $ps_17 = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
            $this->context->smarty->assign('ps_17', (int) $ps_17);
            if ($ps_17 > 0) {
                $this->context->smarty->assign(
                    'base_dir',
                    _PS_BASE_URL_ . __PS_BASE_URI__
                );
            }

            $this->context->smarty->assign('id_module', $this->id);

            return $this->display(__FILE__, 'summary.tpl');
        }
    }

    public function validateB2bFields($fields)
    {
        if (isset($fields) && $fields) {
            $objModel = new BToBCustomFields();
            $result = $objModel->fieldValidate($fields);
            if (isset($result) && count($result) > 0) {
                foreach ($result as $item) {
                    $this->context->controller->errors[] = $item;
                }
            }
            if (count($this->context->controller->errors)) {
                return false;
            }
        }

        return true;
    }

    public function hookActionBBAccountAdd($fields, $id_customer)
    {
        $id_customer = ($id_customer) ? $id_customer : null;
        if ($id_customer && isset($fields) && $fields) {
            $objModel = new BToBCustomFields();

            if (!count($this->context->controller->errors)) {
                $id_guest = BToBCustomFields::getGuestId($id_customer);
                if ($id_guest) {
                    $objModel->updateGuestFields($id_guest, $id_customer);
                }
                $result = $objModel->saveFieldValues($fields, $id_customer);
                if (isset($result) && $result) {
                    if ($result['result'] == false && isset($result['errors'])) {
                        foreach ($result['errors'] as $error) {
                            $this->context->controller->errors[] = $error;
                        }
                        $this->context->controller->errors;
                    } elseif ($result['result'] == true) {
                        return true;
                    }
                }
            }
        }
    }

    public function hookDisplayTop()
    {
        if (Configuration::get('B2BREGISTRATION_TOPLINK_POSITION', 'header') == 'nav') {
            $id_lang = $this->context->language->id;
            $enable_module = (int) Configuration::get(
                'B2BREGISTRATION_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $enable_top_link = (int) Configuration::get(
                'B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $top_link_text = pSQL(Configuration::get(
                'B2BREGISTRATION_URL_TEXT',
                (int) $id_lang,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            ));
            $b2b_links = [];
            $b2b_links = BBProfile::getTopLinks();
            if (!isset($this->context->customer->id)) {
                if (!empty($top_link_text) && $enable_module == 1 && $enable_top_link == 1) {
                    $key = Configuration::get(
                        'B2BREGISTRATION_URL_KEY',
                        null,
                        $this->context->shop->id_shop_group,
                        $this->context->shop->id
                    );
                    if (empty($key)) {
                        $slug = 'b2b-customer-create';
                    }
                    $page_link = $this->context->link->getModuleLink(
                        'b2bregistration',
                        'business',
                        ['profile_key' => $slug]
                    );
                    $b2b_links[] = [
                        'top_link_text' => $top_link_text,
                        'page_link' => $page_link,
                    ];
                }
                if (isset($b2b_links) && $b2b_links) {
                    $background_color = Configuration::get('B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR') ? Configuration::get('B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR') : '';
                    $text_color = Configuration::get('B2BREGISTRATION_TOPLINK_TEXT_COLOR') ? Configuration::get('B2BREGISTRATION_TOPLINK_TEXT_COLOR') : '';
                    $this->context->smarty->assign([
                        'b2b_links' => $b2b_links,
                        'text_color' => $text_color,
                        'background_color' => $background_color,
                        'mobile_view' => false,
                    ]);
                    if (Configuration::get('B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE')) {
                    }

                    return $this->display(__FILE__, 'display_nav2.tpl');
                }
            }
        }
    }

    public function hookDisplayNav2()
    {
        if (Configuration::get('B2BREGISTRATION_TOPLINK_POSITION', 'header') == 'header') {
            $id_lang = $this->context->language->id;
            $enable_module = (int) Configuration::get(
                'B2BREGISTRATION_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $enable_top_link = (int) Configuration::get(
                'B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $top_link_text = pSQL(Configuration::get(
                'B2BREGISTRATION_URL_TEXT',
                (int) $id_lang,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            ));

            $b2b_links = [];
            $b2b_links = BBProfile::getTopLinks();
            if (!isset($this->context->customer->id)) {
                if ($enable_module == 1 && !empty($top_link_text) && $enable_top_link == 1) {
                    $key = Configuration::get(
                        'B2BREGISTRATION_URL_KEY',
                        null,
                        $this->context->shop->id_shop_group,
                        $this->context->shop->id
                    );

                    if (empty($key)) {
                        $slug = 'b2b-customer-create';
                    }
                    $page_link = $this->context->link->getModuleLink(
                        'b2bregistration',
                        'business',
                        ['profile_key' => $slug]
                    );
                    $b2b_links[] = [
                        'top_link_text' => $top_link_text,
                        'page_link' => $page_link,
                    ];
                }

                if (isset($b2b_links) && $b2b_links) {
                    $background_color = Configuration::get('B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR') ? Configuration::get('B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR') : '';
                    $text_color = Configuration::get('B2BREGISTRATION_TOPLINK_TEXT_COLOR') ? Configuration::get('B2BREGISTRATION_TOPLINK_TEXT_COLOR') : '';
                    $this->context->smarty->assign([
                        'b2b_links' => $b2b_links,
                        'text_color' => $text_color,
                        'background_color' => $background_color,
                        'mobile_view' => false,
                    ]);

                    return $this->display(__FILE__, 'display_nav2.tpl');
                }
            }
        }
    }

    public function hookDisplayBanner()
    {
        if (Configuration::get('B2BREGISTRATION_TOPLINK_POSITION', 'header') == 'header') {
            $id_lang = $this->context->language->id;
            $enable_module = (int) Configuration::get(
                'B2BREGISTRATION_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $enable_top_link = (int) Configuration::get(
                'B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $top_link_text = pSQL(Configuration::get(
                'B2BREGISTRATION_URL_TEXT',
                (int) $id_lang,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            ));
            $b2b_links = [];
            $b2b_links = BBProfile::getTopLinks();
            if (!isset($this->context->customer->id)) {
                if ($enable_module == 1 && !empty($top_link_text) && $enable_top_link == 1) {
                    $key = Configuration::get(
                        'B2BREGISTRATION_URL_KEY',
                        null,
                        $this->context->shop->id_shop_group,
                        $this->context->shop->id
                    );
                    if (empty($key)) {
                        $slug = 'b2b-customer-create';
                    }
                    $page_link = $this->context->link->getModuleLink(
                        'b2bregistration',
                        'business',
                        ['profile_key' => $slug]
                    );
                    $b2b_links[] = [
                        'top_link_text' => $top_link_text,
                        'page_link' => $page_link,
                    ];
                }
                if (isset($b2b_links) && $b2b_links) {
                    $background_color = Configuration::get('B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR') ? Configuration::get('B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR') : '';
                    $text_color = Configuration::get('B2BREGISTRATION_TOPLINK_TEXT_COLOR') ? Configuration::get('B2BREGISTRATION_TOPLINK_TEXT_COLOR') : '';
                    $this->context->smarty->assign([
                        'b2b_links' => $b2b_links,
                        'text_color' => $text_color,
                        'background_color' => $background_color,
                        'mobile_view' => true,
                    ]);

                    return $this->display(__FILE__, 'display_nav2.tpl');
                }
            }
        }
    }

    public function hookDisplayNav()
    {
        $id_lang = $this->context->language->id;
        $enable_module = (int) Configuration::get(
            'B2BREGISTRATION_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $enable_top_link = (int) Configuration::get(
            'B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $top_link_text = pSQL(Configuration::get(
            'B2BREGISTRATION_URL_TEXT',
            (int) $id_lang,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $b2b_links = [];
        $b2b_links = BBProfile::getTopLinks();

        if (!isset($this->context->customer->id)) {
            if (!empty($top_link_text) && $enable_module == 1 && $enable_top_link == 1) {
                $key = Configuration::get(
                    'B2BREGISTRATION_URL_KEY',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                );
                if (empty($key)) {
                    $slug = 'b2b-customer-create';
                }
                $page_link = $this->context->link->getModuleLink(
                    'b2bregistration',
                    'business',
                    ['profile_key' => $slug]
                );
                $b2b_links[] = [
                    'top_link_text' => $top_link_text,
                    'page_link' => $page_link,
                ];
            }
            if (isset($b2b_links) && $b2b_links) {
                $background_color = Configuration::get('B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR') ? Configuration::get('B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR') : '';
                $text_color = Configuration::get('B2BREGISTRATION_TOPLINK_TEXT_COLOR') ? Configuration::get('B2BREGISTRATION_TOPLINK_TEXT_COLOR') : '';
                $this->context->smarty->assign([
                    'b2b_links' => $b2b_links,
                    'text_color' => $text_color,
                    'background_color' => $background_color,
                ]);

                return $this->display(__FILE__, 'display_nav.tpl');
            }
        }
    }

    protected function createB2BGroup()
    {
        $b2b_group = new Group();
        $b2b_group->reduction = 0;
        $b2b_group->price_display_method = 1;
        $b2b_group->show_prices = 1;
        $b2b_group->date_add = date('Y-m-d H:i:s');

        foreach (Language::getLanguages() as $lang) {
            $b2b_group->name[$lang['id_lang']] = $this->l('B2B');
        }

        if ($b2b_group->add()) {
            $shops = Shop::getShops(true, null, true);
            foreach ($shops as $shop_id) {
                // Check if the association already exists
                $exists = Db::getInstance()->getValue('
                SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'group_shop 
                WHERE id_group = ' . (int) $b2b_group->id . ' AND id_shop = ' . (int) $shop_id
                );

                if (!$exists) {
                    // Link the group to the shop if it doesn't already exist
                    $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'group_shop (id_group, id_shop) 
                        VALUES (' . (int) $b2b_group->id . ', ' . (int) $shop_id . ')';
                    if (!Db::getInstance()->execute($sql)) {
                        // Handle potential errors in SQL execution
                        return false;
                    }
                }

                // Update configuration for each shop
                if (!Configuration::updateValue(
                    'B2BREGISTRATION_GROUPS',
                    $b2b_group->id,
                    false,
                    Shop::getGroupFromShop($shop_id),
                    $shop_id
                )) {
                    return false;
                }

                $modules = Module::getModulesInstalled();
                $module_permissions = [];
                foreach ($modules as $val) {
                    $module_permissions[] = $val['id_module'];
                }

                // Add module restrictions for the group
                if (!Group::addModulesRestrictions(
                    (int) Configuration::get(
                        'B2BREGISTRATION_GROUPS',
                        null,
                        Shop::getGroupFromShop($shop_id),
                        $shop_id
                    ),
                    $module_permissions,
                    [$shop_id]
                )) {
                    return false;
                }

                // Add the B2B group to all categories for the current shop
                $categories = BusinessAccountModel::getAllCategories();
                foreach ($categories as $id_category) {
                    if (!BusinessAccountModel::addB2BGroupToCategory(
                        $id_category,
                        (int) Configuration::get(
                            'B2BREGISTRATION_GROUPS',
                            null,
                            Shop::getGroupFromShop($shop_id),
                            $shop_id
                        )
                    )) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }

    public function deleteB2BGroup()
    {
        $b2b_group = new Group((int) Configuration::get(
            'B2BREGISTRATION_GROUPS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $default_lang_id = (int) Configuration::get('PS_LANG_DEFAULT');
        if ($b2b_group->name[$default_lang_id] == 'B2B' && isset($b2b_group->name[$default_lang_id])) {
            if ($b2b_group->delete()) {
                Configuration::deleteByName('B2BREGISTRATION_GROUPS');

                return true;
            }
        }

        return false;
    }

    public function hookModuleRoutes()
    {
        $url_link = Configuration::get(
            'B2BREGISTRATION_URL_KEY',
            (int) $this->context->language->id,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        if (empty($url_link)) {
            $url_link = 'b2bregistration';
        }

        return [
            'module-' . $this->name . '-business' => [
                'controller' => 'business',
                'rule' => $url_link,
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id'],
                    'rewrite' => ['regexp' => '[_a-zA-Z0-9\pL\pS-]*', 'param' => 'rewrite'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => $this->name,
                ],
            ],
        ];
    }

    /**
     * GDPR Compliance Hooks.
     */
    public function hookActionDeleteGDPRCustomer($customer)
    {
        if (!empty($customer['email']) && Validate::isEmail($customer['email'])) {
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'customer WHERE id_customer = ' . (int) $customer['id'];
            $sql &= 'DELETE FROM ' . _DB_PREFIX_ . 'b2bregistration WHERE id_customer = ' . (int) $customer['id'];
            $sql &= 'DELETE FROM ' . _DB_PREFIX_ . 'address WHERE id_customer = ' . (int) $customer['id'];
            if (Db::getInstance()->execute($sql)) {
                return json_encode(true);
            }

            return json_encode($this->l('B2B Registration: Unable to delete customer using customer id.'));
        }
    }

    public function hookActionExportGDPRData($customer)
    {
        if (!Tools::isEmpty($customer['email']) && Validate::isEmail($customer['email'])) {
            $res = BusinessAccountModel::getB2BCustomers($this->context->language->id, $customer['id']);
            $result = [];
            foreach ($res as $key => $res1) {
                $result[$key][$this->l('ID')] = $customer['id'];
                $result[$key][$this->l('First Name')] = $customer['firstname'];
                $result[$key][$this->l('Middle Name')] = $res1['middle_name'];
                $result[$key][$this->l('Last Name')] = $customer['lastname'];
                $result[$key][$this->l('Email')] = $customer['email'];
                $result[$key][$this->l('Siret')] = $customer['siret'];
                $result[$key][$this->l('Company')] = $customer['website'];
                $result[$key][$this->l('Address')] = $res1['address1'];
                $result[$key][$this->l('City')] = $res1['city'];
            }
            if ($result) {
                return json_encode($result);
            }

            return json_encode($this->l('B2B Registration: Unable to export customer using email.'));
        }
    }

    public function hookDisplayCustomerAccount()
    {
        $enable_module = (int) Configuration::get(
            'B2BREGISTRATION_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $id_customer = (int) $this->context->cookie->id_customer;
        $b2b = BusinessAccountModel::getRegisteredB2B($id_customer);
        if (isset($b2b['id_b2b_profile']) && $b2b['id_b2b_profile'] != 0) {
            $enable_module = BBProfile::getProfileStatus($b2b['id_b2b_profile']);
        }

        if ($enable_module) {
            if (empty($b2b)) {
                if (Tools::version_compare(_PS_VERSION_, '1.7', '>=') == true) {
                    return $this->display(__FILE__, 'hook_customer_account_17.tpl');
                } else {
                    return $this->display(__FILE__, 'hook_customer_account_16.tpl');
                }
            } else {
                $links = $this->context->link->getModuleLink(
                    'b2bregistration',
                    'b2b',
                    ['id_b2b' => $b2b['id_b2bregistration']],
                    true
                );
                $this->context->smarty->assign('links', $links);
                if (Tools::version_compare(_PS_VERSION_, '1.7', '>=') == true) {
                    return $this->display(__FILE__, 'hook_b2b_customer.tpl');
                } else {
                    return $this->display(__FILE__, 'hook_b2b_customer_16.tpl');
                }
            }
        }
    }

    public function hookActionObjectCustomerUpdateAfter($object)
    {
        $id_customer = (int) $object['object']->id;
        $customer = new Customer($id_customer);
        if (!empty($id_customer)) {
            $obj = BusinessAccountModel::getBusinessStatus($id_customer);
            $objs = new BusinessAccountModel($obj['id_b2bregistration']);
            if ($obj) {
                $objs->active = $customer->active;
                $res = $objs->update();
                if ($res == true) {
                    $subject = Mail::l('B2B Registration Approvel');
                    $templateVars = [
                        '{first_name}' => $customer->firstname,
                        '{last_name}' => $customer->lastname,
                        '{email}' => $customer->email,
                    ];
                    if ($customer->active == 1) {
                        $template_name = 'b2b_activated';
                    } else {
                        $template_name = 'b2b_customer_pending';
                    }
                    $title = $subject;
                    $from = Configuration::get('PS_SHOP_EMAIL');
                    $email_sender = Configuration::get('PS_SHOP_NAME');
                    $fromName = $email_sender;
                    $mailDir = _PS_MODULE_DIR_ . 'b2bregistration/mails/';
                    $toName = $customer->firstname;
                    Mail::Send(
                        Context::getContext()->language->id,
                        $template_name,
                        $title,
                        $templateVars,
                        $customer->email,
                        $toName,
                        $from,
                        $fromName,
                        null,
                        null,
                        $mailDir
                    );
                }
            }
        }
    }

    public function hookActionObjectCustomerDeleteAfter($object)
    {
        if ($object) {
            $id_customer = (int) Tools::getValue('id_customer');
            if ($id_customer) {
                BusinessAccountModel::extraFieldsDeletion($id_customer);
                $exist_user_fields = BusinessAccountModel::tableExists('b2b_fields_data');
                if ($exist_user_fields) {
                    BToBFieldsData::customFieldsDeletion($id_customer);
                }
            }
        }
    }

    /**
     * empty listener for registerGDPRConsent hook.
     */
    public function hookRegisterGDPRConsent()
    {
        /* registerGDPRConsent is a special kind of hook that doesn't need a listener, see :
    https://build.prestashop.com/howtos/module/how-to-make-your-module-compliant-with-prestashop-official-gdpr-compliance-module/
    However since Prestashop 1.7.8, modules must implement a listener for all the hooks they register: a check is made
    at module installation.
     */
    }

    public function upgradeB2BRegistrationModule()
    {
        include dirname(__FILE__) . '/sql/install.php';
        $this->registerHook('displayTop');
        Configuration::updateValue('B2BREGISTRATION_TOPLINK_BACKGROUND_COLOR', '#25b9d7');
        Configuration::updateValue('B2BREGISTRATION_TOPLINK_TEXT_COLOR', '#fff');
        Configuration::updateValue('B2BREGISTRATION_TOPLINK_POSITION', 'header');
        Configuration::updateValue('B2BREGISTRATION_TOKEN', Tools::substr(Tools::encrypt($this->name . '-' . date('c')), 0, 32));
        $exist_fields = BusinessAccountModel::tableExists('b2b_custom_fields');
        if ($exist_fields == true) {
            $data = BusinessAccountModel::getOldCustomFields('b2b_custom_fields');
            BusinessAccountModel::insertOldDataToNewTables($data);
        }
        $exist_lang_fields = BusinessAccountModel::tableExists('b2b_custom_fields_lang');
        if ($exist_lang_fields == true) {
            $data = BusinessAccountModel::getOldCustomFields('b2b_custom_fields_lang');
            BusinessAccountModel::insertOldLangDataToNewTables($data);
        }
        $exist_user_fields = BusinessAccountModel::tableExists('b2b_fields_data');
        if ($exist_user_fields == true) {
            $data = BusinessAccountModel::getOldCustomFields('b2b_fields_data');
            BusinessAccountModel::insertOldUserDataToNewTables($data);
        }

        return true;
    }

    public function upgradeB2BRegistration()
    {
        $db_data = BusinessAccountModel::upgradeB2BModule();
        $subtab3 = new Tab();
        $subtab3->class_name = 'AdminB2BProfiles';
        $subtab3->id_parent = Tab::getIdFromClassName($this->tab_class);
        $subtab3->module = $this->tab_module;
        $subtab3->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('B2B Profiles');

        return $subtab3->add() . $db_data;
    }

    public function addDefaultValues()
    {
        return Configuration::updateValue(
            'B2BREGISTRATION_ENABLE_DISABLE',
            true,
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE',
            false,
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE',
            false,
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_NAME_SUFFIX_OPTIONS',
            'MD,PHD',
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE',
            false,
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE',
            false,
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS',
            true,
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE',
            true,
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE',
            true,
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE',
            false,
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_DOB_ENABLE_DISABLE',
            false,
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_ADDRESS_ENABLE_DISABLE',
            true,
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE',
            true,
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_WEBSITE_ENABLE_DISABLE',
            true,
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        // MultiLang Fields
         && Configuration::updateValue(
             'B2BREGISTRATION_URL_KEY',
             [$this->context->language->id => 'b2b-customer-create'],
             false,
             $this->id_shop_group,
             $this->id_shop
         )
        && Configuration::updateValue(
            'B2BREGISTRATION_URL_TEXT',
            [$this->context->language->id => 'Create Business Account'],
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_PERSONAL_TEXT',
            [$this->context->language->id => 'Personal Information'],
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_COMPANY_TEXT',
            [$this->context->language->id => 'Company Information'],
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_SIGNIN_TEXT',
            [$this->context->language->id => 'Sign in Information'],
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_CUSTOM_FIELD_TEXT',
            [$this->context->language->id => 'Custom Fields'],
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_CUSTOM_TEXT',
            [$this->context->language->id => 'Custom Page'],
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_ERROR_MSG_TEXT',
            [$this->context->language->id => 'Your account is pending for validation and will be activated soon'],
            false,
            $this->id_shop_group,
            $this->id_shop
        )
        && Configuration::updateValue(
            'B2BREGISTRATION_ADDRESS_TEXT',
            [$this->context->language->id => 'Address Information'],
            false,
            $this->id_shop_group,
            $this->id_shop
        );
    }
}

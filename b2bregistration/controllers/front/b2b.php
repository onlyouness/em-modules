<?php
/**
 * 2007-2022 PrestaShop.
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2022 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class B2BRegistrationB2BModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme = false);
        $controller = Dispatcher::getInstance()->getController();
        if ($controller == 'b2b') {
            $this->addjQueryPlugin([
                'fancybox',
            ]);

            $this->addjQueryPlugin(['date']);
            $this->addJqueryUI([
                'ui.slider',
                'ui.datepicker',
            ]);
            $this->addCSS($this->module->getPathUri() . 'views/css/bb_registrationfields.css', 'all');
            $this->addJS($this->module->getPathUri() . 'views/js/bb_registrationfields.js');
            Media::addJsDef(['is_required_label' => sprintf(' %s', $this->module->translations['is_required'])]);

            if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                $this->registerStylesheet(
                    'jquery-ui-timepicker-addon',
                    '/js/jquery/plugins/timepicker/jquery-ui-timepicker-addon.css',
                    ['media' => 'all', 'priority' => 500]
                );
                $this->registerJavascript(
                    'jquery-ui-timepicker-addon',
                    '/js/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js',
                    ['position' => 'top', 'priority' => 500]
                );
                $this->registerJavascript(
                    'validate_registration_fields',
                    'modules/' . $this->module->name . '/views/js/validate_registration_fields.js',
                    ['position' => 'bottom', 'priority' => 501]
                );
            } else {
                $this->addCSS([_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css']);
                $this->addJS([
                    _PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js',
                    $this->module->getPathUri() . 'views/js/validate_registration_fields.js',
                ]);
            }
        }
    }

    public function initContent()
    {
        parent::initContent();
        $id_customer = (int) $this->context->customer->id;
        $id_profile = ($id_customer) ? BBProfile::getIdProfileByField(
            'b2b_profile_group',
            $this->context->customer->id_default_group
        ) : 0;
        $enable_captcha = (int) Configuration::get(
            'B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $site_key = pSQL(Configuration::get(
            'B2BREGISTRATION_SITE_KEY',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $personal_heading = '';
        if (Validate::isLoadedObject($profile = new BBProfile(
            $id_profile,
            $this->context->language->id
        )) && $profile->active) {
            $id_profile = (int) $profile->id;
            $enable_module = $profile->active;
            $enable_suffix = $profile->b2b_name_suffix_active;
            $enable_custom = $profile->b2b_custom_fields;
            $middle_name = $profile->b2b_middle_name_active;
            $cms_page = $profile->b2b_tos_page;
            $enable_address = $profile->b2b_address;
            $enable_website = $profile->b2b_website;
            $enable_identification_number = $profile->b2b_profile_siret;
            $name_suffix = !empty($profile->b2b_name_suffix) ? explode(
                ',',
                $profile->b2b_name_suffix
            ) : [];
            $enable_group_selection = $profile->b2b_customer_enable_group;
            $selected_groups = !empty($profile->groupBox) ? explode(
                ',',
                $profile->groupBox
            ) : [];
        } else {
            $enable_module = (int) Configuration::get(
                'B2BREGISTRATION_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $enable_suffix = (int) Configuration::get(
                'B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $enable_website = (int) Configuration::get(
                'B2BREGISTRATION_WEBSITE_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $enable_identification_number = (int) Configuration::get(
                'B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );

            $enable_custom = (int) Configuration::get(
                'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $name_suffix = explode(',', Configuration::get(
                'B2BREGISTRATION_NAME_SUFFIX_OPTIONS',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            ));
            $middle_name = (int) Configuration::get(
                'B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );

            $cms_page = (int) Configuration::get(
                'B2BREGISTRATION_CMS_PAGES',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );

            $personal_heading = pSQL(Configuration::get(
                'B2BREGISTRATION_PERSONAL_TEXT',
                $this->context->language->id,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            ));
            $enable_address = (int) Configuration::get(
                'B2BREGISTRATION_ADDRESS_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $enable_group_selection = (int) Configuration::get(
                'B2BREGISTRATION_GROUP_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $selected_groups = explode(',', pSQL(Configuration::get(
                'B2BREGISTRATION_GROUP_SELECTION',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            )));
        }
        $cms_page_rule = (int) Configuration::get(
            'B2BREGISTRATION_CMS_PAGES_RULE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $cms_page_link = '';
        if ($cms_page) {
            $cms_page_link = new CMS($cms_page, $this->context->cookie->id_lang);
        }
        $cms_page_link_rule = '';
        if ($cms_page_rule) {
            $cms_page_link_rule = new CMS($cms_page_rule, $this->context->cookie->id_lang);
        }
        $address = BusinessAccountModel::getAddress($id_customer);
        if (!empty($address)) {
            $this->context->smarty->assign('address', $address);
        }
        $all_groups = Group::getGroups($this->context->language->id);
        $custom_fields = (int) BToBCustomFields::profileHasFields($id_profile);
        $this->context->smarty->assign('hook_create_account_form', $this->module->hookDisplayBBFields(['id_profile' => $id_profile]));
        $url_link = $this->context->link->getModuleLink('b2bregistration', 'b2b');
        $this->context->smarty->assign([
            'enable_suffix' => $enable_suffix,
            'name_suffix' => $name_suffix,
            'id_profile' => $id_profile,
            'enable_group_selection' => $enable_group_selection,
            'selected_groups' => $selected_groups,
            'all_groups' => $all_groups,
            'enable_website' => $enable_website,
            'enable_captcha' => $enable_captcha,
            'enable_address' => $enable_address,
            'enable_custom' => $enable_custom,
            'cms' => $cms_page_link,
            'cms_rule' => $cms_page_link_rule,
            'custom_fields' => $custom_fields,
            'site_key' => $site_key,
            'personal_heading' => $personal_heading,
            'enable_identification_number' => $enable_identification_number,
            'middle_name' => $middle_name,
            'url_link' => $url_link,
            'email' => $this->context->cookie->email,
            'firstname' => $this->context->cookie->customer_firstname,
            'lastname' => $this->context->cookie->customer_lastname,
            'id_module' => $this->module->id,
            'default_group' => Context::getContext()->customer->id_default_group,
        ]);
        $b2b = BusinessAccountModel::getRegisteredB2B($id_customer);
        $id_b2b = (int) Tools::getValue('id_b2b', (isset($b2b['id_b2bregistration'])
            && $b2b['id_b2bregistration']) ? $b2b['id_b2bregistration'] : 0);
        if ($this->context->customer->logged && $enable_module) {
            if (!empty($id_b2b) && $id_b2b) {
                $current_lang = (int) $this->context->language->id;
                $b2b_data = BusinessAccountModel::getB2BCustomer($current_lang, $id_customer);
                $this->context->smarty->assign([
                    'b2b_data' => $b2b_data,
                ]);
                if (true === Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                    $this->setTemplate('module:b2bregistration/views/templates/front/b2b_account_info.tpl');
                } else {
                    $this->setTemplate('b2b_account_info_16.tpl');
                }
            } else {
                if (true === Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                    $this->setTemplate('module:b2bregistration/views/templates/front/b2b_account.tpl');
                } else {
                    $this->setTemplate('b2b_account_16.tpl');
                }
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function init()
    {
        parent::init();
        $result = false;
        $enable_identification = (int) Configuration::get(
            'B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $enable_wesbsite = (int) Configuration::get(
            'B2BREGISTRATION_WEBSITE_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $default_group = (int) Configuration::get(
            'B2BREGISTRATION_GROUPS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $enable_address = (int) Configuration::get(
            'B2BREGISTRATION_ADDRESS_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $enable_email_customer = (int) Configuration::get(
            'B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $enable_email_admin = (int) Configuration::get(
            'B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $enable_custom = (int) Configuration::get(
            'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $email_sender = pSQL(Configuration::get(
            'B2BREGISTRATION_ADMIN_EMAIL_SENDER',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $auto_approvel = (int) Configuration::get(
            'B2BREGISTRATION_AUTO_APPROVEL',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $enable_group_selection = (int) Configuration::get(
            'B2BREGISTRATION_GROUP_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );

        if (Tools::getValue('inprocess') == 1) {
            $html_content = Tools::getValue(
                'B2BREGISTRATION_CUSTOM_TEXT' . $this->context->language->id,
                Configuration::get(
                    'B2BREGISTRATION_CUSTOM_TEXT',
                    (int) $this->context->language->id,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            );
            $this->context->smarty->assign([
                'html_content' => $html_content,
            ]);

            return $this->setTemplate('module:b2bregistration/views/templates/front/inprocess.tpl');
        }
        if (Tools::isSubmit('b2b_data')) {
            $id_profile = (int) Tools::getValue('id_profile');
            $id_customer = (int) $this->context->cookie->id_customer;
            $name_suffix = pSQL(Tools::getValue('name_suffix'));
            $first_name = pSQL(Tools::getValue('first_name'));
            $middle_name = pSQL(Tools::getValue('middle_name'));
            $last_name = pSQL(Tools::getValue('last_name'));
            $website = pSQL(Tools::getValue('website'));
            $email = pSQL(Tools::getValue('email'));
            $company_name = pSQL(Tools::getValue('company_name'));
            $identification_number = pSQL(Tools::getValue('identification_number'));
            $customer = new Customer($id_customer);
            $id_b2b = (int) Tools::getValue('id_b2b');
            if ($id_profile && Validate::isLoadedObject(
                $b2b_profile = new BBProfile($id_profile, $this->context->language->id)
            )) {
                $default_group = $b2b_profile->b2b_profile_group;
                $enable_wesbsite = $b2b_profile->b2b_website;
                $auto_approvel = $b2b_profile->b2b_customer_auto_approval;
                $enable_custom = $b2b_profile->b2b_custom_fields;
                $enable_group_selection = $b2b_profile->b2b_customer_enable_group;
                $enable_identification = $b2b_profile->b2b_profile_siret;
            }
            if ($enable_group_selection) {
                $default_group = (int) Tools::getValue('customer_group');
            }
            $b2b = new BusinessAccountModel();
            if (!empty($id_b2b) && $id_b2b) {
                $b2b_upadte = new BusinessAccountModel($id_b2b);
            }
            if ($auto_approvel == 1) {
                $active = 1;
            } else {
                $active = 0;
            }
            if (empty($first_name)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'first_name_required'
                ];
            }

            if (!Validate::isName($first_name)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'first_name_valid'
                ];
            }

            if (empty($last_name)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'last_name_required'
                ];
            }

            if (!Validate::isName($last_name)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'last_name_valid'
                ];
            }

            if ($enable_wesbsite && empty($website)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'website_required'
                ];
            }

            if ($enable_address) {
                if (empty($company_name)) {
                    $this->context->controller->errors[] = $this->module->translations[
                        'company_required'
                    ];
                } elseif ($enable_identification) {
                    if (empty($identification_number)) {
                        $this->context->controller->errors[] = $this->module->translations[
                            'siret_required'
                        ];
                    } elseif (!Validate::isSiret($identification_number) && true === (bool) Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                        $this->context->controller->errors[] = $this->module->translations[
                            'siret_valid'
                        ];
                    }
                }
            }

            if ($enable_custom && ($customFields = Tools::getValue('fields'))) {
                $this->module->validateB2bFields($customFields);
            }

            if (!count($this->context->controller->errors)) {
                $customer->id_default_group = $default_group;
                $customer->firstname = $first_name;
                $customer->lastname = $last_name;
                $customer->website = $website;
                $customer->company = $company_name;
                $customer->email = $email;
                $customer->siret = $identification_number;
                $customer->active = $active;
                $res = $customer->update();
                Hook::exec('actionCustomerAccountUpdate', [
                    'customer' => $customer,
                ]);
    
                $result = true;
                if ($res == true) {
                    if ($id_b2b) {
                        $b2b_upadte->id_customer = $customer->id;
                        $b2b_upadte->middle_name = $middle_name;
                        $b2b_upadte->name_suffix = $name_suffix;
                        $b2b_upadte->flag = 1;
                        $b2b_upadte->active = $active;
                        $b2b_upadte->update();
                        $result = true;
                    } else {
                        $b2b->id_customer = $customer->id;
                        $b2b->middle_name = $middle_name;
                        $b2b->name_suffix = $name_suffix;
                        $b2b->flag = 1;
                        $b2b->active = $active;
                        $b2b->save();
                        $result = true;
                    }
                }
                if ($res && $enable_custom && isset($customFields)) {
                    $this->module->hookActionBBAccountAdd($customFields, $customer->id);
                }
                if ($enable_email_customer == 1 && $result == true && !$id_b2b) {
                    $subject = Mail::l('Customer Registration');
                    $templateVars = [
                        '{first_name}' => $first_name,
                        '{last_name}' => $last_name,
                        '{company_name}' => $company_name,
                        '{website}' => $website,
                        '{email}' => $email,
                    ];
                    if ($auto_approvel == 1) {
                        $template_name = 'b2b_customer_registration';
                    } else {
                        $template_name = 'b2b_customer_pending';
                    }
                    $title = $subject;
                    $from = Configuration::get('PS_SHOP_EMAIL');
                    if ($email_sender == '') {
                        $email_sender = Configuration::get('PS_SHOP_NAME');
                    }
                    $fromName = $email_sender;
                    $mailDir = _PS_MODULE_DIR_ . 'b2bregistration/mails/';
                    $toName = $first_name;
                    $send = Mail::Send(
                        Context::getContext()->language->id,
                        $template_name,
                        $title,
                        $templateVars,
                        $email,
                        $toName,
                        $from,
                        $fromName,
                        null,
                        null,
                        $mailDir
                    );
                    if ($send) {
                        $result = true;
                    }
                }

                if ($enable_email_admin == 1 && $result == true) {
                    $admin_email = pSQL(Configuration::get(
                        'B2BREGISTRATION_ADMIN_EMAIL_ID',
                        false,
                        $this->context->shop->id_shop_group,
                        $this->context->shop->id
                    ));
                    $subject = Mail::l('New Customer Registration');
                    $templateVars = [
                        '{first_name}' => $first_name,
                        '{last_name}' => $last_name,
                        '{company_name}' => $company_name,
                        '{website}' => $website,
                        '{email}' => $email,
                    ];
                    if ($id_b2b) {
                        $template_name = 'customer_updation_admin_notify';
                    } else {
                        $template_name = 'customer_registration_admin_notify';
                    }

                    $title = $subject;
                    $from = Configuration::get('PS_SHOP_EMAIL');
                    if ($email_sender == '') {
                        $email_sender = Configuration::get('PS_SHOP_NAME');
                    }
                    $fromName = $email_sender;
                    $mailDir = _PS_MODULE_DIR_ . 'b2bregistration/mails/';
                    $toName = 'Admin';
                    Mail::Send(
                        Context::getContext()->language->id,
                        $template_name,
                        $title,
                        $templateVars,
                        $admin_email,
                        $toName,
                        $from,
                        $fromName,
                        null,
                        null,
                        $mailDir
                    );
                    $result = true;
                }
                if (!empty($id_b2b) && $id_b2b) {
                    if (_PS_VERSION_ >= '1.7') {
                        if ($active == 1) {
                            $this->context->controller->success[] = $this->module->translations[
                                'update_account'
                            ];
                        } else {
                            $this->context->controller->errors[] = $this->module->translations[
                                'validate_account'
                            ];
                        }
                    } else {
                        if ($active == 1) {
                            Tools::redirect('index.php?controller=authentication?back=my-account');
                        } else {
                            $this->context->controller->errors[] = $this->module->translations[
                                'validate_account'
                            ];
                        }
                    }
                } else {
                    if ($result == true && $customer->id && $auto_approvel == 1) {
                        $ps_version = _PS_VERSION_;
                        if ($ps_version >= '1.7') {
                            $this->context->updateCustomer($customer);
                            Tools::redirect('index.php?controller=authentication?back=my-account');
                        } else {
                            Tools::redirect('index.php?controller=authentication?back=my-account');
                        }
                    } else {
                        Tools::redirect($this->context->link->getModuleLink(
                            'b2bregistration',
                            'business',
                            ['inprocess' => 1]
                        ));
                    }
                }
            }
        }
    }
}

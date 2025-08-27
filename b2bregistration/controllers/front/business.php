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
class B2BRegistrationBusinessModuleFrontController extends ModuleFrontController
{
    protected $profile_key;

    public function __construct()
    {
        parent::__construct();
        $this->display_column_left = false;
        $this->display_column_right = false;
        $this->context = Context::getContext();
        $this->profile_key = Tools::safeOutput(Tools::getValue('profile_key'));
    }

    public function initContent()
    {
        parent::initContent();
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
            if (true === Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                return $this->setTemplate('module:b2bregistration/views/templates/front/inprocess.tpl');
            } else {
                return $this->setTemplate('inprocess16.tpl');
            }
        }
        if ($this->context->customer->logged) {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        } else {
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
            $required_country = (int) Configuration::get(
                'B2BREGISTRATION_COUNTRY_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $required_state = (int) Configuration::get(
                'B2BREGISTRATION_STATE_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );

            $all_groups = Group::getGroups($this->context->language->id);
            $id_profile = 0;
            $enable_module = (int) Configuration::get(
                'B2BREGISTRATION_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            if (!$enable_module && empty($this->profile_key)) {
                $b2b_links = BBProfile::getTopLinks();
                if ($b2b_links) {
                    foreach ($b2b_links as $link) {
                        if ($link['active'] == 1) {
                            $this->profile_key = $link['b2b_link_rewrite'];
                            break;
                        }
                    }
                }
            }

            if ((!empty($this->profile_key)
                && ($id_profile = BBProfile::getIdProfileByKey($this->profile_key))) || (int) Configuration::get(
                    'B2BREGISTRATION_ENABLE_DISABLE',
                    false,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                ) != 1) {
                if (Validate::isLoadedObject(
                    $profile = new BBProfile($id_profile, $this->context->language->id)
                ) && $profile->active) {
                    $id_profile = (int) $profile->id;
                    $enable_module = $profile->active;
                    $enable_prefix = $profile->b2b_name_prefix_active;
                    $enable_suffix = $profile->b2b_name_suffix_active;
                    $enable_custom = $profile->b2b_custom_fields;
                    $middle_name = $profile->b2b_middle_name_active;
                    $personal_heading = $profile->b2b_personal_info_heading;
                    $company_heading = $profile->b2b_company_info_heading;
                    $signin_heading = $profile->b2b_signin_heading;
                    $address_heading = $profile->b2b_address_heading;
                    $custom_heading = $profile->b2b_customfields_heading;
                    $cms_page = $profile->b2b_tos_page;
                    $enable_address = $profile->b2b_address;
                    $enable_website = $profile->b2b_website;
                    $enable_birthdate = $profile->b2b_profile_dob;
                    $enable_identification_number = $profile->b2b_profile_siret;
                    $name_suffix = !empty($profile->b2b_name_suffix) ? explode(
                        ',',
                        $profile->b2b_name_suffix
                    ) : [];
                    $name_prefix = !empty($profile->b2b_name_prefix) ? explode(
                        ',',
                        $profile->b2b_name_prefix
                    ) : [];
                    $enable_group_selection = $profile->b2b_customer_enable_group;
                    $selected_groups = !empty($profile->groupBox) ? explode(
                        ',',
                        $profile->groupBox
                    ) : [];
                }
            } else {
                // default profile

                $enable_prefix = (int) Configuration::get(
                    'B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE',
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
                $enable_address = (int) Configuration::get(
                    'B2BREGISTRATION_ADDRESS_ENABLE_DISABLE',
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
                $enable_custom = (int) Configuration::get(
                    'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS',
                    false,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                );
                $enable_birthdate = (int) Configuration::get(
                    'B2BREGISTRATION_DOB_ENABLE_DISABLE',
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

                $name_prefix = explode(',', Configuration::get(
                    'B2BREGISTRATION_NAME_PREFIX_OPTIONS',
                    false,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                ));
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
                $personal_heading = pSQL(Configuration::get(
                    'B2BREGISTRATION_PERSONAL_TEXT',
                    $this->context->language->id,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                ));

                $company_heading = pSQL(Configuration::get(
                    'B2BREGISTRATION_COMPANY_TEXT',
                    $this->context->language->id,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                ));
                $signin_heading = pSQL(Configuration::get(
                    'B2BREGISTRATION_SIGNIN_TEXT',
                    $this->context->language->id,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                ));
                $address_heading = pSQL(Configuration::get(
                    'B2BREGISTRATION_ADDRESS_TEXT',
                    $this->context->language->id,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                ));
                $custom_heading = pSQL(Configuration::get(
                    'B2BREGISTRATION_CUSTOM_FIELD_TEXT',
                    $this->context->language->id,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                ));
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
            $cms_page = (int) Configuration::get(
                'B2BREGISTRATION_CMS_PAGES',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $cms_page_rule = (int) Configuration::get(
                'B2BREGISTRATION_CMS_PAGES_RULE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $cms_page_link_rule = '';
            if ($cms_page_rule) {
                $cms_page_link_rule = new CMS($cms_page_rule, $this->context->cookie->id_lang);
            }
            $cms_page_link = '';
            if ($cms_page) {
                $cms_page_link = new CMS($cms_page, $this->context->cookie->id_lang);
            }
            $all_groups = Group::getGroups($this->context->language->id);
            $custom_fields = (int) BToBCustomFields::profileHasFields($id_profile);
            $this->context->smarty->assign('hook_create_account_form', $this->module->hookDisplayBBFields(['id_profile' => $id_profile]));
            $genders = BusinessAccountModel::getAllGenders($this->context->language->id);
            $url_link = $this->context->link->getModuleLink(
                'b2bregistration',
                'business',
                ['profile_key' => $this->profile_key]
            );
            $selected_country_id = (int) Tools::getValue('id_country');
            $selected_state_id = (int) Tools::getValue('id_state');
            $states = [];
            if ($selected_country_id) {
                $states = State::getStatesByIdCountry($selected_country_id);
            }
            $this->context->smarty->assign([
                'selected_country_id' => $selected_country_id,
                'selected_state_id' => $selected_state_id,
                'selected_states' => $states,
                'available_countries' => Country::getCountries($this->context->language->id, true),
                'required_country' => $required_country,
                'required_state' => $required_state,
                'cms_rule' => $cms_page_link_rule,
                'name_prefix' => $name_prefix,
                'enable_group_selection' => $enable_group_selection,
                'selected_groups' => $selected_groups,
                'all_groups' => $all_groups,
                'id_profile' => $id_profile,
                'enable_prefix' => $enable_prefix,
                'enable_suffix' => $enable_suffix,
                'enable_address' => $enable_address,
                'enable_website' => $enable_website,
                'enable_birthdate' => $enable_birthdate,
                'enable_captcha' => $enable_captcha,
                'cms' => $cms_page_link,
                'site_key' => $site_key,
                'enable_identification_number' => $enable_identification_number,
                'name_suffix' => $name_suffix,
                'middle_name' => $middle_name,
                'personal_heading' => $personal_heading,
                'company_heading' => $company_heading,
                'signin_heading' => $signin_heading,
                'address_heading' => $address_heading,
                'custom_heading' => $custom_heading,
                'enable_custom' => $enable_custom,
                'genders' => $genders,
                'url_link' => $url_link,
                'id_module' => $this->module->id,
                'custom_fields' => $custom_fields,
                'default_group' => Context::getContext()->customer->id_default_group,
            ]);
            if ($enable_module) {
                if (true === Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                    $this->setTemplate('module:b2bregistration/views/templates/front/business_account.tpl');
                } else {
                    $this->setTemplate('business_account_16.tpl');
                }
            } else {
                Tools::redirect('index.php?controller=authentication?back=my-account');
            }
        }
    }

    public function init()
    {
        parent::init();
        $result = false;
        if (Tools::isSubmit('b2b_add_data')) {
            $id_profile = (int) Tools::getValue('id_profile');
            $enable_identification = (int) Configuration::get(
                'B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE',
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
            $enable_wesbsite = (int) Configuration::get(
                'B2BREGISTRATION_WEBSITE_ENABLE_DISABLE',
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
            $required_country = (int) Configuration::get(
                'B2BREGISTRATION_COUNTRY_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $required_state = (int) Configuration::get(
                'B2BREGISTRATION_STATE_ENABLE_DISABLE',
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
            $default_country = (int) Configuration::get(
                'PS_COUNTRY_DEFAULT',
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
            $enable_birth = (int) Configuration::get(
                'B2BREGISTRATION_DOB_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $enable_vat = (int) Configuration::get(
                'B2BREGISTRATION_ENABLE_DISABLE_VAT_VALIDATION',
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
            $b2b_account_msg = (int) Configuration::get(
                'B2BREGISTRATION_ERROR_MSG_TEXT',
                $this->context->language->id,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            if ($id_profile && Validate::isLoadedObject(
                $b2b_profile = new BBProfile($id_profile, $this->context->language->id)
            )) {
                $default_group = $b2b_profile->b2b_profile_group;
                $enable_birth = $b2b_profile->b2b_dob_active;
                $enable_wesbsite = $b2b_profile->b2b_website;
                $enable_address = $b2b_profile->b2b_address;
                $auto_approvel = $b2b_profile->b2b_customer_auto_approval;
                $enable_custom = $b2b_profile->b2b_custom_fields;
                $b2b_account_msg = $b2b_profile->b2b_account_msg;
                $enable_group_selection = $b2b_profile->b2b_customer_enable_group;
                $enable_identification_number = $b2b_profile->b2b_profile_siret;
            }
            $name_prefix = pSQL(Tools::getValue('name_prefix'));
            $name_suffix = pSQL(Tools::getValue('name_suffix'));
            $first_name = pSQL(Tools::getValue('first_name'));
            $middle_name = pSQL(Tools::getValue('middle_name'));
            $last_name = pSQL(Tools::getValue('last_name'));
            $address_alias = pSQL(Tools::getValue('address_alias'));
            $city = pSQL(Tools::getValue('city'));
            $address = pSQL(Tools::getValue('address'));
            $birthdate = pSQL(Tools::getValue('birthday'));
            $website = pSQL(Tools::getValue('website'));
            $company_name = pSQL(Tools::getValue('company_name'));
            $email = pSQL(Tools::getValue('email'));
            $vat = pSQL(Tools::getValue('vat_number'));
            $password = pSQL(Tools::getValue('password'));
            $newsletter = (int) Tools::getValue('newsletter');
            $confirm_password = pSQL(Tools::getValue('confirm_password'));
            $id_fields = Tools::getValue('id_fields');
            $id_country = (int) Tools::getValue('id_country');
            $id_state = (int) Tools::getValue('id_state');
            $states = [];
            if ($id_country) {
                $states = State::getStatesByIdCountry($id_country);
            }
            if ($enable_group_selection) {
                $default_group = (int) Tools::getValue('customer_group');
            }
            if (!empty($password)) {
                $passwd = Tools::encrypt($password);
            }
            if ($auto_approvel == 1) {
                $active = 1;
            } else {
                $active = 0;
            }
            $partner_option = (int) Tools::getValue('partner_option');
            $identification_number = pSQL(Tools::getValue('identification_number'));
            $customer = new Customer();
            $response = B2BVatNumber::fmmWebServiceCheck($vat);
            $b2b = new BusinessAccountModel();
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

            if ($enable_birth && !empty($birthdate) && !Validate::isBirthDate($birthdate)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'invalid_birthday'
                ];
            }

            if ($enable_wesbsite && in_array('website', $this->module->markedRequired) && empty($website)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'website_required'
                ];
            }

            if (in_array('vat_number', $this->module->markedRequired) && empty($vat)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'vat_required'
                ];
            }
            if ($enable_address) {
                if (empty($address_alias)) {
                    $this->context->controller->errors[] = $this->module->translations[
                        'address_alias_required'
                    ];
                } elseif (empty($city)) {
                    $this->context->controller->errors[] = $this->module->translations[
                        'city_required'
                    ];
                } elseif (empty($id_country) && $required_country) {
                    $this->context->controller->errors[] = $this->module->translations[
                        'country_required'
                    ];
                } elseif (empty($id_state) && $required_state && $states) {
                    $this->context->controller->errors[] = $this->module->translations[
                        'state_required'
                    ];
                } elseif (!Validate::isCityName($city)) {
                    $this->context->controller->errors[] = $this->module->translations[
                        'city_valid'
                    ];
                } elseif (!empty($vat) && $enable_vat && $response == 2) {
                    $this->context->controller->errors[] = $this->module->translations[
                        'vat_not_found'
                    ];
                } elseif (!empty($vat) && $enable_vat && $response <= 0) {
                    $this->context->controller->errors[] = $this->module->translations[
                        'vat_valid'
                    ];
                } elseif (empty($address)) {
                    $this->context->controller->errors[] = $this->module->translations[
                        'address_required'
                    ];
                } elseif (empty($company_name)) {
                    $this->context->controller->errors[] = $this->module->translations[
                        'company_required'
                    ];
                } elseif ($enable_identification_number) {
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

            if (empty($email)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'email_required'
                ];
            }

            if (!Validate::isEmail($email)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'email_valid'
                ];
            }

            if ($customer->customerExists($email, false, true)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'email_exist'
                ];
            }

            if (empty($password)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'password_required'
                ];
            }

            if (empty($confirm_password)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'confirm_required'
                ];
            }

            if ($password != $confirm_password) {
                $this->context->controller->errors[] = $this->module->translations[
                    'confirm_valid'
                ];
            }

            if ($enable_custom && ($customFields = Tools::getValue('fields'))) {
                $this->module->validateB2bFields($customFields);
            }

            if (!count($this->context->controller->errors)) {
                $customer->id_shop = $this->context->shop->id;
                $customer->id_shop_group = $this->context->shop->id_shop_group;
                $customer->id_default_group = $default_group;
                $customer->id_lang = $this->context->language->id;
                $customer->id_gender = $name_prefix;
                $customer->firstname = $first_name;
                $customer->lastname = $last_name;
                $customer->birthday = date('Y-m-d', strtotime($birthdate));
                $customer->email = $email;
                $customer->newsletter = $newsletter;
                $customer->optin = $partner_option;
                $customer->website = $website;
                $customer->company = $company_name;
                $customer->siret = $identification_number;
                $customer->passwd = $passwd;
                $customer->active = $active;
                $res = $customer->save();
                Hook::exec('actionCustomerAccountAdd', [
                    'newCustomer' => $customer,
                ]);
                $result = true;
                if ($res == true) {
                    $b2b->id_customer = $customer->id;
                    $b2b->id_b2b_profile = $id_profile;
                    $b2b->middle_name = $middle_name;
                    $b2b->name_suffix = $name_suffix;
                    $b2b->flag = 1;
                    $b2b->active = $active;
                    $b2b->save();
                    $result = true;
                }
                if ($res == true && $enable_address) {
                    $addres = new Address();
                    $addres->id_customer = $customer->id;
                    $addres->company = $company_name;
                    $addres->firstname = $first_name;
                    $addres->lastname = $last_name;
                    $addres->address1 = $address;
                    $addres->alias = $address_alias;
                    $addres->id_country = $id_country;
                    $addres->id_state = $id_state;
                    $addres->city = $city;
                    $addres->vat_number = $vat;
                    if (empty($identification_number)) {
                        $addres->dni = '-';
                    } else {
                        $addres->dni = $identification_number;
                    }
                    $addres->save();
                    $result = true;
                }
                if ($result && $enable_custom && isset($customFields)) {
                    $this->module->hookActionBBAccountAdd($customFields, $customer->id);
                }
                if ($enable_email_customer == 1 && $result == true) {
                    $subject = Mail::l('Customer Registration');
                    $templateVars = [
                        '{first_name}' => $first_name,
                        '{last_name}' => $last_name,
                        '{company_name}' => $company_name,
                        '{website}' => $website,
                        '{email}' => $email,
                        '{b2b_account_msg}' => $b2b_account_msg,
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
                        $this->context->controller->confirmations[] = $this->module->translations[
                            'email_send'
                        ];
                    }
                    $result = true;
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
                    $template_name = 'customer_registration_admin_notify';
                    $title = $subject;
                    $from = Configuration::get('PS_SHOP_EMAIL');
                    if ($email_sender == '') {
                        $email_sender = Configuration::get('PS_SHOP_NAME');
                    }
                    $fromName = $email_sender;
                    $mailDir = _PS_MODULE_DIR_ . 'b2bregistration/mails/';
                    $toName = 'Admin';
                    $send = Mail::Send(
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
                if ($result == true && $customer->id && $auto_approvel == 1) {
                    $ps_version = _PS_VERSION_;
                    if ($ps_version >= '1.7') {
                        $this->context->updateCustomer($customer);
                        Hook::exec(
                            'actionAuthentication',
                            [
                                'customer' => $this->context->customer,
                            ]
                        );
                    } else {
                        $this->context->cookie->id_customer = (int) $customer->id;
                        $this->context->cookie->customer_firstname = $customer->firstname;
                        $this->context->cookie->customer_lastname = $customer->lastname;
                        $this->context->cookie->logged = 1;
                        Tools::redirect('index.php?controller=authentication?back=my-account');
                    }
                } else {
                    Tools::redirect($this->context->link->getModuleLink(
                        'b2bregistration',
                        'business',
                        ['profile_key' => $this->profile_key, 'inprocess' => 1]
                    ));
                }
            }
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme = false);
        $controller = Dispatcher::getInstance()->getController();
        if ($controller == 'business') {
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

    public function displayAjaxGetStatesByCountry()
    {
        $country_id = (int) Tools::getValue('id_country');
        $states = [];
        if ($country_id) {
            $states = State::getStatesByIdCountry($country_id);
        }
        exit(json_encode($states));
    }
}

<?php
/**
 *  B2B Registration.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright © Copyright 2022 - All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  FMM Modules
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class AdminB2BProfilesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'b2b_profile';
        $this->className = 'BBProfile';
        $this->identifier = 'id_b2b_profile';
        $this->list_simple_header = false;
        $this->lang = true;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->multishop_context = Shop::CONTEXT_ALL;
        $this->_orderBy = 'id_b2b_profile';
        $this->bulk_actions = [
            'delete' => [
                'text' => 'Delete selected',
                'confirm' => 'Delete selected items?',
                'icon' => 'icon-trash',
            ],
        ];
        parent::__construct();
        if (Shop::isFeatureActive()) {
            Shop::addTableAssociation(
                $this->table,
                ['type' => 'shop']
            );
        }
        $this->context = Context::getContext();

        $this->fields_list = [
            'id_b2b_profile' => [
                'title' => $this->l('ID'),
                'width' => 'auto',
                'orderby' => true,
            ],
            'b2b_profile_name' => [
                'title' => $this->l('Profile'),
                'filter_key' => 'b.b2b_profile_name',
            ],
            'b2b_link_rewrite' => [
                'title' => $this->l('Key'),
                'filter_key' => 'a.b2b_link_rewrite',
            ],
            'b2b_profile_group' => [
                'title' => $this->l('Group'),
                'orderby' => false,
                'callback' => 'getGroupName',
            ],
            'active' => [
                'title' => $this->l('Status'),
                'align' => 'text-center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a.active',
            ],
            'b2b_customer_auto_approval' => [
                'title' => $this->l('Auto Approvel Customer'),
                'align' => 'text-center',
                'active' => 'b2b_customer_auto_approval',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a.b2b_customer_auto_approval',
            ],
            'b2b_custom_fields' => [
                'title' => $this->l('Enable Custom Fieds'),
                'align' => 'text-center',
                'active' => 'b2b_custom_fields',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a.b2b_custom_fields',
            ],
        ];
    }

    public function getGroupName($id_group)
    {
        $group = new Group($id_group, $this->context->employee->id_lang);

        return $group->name;
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm()
    {
        $this->context->smarty->assign('custom_fields', $this->context->link->getAdminLink('AdminB2BCustomFields'));
        $switch_option = (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) ? 'switch' : 'radio';
        $groups = Group::getGroups($this->context->language->id, $this->context->shop->id);
        $cms_pages = [];
        foreach (CMS::listCms($this->context->language->id) as $cms_page) {
            $cms_pages[] = ['id' => $cms_page['id_cms'], 'name' => $cms_page['meta_title']];
        }

        $this->fields_form = [
            'tinymce' => true,
            'tabs' => [
                'basic_profile' => $this->l('Basic'),
                'advance_profile' => $this->l('Headings'),
            ],
            'legend' => [
                'title' => $this->l('B2B Custom Fileds'),
                'icon' => 'icon-globe',
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => 'id_b2b_profile',
                ],
                [
                    'label' => $this->l('Profile Name'),
                    'type' => 'text',
                    'name' => 'b2b_profile_name',
                    'desc' => $this->l('Enater a valid name for your B2B profile.'),
                    'col' => '5',
                    'lang' => true,
                    'required' => true,
                    'tab' => 'basic_profile',
                ],
                [
                    'type' => $switch_option,
                    'label' => $this->l('Enable Profile'),
                    'desc' => $this->l('Enable/disable profile'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                    'tab' => 'basic_profile',
                ],
                [
                    'label' => 'Registration Form URL Key',
                    'type' => 'text',
                    'name' => 'b2b_link_rewrite',
                    'desc' => $this->l('Frontend Default: b2b-customer-create'),
                    'col' => '5',
                    'required' => true,
                    'tab' => 'basic_profile',
                ],
                [
                    'type' => $switch_option,
                    'label' => $this->l('Enable Top Link in Header'),
                    'name' => 'b2b_profile_link',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'b2b_profile_link_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'b2b_profile_link_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                    'tab' => 'basic_profile',
                ],
                [
                    'label' => $this->l('Top Link Text'),
                    'type' => 'text',
                    'name' => 'b2b_profile_link_text',
                    'col' => '5',
                    'lang' => true,
                    'required' => true,
                    'tab' => 'basic_profile',
                ],
                [
                    'type' => $switch_option,
                    'label' => $this->l('B2B Customer Auto Approvel'),
                    'name' => 'b2b_customer_auto_approval',
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
                    'name' => 'b2b_custom_fields',
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
                    'label' => $this->l('Pending Account Message Text'),
                    'type' => 'textarea',
                    'name' => 'b2b_account_msg',
                    'col' => 8,
                    'lang' => true,
                    'autoload_rte' => true,
                    'required' => true,
                    'tab' => 'basic_profile',
                ],
                [
                    'label' => $this->l('Choose CMS Page for Terms and Conditions'),
                    'type' => 'select',
                    'name' => 'b2b_tos_page',
                    'required' => true,
                    'options' => [
                        'query' => $cms_pages,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'tab' => 'basic_profile',
                ],
                [
                    'type' => $switch_option,
                    'label' => $this->l('Enable Name Prefix'),
                    'name' => 'b2b_name_prefix_active',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'b2b_name_prefix_active_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'b2b_name_prefix_active_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                    'tab' => 'basic_profile',
                ],
                [
                    'label' => $this->l('Name Prefix Dropdown Options'),
                    'type' => 'B2BREGISTRATION_NAME_PREFIX_OPTIONS',
                    'name' => 'b2b_name_prefix',
                    'required' => true,
                    'tab' => 'basic_profile',
                ],
                [
                    'type' => $switch_option,
                    'label' => $this->l('Enable Name Suffix'),
                    'name' => 'b2b_name_suffix_active',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'b2b_name_suffix_active_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'b2b_name_suffix_active_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                    'tab' => 'basic_profile',
                ],
                [
                    'label' => $this->l('Name Suffix Dropdown Options'),
                    'type' => 'text',
                    'name' => 'b2b_name_suffix',
                    'desc' => $this->l('Comma (,) separated values.e.g MD,PHD'),
                    'col' => '5',
                    'required' => true,
                    'tab' => 'basic_profile',
                ],
                [
                    'type' => $switch_option,
                    'label' => $this->l('Enable Middle Name'),
                    'name' => 'b2b_middle_name_active',
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
                    'name' => 'b2b_customer_enable_group',
                    'is_bool' => true,
                    'desc' => $this->l('Use this to enable and disable group selection for front form'),
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
                    'type' => 'group',
                    'label' => $this->l('Selected Groups'),
                    'desc' => $this->l('Enable groups for front form'),
                    'name' => 'groupBox',
                    'values' => Group::getGroups(Context::getContext()->language->id, $this->context->shop->id),
                    'tab' => 'basic_profile',
                ],
                [
                    'label' => $this->l('Assign Groups'),
                    'type' => 'select',
                    'name' => 'b2b_profile_group',
                    'required' => true,
                    'options' => [
                        'query' => $groups,
                        'id' => 'id_group',
                        'name' => 'name',
                    ],
                    'tab' => 'basic_profile',
                ],

                [
                    'label' => $this->l('Personal Data Heading'),
                    'type' => 'text',
                    'name' => 'b2b_personal_info_heading',
                    'col' => '5',
                    'lang' => true,
                    'required' => true,
                    'tab' => 'advance_profile',
                ],
                [
                    'label' => $this->l('Company Data Heading'),
                    'type' => 'text',
                    'name' => 'b2b_company_info_heading',
                    'col' => '5',
                    'lang' => true,
                    'required' => true,
                    'tab' => 'advance_profile',
                ],
                [
                    'label' => $this->l('Signin Data Heading'),
                    'type' => 'text',
                    'name' => 'b2b_signin_heading',
                    'col' => '5',
                    'lang' => true,
                    'required' => true,
                    'tab' => 'advance_profile',
                ],
                [
                    'label' => $this->l('Address Data Heading'),
                    'type' => 'text',
                    'name' => 'b2b_address_heading',
                    'col' => '5',
                    'lang' => true,
                    'required' => true,
                    'tab' => 'advance_profile',
                ],
                [
                    'label' => $this->l('Custom Field Heading'),
                    'type' => 'text',
                    'name' => 'b2b_customfields_heading',
                    'col' => '5',
                    'lang' => true,
                    'required' => true,
                    'tab' => 'advance_profile',
                ],

                [
                    'type' => 'text',
                    'label' => $this->l('Redirection URL'),
                    'desc' => sprintf(
                        '%s <strong>https://www.example.com/abx/123-xyz.html</strong>',
                        $this->l('Should be an absolute URL containing protocol. example:')
                    ),
                    'name' => 'b2b_redirect_url',
                    'col' => 7,
                    'required' => true,
                    'tab' => 'basic_profile',
                ],
                [
                    'type' => $switch_option,
                    'label' => $this->l('Enable Date of Birth'),
                    'name' => 'b2b_profile_dob',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'b2b_profile_dob_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'b2b_profile_dob_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                    'tab' => 'basic_profile',
                ],
                [
                    'type' => $switch_option,
                    'label' => $this->l('Enable Identification/Siret'),
                    'name' => 'b2b_profile_siret',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'b2b_profile_siret_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'b2b_profile_siret_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                    'tab' => 'basic_profile',
                ],
                [
                    'type' => $switch_option,
                    'label' => $this->l('Enable Website'),
                    'name' => 'b2b_website',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'b2b_website_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'b2b_website_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                    'tab' => 'basic_profile',
                ],
                [
                    'type' => $switch_option,
                    'label' => $this->l('Enable Address'),
                    'name' => 'b2b_address',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'b2b_address_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'b2b_address_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                    'tab' => 'basic_profile',
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default button pull-right',
            ],
            'buttons' => [
                'save-and-stay' => [
                    'title' => $this->l('Save and Stay'),
                    'name' => 'submitAdd' . $this->table . 'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ],
            ],
        ];
        $cpGender = [];
        $shops = '';
        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
                'tab' => 'basic_profile',
            ];
        }
        $genders = businessAccountModel::getAllGenders($this->context->language->id);
        $current_object = $this->loadObject(true);
        $groups = Group::getGroups($this->context->language->id);

        if (Tools::getValue($this->identifier) && $this->object->id) {
            $cpGender = !empty($this->object->b2b_name_prefix) ? explode(',', $this->object->b2b_name_prefix) : [];
            $groups_ids = !empty($this->object->groupBox) ? explode(',', $this->object->groupBox) : [];
            foreach ($groups as $group) {
                $this->fields_value['groupBox_' . $group['id_group']] = Tools::getValue(
                    'groupBox_' . $group['id_group'],
                    in_array($group['id_group'], $groups_ids)
                );
            }
        } else {
            foreach ($groups as $group) {
                $this->fields_value['groupBox_' . $group['id_group']] = false;
            }
        }

        $this->context->smarty->assign([
            'genders' => $genders,
            'cpGender' => $cpGender,
            'ps_version' => _PS_VERSION_,
            'ajax_token' => Configuration::get('B2BREGISTRATION_TOKEN'),
            'config_url' => $this->context->link->getAdminLink('AdminB2BProfiles'),
            'PS_ALLOW_ACCENTED_CHARS_URL' => Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
        ]);

        return parent::renderForm();
    }

    public function ajaxProcessOpenPrefixesDialog()
    {
        $ajax_token = pSQL(Tools::getValue('ajax_token'));
        $token = Configuration::get('B2BREGISTRATION_TOKEN');
        if ($ajax_token == $token) {
            $languages = Language::getLanguages();
            $defaultFormLanguage = (int) $this->context->employee->id_lang;
            $action_url = $this->context->link->getAdminLink('AdminB2BProfiles');
            $this->context->smarty->assign([
                'languages' => $languages,
                'defaultFormLanguage' => $defaultFormLanguage,
                'action_url' => $action_url,
                'ajax_token' => $ajax_token,
                'ps_version' => _PS_VERSION_,
            ]);
            $res = $this->context->smarty->fetch(
                _PS_MODULE_DIR_ .
                'b2bregistration/views/templates/admin/prefix/new_prefix.tpl'
            );
            exit(json_encode($res));
        }
    }

    public function ajaxProcessSavePrefix()
    {
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

    public function ajaxProcessDeletePrefix()
    {
        $ajax_token = pSQL(Tools::getValue('ajax_token'));
        $token = Configuration::get('B2BREGISTRATION_TOKEN');
        if ($ajax_token == $token) {
            $result = true;
            $id = (int) Tools::getValue('id_prefix');
            if (Validate::isLoadedObject($obj = new Gender($id))) {
                $result &= $obj->delete();
            }
            exit(json_encode($result));
        }
    }

    public function initProcess()
    {
        parent::initProcess();
        if (Tools::isSubmit(sprintf('submitAdd%s', $this->table))) {
            $languages = Language::getLanguages();
            $b2b_link_rewrite = Tools::getValue('b2b_link_rewrite');
            $shop_associations = Tools::getValue('checkBoxShopAsso');

            if (!$b2b_link_rewrite || !Validate::isLinkRewrite($b2b_link_rewrite)) {
                $this->errors[] = $this->l('Registration Form URL Key is invalid.');
            }

            if (Tools::getValue('b2b_name_prefix_active')) {
                if (!($prefix = Tools::getValue('b2b_name_prefix')) || !count($prefix)) {
                    $this->errors[] = $this->l('Name Prefix Dropdown Options is required.');
                }
            }

            if (Tools::getValue('b2b_name_suffix_active')) {
                if (!($suffix = Tools::getValue('b2b_name_suffix')) || !count(explode(',', $suffix))) {
                    $this->errors[] = $this->l('Name Suffix Dropdown Options is required.');
                }
            }

            $ml_fields = [
                'b2b_account_msg' => $this->l('Pending Account Message Text'),
                'b2b_profile_name' => $this->l('Profile'),
                'b2b_profile_link_text' => $this->l('Top Link'),
                'b2b_signin_heading' => $this->l('Signin Data Heading'),
                'b2b_address_heading' => $this->l('Address Data Heading'),
                'b2b_customfields_heading' => $this->l('Custom Field Heading'),
                'b2b_company_info_heading' => $this->l('Company Data Heading'),
                'b2b_personal_info_heading' => $this->l('Personal Data Heading'),
            ];
            $default_lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
            foreach ($ml_fields as $field_name => $label) {
                $field_value = Tools::getValue($field_name . '_' . $default_lang->id);
                if (!$field_value || !Validate::isString($field_value)) {
                    $this->errors[] = sprintf($this->l('"%s" is invalid in %s'), $label, $default_lang->name);
                }
            }

            if (Tools::getValue('b2b_customer_enable_group') == 1 && !Tools::getValue('groupBox')) {
                $this->errors[] = $this->l('Selected groups cannot be empty when enabled');
            }

            $id_b2b_profile = (int) Tools::getValue('b2b_profile_group');
            $defaultB2bGroup = Configuration::get(
                'B2BREGISTRATION_GROUPS',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $occupiedGroups = [];
            $occupiedGroups = BBProfile::getAllOccupiedGroups(false, [$id_b2b_profile]);
            if ($defaultB2bGroup == $id_b2b_profile) {
                $this->errors[] = sprintf(
                    $this->l('Selected group is already assigned to default B2B profile.') .
                    ' <strong><a href="%s" target="_blank">%s</a></strong>',
                    $this->context->link->getAdminLink('AdminGroups') . '&addgroup',
                    $this->l('Create a New Group')
                );
            } elseif (in_array($id_b2b_profile, $occupiedGroups)) {
                $this->errors[] = sprintf(
                    $this->l('Selected group is already occupied.
                    Please select a new group for your B2B profile.') .
                    ' <strong><a href="%s" target="_blank">%s</a></strong>',
                    $this->context->link->getAdminLink('AdminGroups') . '&addgroup',
                    $this->l('Create a New Group')
                );
            }
            if (count($this->errors)) {
                return $this->errors;
            }
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('b2b_custom_fields' . $this->table)) {
            if (
                ($id_profile = Tools::getValue('id_b2b_profile'))
                && Validate::isLoadedObject($profile = new BBProfile((int) $id_profile))
            ) {
                $profile->b2b_custom_fields = !$profile->b2b_custom_fields;
                if ($profile->update()) {
                    $this->confirmations[] = $this->l('B2B custom fields status updated successfully.');
                }
            }
        } elseif (Tools::isSubmit('b2b_customer_auto_approval' . $this->table)) {
            if (
                ($id_profile = Tools::getValue('id_b2b_profile'))
                && Validate::isLoadedObject($profile = new BBProfile((int) $id_profile))
            ) {
                $profile->b2b_customer_auto_approval = !$profile->b2b_customer_auto_approval;
                if ($profile->update()) {
                    $this->confirmations[] = $this->l('B2B auto approval status updated successfully.');
                }
            }
        }
        parent::postProcess();
    }
}

<?php
/**
 * B2B Registration.
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
class AdminB2BCustomersController extends ModuleAdminController
{
    protected $profile;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'b2bregistration';
        $this->className = 'BusinessAccountModel';
        $this->identifier = 'id_b2bregistration';
        $this->list_simple_header = false;
        $this->lang = false;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->specificConfirmDelete = false;
        $this->_filterHaving = true;
        parent::__construct();
        $this->option_fields = ['multiselect', 'checkbox', 'radio'];

        $customFields = [];
        $customer = $this->loadObject(true);

        $where = 'a.active = 1';
        $this->fieldLabels = BToBCustomFields::getFieldsOnly(
            $where,
            (int) Context::getContext()->shop->id,
            $this->context->language->id,
            'a.position'
        );
        $this->bulk_actions = [
            'delete' => ['text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')],
        ];
        if (isset($this->fieldLabels) && $this->fieldLabels) {
            foreach ($this->fieldLabels as $label) {
                $customFields['field_value_' . $label['id_bb_registration_fields']] = [
                    'title' => $label['field_name'],
                    'field_type' => $label['field_type'],
                    'orderby' => false,
                    'search' => false,
                ];
                if ($label['field_type'] == 'boolean') {
                    $customFields['field_value_' . $label['id_bb_registration_fields']]['align'] = 'center';
                }
                if ($label['field_type'] == 'image') {
                    $customFields['field_value_' . $label['id_bb_registration_fields']]['callback'] = 'displayImage';
                }
                if ($label['field_type'] == 'attachment') {
                    $customFields['field_value_' . $label['id_bb_registration_fields']]['callback'] = 'downloadFile';
                }
            }
        }
        $titles_array = [];
        $genders = Gender::getGenders($this->context->language->id);
        foreach ($genders as $gender) {
            /* @var Gender $gender */
            $titles_array[$gender->id_gender] = $gender->name;
        }

        $this->_select = 'a.active as status, b.*, c.city, c.vat_number, c.address1, c.alias, gl.name as title';
        $this->_group = ' GROUP BY c.`id_customer`';
        $this->_join = '
        INNER JOIN `' . _DB_PREFIX_ . 'customer` b ON (b.`id_customer` = a.`id_customer`)
        LEFT JOIN `' . _DB_PREFIX_ . 'address` c ON c.id_customer = b.id_customer
        LEFT JOIN ' . _DB_PREFIX_ .
        'gender_lang gl ON (b.id_gender = gl.id_gender AND gl.id_lang = ' .
        (int) $this->context->language->id . ')
        ';

        $this->context = Context::getContext();
        $this->fields_list = [
            'id_b2bregistration' => [
                'title' => $this->l('ID'),
                'width' => 'auto',
                'orderby' => true,
                'filter_key' => 'a!id_b2bregistration',
            ],
            'title' => [
                'title' => $this->l('Social title'),
                'filter_key' => 'b!id_gender',
                'type' => 'select',
                'list' => $titles_array,
                'filter_type' => 'int',
                'order_key' => 'gl!name',
            ],
            'firstname' => [
                'title' => $this->l('First name'),
                'maxlength' => 30,
                'filter_key' => 'b!firstname',
            ],
            'lastname' => [
                'title' => $this->l('Last name'),
                'maxlength' => 30,
                'orderby' => false,
                'filter_key' => 'b!lastname',
            ],
            'email' => [
                'title' => $this->l('Email address'),
                'maxlength' => 50,
                'filter_key' => 'b!email',
            ],
            'id_b2b_profile' => [
                'title' => $this->l('Profile'),
                'orderby' => false,
                'filter_key' => 'b.id_b2b_profile',
                'callback' => 'getProfileName',
            ],
            'status' => [
                'title' => $this->l('Enabled'),
                'align' => 'text-center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!active',
            ],
            'company' => [
                'title' => $this->l('Company'),
                'filter_key' => 'b!company',
            ],
            'siret' => [
                'title' => $this->l('Identification Number'),
                'filter_key' => 'b!siret',
            ],
            'address1' => [
                'title' => $this->l('Address'),
                'filter_key' => 'c!address1',
            ],
            'city' => [
                'title' => $this->l('City'),
                'filter_key' => 'c!city',
            ],
        ];
    }

    public function getProfileName($id_profile)
    {
        if (!$id_profile) {
            return $this->l('Default');
        } else {
            $profile = new BBProfile($id_profile, $this->context->employee->id_lang);

            return $profile->b2b_profile_name;
        }
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $dashboard_link = $this->context->link->getAdminLink('AdminModules') .
        '&configure=' .
        $this->module->name .
        '&tab_module=' .
        $this->module->tab .
        '&module_name=' .
        $this->module->name;
        $this->context->smarty->assign('confirm', 'Sure?');
        $this->tpl_list_vars['dashboard_link'] = $dashboard_link;

        return parent::renderList();
    }

    public function renderForm()
    {
        $switch = (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) ? 'switch' : 'radio';
        if (!($obj = $this->loadObject(true))) {
            return;
        }
        $default_country[0] = [
            'id_country' => 0,
            'name' => 'Please Choose',
        ];
        $available_countries = Country::getCountries($this->context->language->id, true);
        $available_countries = array_merge($default_country, $available_countries);
        $id_customer = 0;
        if ($obj->id) {
            $id_customer = $obj->id_customer;
            $b2b = BusinessAccountModel::getB2BCustomer($this->context->language->id, $obj->id_customer);
        }
        $name_suffixs = [];
        $nfs = explode(',', Configuration::get(
            'B2BREGISTRATION_NAME_SUFFIX_OPTIONS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $enable_custom = (int) Configuration::get(
            'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        if (Validate::isLoadedObject($this->profile)) {
            $nfs = (isset($this->profile->b2b_name_suffix) ? explode(',', $this->profile->b2b_name_suffix) : []);
        }

        if (isset($nfs) && $nfs) {
            foreach ($nfs as $suffix) {
                $name_suffixs[] = [
                    'id' => trim($suffix),
                    'name' => trim($suffix),
                ];
            }
        }
        $this->context->smarty->assign('name_suffix', $name_suffixs);
        $genders = Gender::getGenders();
        $list_genders = [];
        foreach ($genders as $key => $gender) {
            $list_genders[$key]['id'] = 'gender_' . $gender->id;
            $list_genders[$key]['value'] = $gender->id;
            $list_genders[$key]['label'] = $gender->name;
        }
        $years = Tools::dateYears();
        $months = Tools::dateMonths();
        $days = Tools::dateDays();
        $groups = Group::getGroups($this->default_form_language, true);
        $headings_collection = BBProfile::getAllProfiles();
        $list_headings = [];
        if (!empty($headings_collection)) {
            foreach ($headings_collection as $key => $heading) {
                $list_headings[] = [
                    'id_option' => trim($heading['id_b2b_profile']),
                    'name' => trim($heading['b2b_profile_name']),
                ];
            }
        }
        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Customer'),
                'icon' => 'icon-user',
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => 'id_customer',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'id_b2bregistration',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'id_address',
                ],
                [
                    'type' => $switch,
                    'label' => $this->l('Enabled Customer'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'actives_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'actives_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                    'hint' => $this->l('Enable or disable customer login.'),
                ],
                [
                    'type' => 'radio',
                    'label' => $this->l('Social title'),
                    'name' => 'id_gender',
                    'required' => false,
                    'class' => 't',
                    'values' => $list_genders,
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Profile'),
                    'name' => 'id_b2b_profile',
                    'desc' => $this->l(
                        'Associate your custom field with a profile.
                        Custom field will be displayed only on respective profile form.'
                    ),
                    'required' => false,
                    'options' => [
                        'query' => $list_headings,
                        'id' => 'id_option',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('First name'),
                    'name' => 'firstname',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:') . ' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Middle name'),
                    'name' => 'middle_name',
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:') . ' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Last name'),
                    'name' => 'lastname',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:') . ' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Company'),
                    'name' => 'company',
                    'col' => '4',
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Name Suffix'),
                    'name' => 'name_suffix',
                    'options' => [
                        'query' => $name_suffixs,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'default' => ['id' => '', 'name' => '--'],
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('SIRET'),
                    'name' => 'siret',
                    'col' => '4',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Website'),
                    'name' => 'website',
                    'col' => '4',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Address Alias'),
                    'name' => 'alias',
                    'col' => '4',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Address'),
                    'name' => 'address1',
                    'col' => '4',
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Country'),
                    'id' => 'b2bregistration_country',
                    'name' => 'country',
                    'options' => [
                        'query' => $available_countries,
                        'id' => 'id_country',
                        'name' => 'name',
                    ],
                    'default' => ['id' => 0, 'name' => 'Please Choose'],
                    'onchange' => 'getStatesByCountry(this.value)',
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('State'),
                    'name' => 'state',
                    'id' => 'b2bregistration_state',
                    'col' => '4',
                    'options' => [
                        'query' => [],
                        'id' => 'id_state',
                        'name' => 'name',
                    ],
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('City'),
                    'name' => 'city',
                    'col' => '4',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Vat Number'),
                    'name' => 'vat_number',
                    'col' => '4',
                ],
                [
                    'type' => 'text',
                    'prefix' => '<i class="icon-envelope-o"></i>',
                    'label' => $this->l('Email address'),
                    'name' => 'email',
                    'col' => '4',
                    'required' => true,
                    'autocomplete' => false,
                ],
                [
                    'type' => 'password',
                    'label' => $this->l('Password'),
                    'name' => 'passwd',
                    'required' => true,
                    'col' => '4',
                ],
                [
                    'type' => 'birthday',
                    'label' => $this->l('Birthday'),
                    'name' => 'birthday',
                    'required' => true,
                    'options' => [
                        'days' => $days,
                        'months' => $months,
                        'years' => $years,
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Default customer group'),
                    'name' => 'id_default_group',
                    'options' => [
                        'query' => $groups,
                        'id' => 'id_group',
                        'name' => 'name',
                    ],
                    'col' => '4',
                    'hint' => [
                        $this->l('This group will be the user\'s default group.'),
                        $this->l('Only the discount for the selected group will be applied to this customer.'),
                    ],
                ],
                [
                    'type' => $switch,
                    'label' => $this->l('Enabled Newsletter'),
                    'name' => 'newsletter',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                    'hint' => $this->l('Enable or disable customer login.'),
                ],
                [
                    'type' => $switch,
                    'label' => $this->l('Partner offers'),
                    'name' => 'optin',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'optin_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'optin_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                    'disabled' => (bool) !Configuration::get('PS_CUSTOMER_OPTIN'),
                    'hint' => $this->l('This customer will receive your ads via email.'),
                ],
            ],
        ];

        $this->fields_form['submit'] = [
            'title' => $this->l('Save'),
        ];

        // +++++ START +++++ b2b custom fields +++++//
        $fieldValues = [];
        $customerFields = BToBCustomFields::getAllFields('val.id_customer = ' . (int) $id_customer);

        if (isset($customerFields) && $customerFields) {
            foreach ($customerFields as $value) {
                $input_name = (in_array(
                    $value['field_type'],
                    $this->option_fields
                )) ? 'fields[' . $value['id_bb_registration_fields'] .
                '][]' : 'fields[' . $value['id_bb_registration_fields'] . ']';
                $value = (in_array(
                    $value['field_type'],
                    ['checkbox', 'multiselect']
                )) ? explode(
                    ',',
                    $value['field_value_id']
                ) : ((isset($value['value'])
                    && $value['value']) ? $value['value'] : $value['field_value_id']);
                $fieldValues[$input_name] = $value;
            }
        }

        $temp_fields = [];
        $fields_value = [];

        if (isset($this->fieldLabels) && $this->fieldLabels) {
            foreach ($this->fieldLabels as $field) {
                $input = [];
                $options = [];
                if (BToBCustomFields::getOptions($field['id_bb_registration_fields'])) {
                    $options = BToBCustomFields::getOptions($field['id_bb_registration_fields']);
                }
                $input['type'] = $field['field_type'];
                $input['required'] = (bool) $field['value_required'];
                $input['name'] = (in_array(
                    $field['field_type'],
                    $this->option_fields
                )) ? 'fields[' . $field['id_bb_registration_fields'] .
                '][]' : 'fields[' . $field['id_bb_registration_fields'] . ']';
                $input['label'] = (isset(
                    $field['field_name']
                ) && $field['field_name']) ? $field['field_name'] : sprintf(
                    $this->l('Field %d'),
                    $field['id_bb_registration_fields']
                );

                if (!isset($input['name']) && !isset($fieldValues[$input['name']])
                    && !$fieldValues[$input['name']]) {
                    $this->fields_value[$input['name']] = (isset(
                        $field['default_value']
                    ) && $field['default_value']) ? $field['default_value'] : '';
                }
                switch ($field['field_type']) {
                    case 'message':
                        $input['type'] = 'free';
                        $fieldValues[$input['name']] = (isset(
                            $field['default_value']
                        ) && $field['default_value']) ? '<label class="form-control-static">' .
                        $field['default_value'] . '</label>' : '';
                        break;
                    case 'boolean':
                        $input['type'] = 'radio';
                        $input['values'] = [
                            [
                                'id' => $input['name'] . '_on',
                                'value' => 'Yes',
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => $input['name'] . '_off',
                                'value' => 'No',
                                'label' => $this->l('No'),
                            ],
                        ];
                        break;
                    case 'radio':
                        if (isset($options) && $options) {
                            foreach ($options as $val) {
                                $input['values'][] = [
                                    'id' => 'field_value_id_' . $val['field_value_id'],
                                    'value' => $val['field_value_id'],
                                    'label' => $val['field_value'],
                                ];
                            }
                        } else {
                            $input['values'][] = [
                                'id' => 'field_value_id',
                                'value' => null,
                                'label' => '',
                            ];
                        }
                        break;
                    case 'checkbox':
                        if (!isset(
                            $input['name']
                        ) && !isset($fieldValues[$input['name']])
                            && !$fieldValues[$input['name']]) {
                            $this->fields_value[$input['name']] = [];
                        }
                        $input['values'] = [
                            'query' => $options,
                            'id' => 'field_value_id',
                            'name' => 'field_value',
                        ];
                        break;
                    case 'select':
                        $input['options'] = [
                            'query' => $options,
                            'id' => 'field_value_id',
                            'name' => 'field_value',
                        ];
                        break;
                    case 'multiselect':
                        $input['type'] = 'swap';
                        $input['multiple'] = true;
                        if (!isset($input['name'])
                            && !isset($fieldValues[$input['name']]) && !$fieldValues[$input['name']]) {
                            $this->fields_value[$input['name']] = [];
                        }
                        $input['options'] = [
                            'query' => $options,
                            'id' => 'field_value_id',
                            'name' => 'field_value',
                        ];
                        break;
                    case 'image':
                    case 'attachment':
                        $name = (isset($fieldValues[$input['name']])
                            && $fieldValues[$input['name']]) ? $fieldValues[$input['name']] : false;
                        $thumb_size = ($name && file_exists($name)) ? filesize($name) / 1000 : false;

                        if ($field['field_type'] == 'image') {
                            $input['display_image'] = true;
                            $input['image'] = $name ? '<img src="' . __PS_BASE_URI__ .
                            Tools::str_replace_once(_PS_ROOT_DIR_ . '/', '', $name) .
                            '?time=' . time() . '" alt="" class="imgm img-thumbnail" width="50%"/>' : false;
                        } else {
                            if (isset($name) && file_exists($name) && $field['id_bb_registration_fields']) {
                                $link = $this->context->link->getAdminLink('AdminCustomerRegistrationFields') .
                                '&downloadAttachment&id_bb_registration_fields=' .
                                $field['id_bb_registration_fields'] . '&' .
                                $this->identifier . '=' . Tools::getValue($this->identifier);
                                if (Configuration::get('PS_REWRITING_SETTINGS')) {
                                    $link = Tools::strReplaceFirst('&', '?', $link);
                                }
                                $input['file'] = isset($link) ? $link : null;
                            }
                        }
                        $input['type'] = 'file';
                        $input['size'] = $thumb_size;
                        $input['id'] = 'file_' . $field['id_bb_registration_fields'];
                        $input['delete_url'] = self::$currentIndex . '&' . $this->identifier . '=' .
                        Tools::getValue($this->identifier) . '&token=' . $this->token .
                            '&deleteImage&id_bb_registration_fields=' . $field['id_bb_registration_fields'];
                        break;
                }
                $temp_fields[] = $input;
            }
        } else {
            $temp_fields[] = [
                'type' => 'free',
                'name' => 'none',
            ];
        }
        if (isset($temp_fields) && $temp_fields) {
            foreach ($temp_fields as $field) {
                array_push($this->fields_form['input'], $field);
            }
        }

        $this->fields_value = $fieldValues;
        $birthday = explode('-', $this->getFieldValue($obj, 'birthday'));
        $this->context->smarty->assign('enable_custom', $enable_custom);
        if (!empty($b2b)) {
            Media::addJsDef(['id_state' => $b2b['id_state']]);
            $this->context->smarty->assign('selected_name_sufix', $b2b['name_suffix']);
            $this->context->smarty->assign('id_customer', $obj->id_customer);
            $birthday = explode('-', $b2b['birthday']);
            $this->fields_value['years'] = $birthday[0];
            $this->fields_value['months'] = $birthday[1];
            $this->fields_value['days'] = $birthday[2];
            $this->fields_value['name_suffix'] = $b2b['name_suffix'];
            $this->fields_value['id_customer'] = $b2b['id_customer'];
            $this->fields_value['firstname'] = $b2b['firstname'];
            $this->fields_value['lastname'] = $b2b['lastname'];
            $this->fields_value['email'] = $b2b['email'];
            $this->fields_value['id_gender'] = $b2b['id_gender'];
            $this->fields_value['passwd'] = $b2b['passwd'];
            $this->fields_value['active'] = $b2b['active'];
            $this->fields_value['id_default_group'] = $b2b['id_default_group'];
            $this->fields_value['birthday'] = $b2b['birthday'];
            $this->fields_value['optin'] = $b2b['optin'];
            $this->fields_value['newsletter'] = $b2b['newsletter'];
            $this->fields_value['website'] = $b2b['website'];
            $this->fields_value['siret'] = $b2b['siret'];
            $this->fields_value['company'] = $b2b['company'];
            $this->fields_value['alias'] = $b2b['alias'];
            $this->fields_value['id_address'] = $b2b['id_address'];
            $this->fields_value['country'] = $b2b['id_country'];
            $this->fields_value['state'] = $b2b['id_state'];
            $this->fields_value['address1'] = $b2b['address1'];
            $this->fields_value['vat_number'] = $b2b['vat_number'];
            $this->fields_value['city'] = $b2b['city'];
        } else {
            $this->fields_value = [
                'years' => $this->getFieldValue($obj, 'birthday') ? $birthday[0] : 0,
                'months' => $this->getFieldValue($obj, 'birthday') ? $birthday[1] : 0,
                'days' => $this->getFieldValue($obj, 'birthday') ? $birthday[2] : 0,
            ];
        }
        $tab_link = $this->context->link->getAdminLink('AdminModules') .
        '&configure=' .
        $this->module->name .
        '&tab_module=' .
        $this->module->tab .
        '&module_name=' .
        $this->module->name;
        $this->context->smarty->assign('tab_link', $tab_link);

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAdd' . $this->table)) {
            $result = false;
            $id_customer = (int) Tools::getValue('id_customer');
            $id_b2bregistration = (int) Tools::getValue('id_b2bregistration');
            $id_address = (int) Tools::getValue('id_address');
            $new = (!$id_customer) ? true : false;
            $default_country = (int) Configuration::get(
                'PS_COUNTRY_DEFAULT',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $enable_email = (int) Configuration::get(
                'B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE',
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

            $enable_custom = (int) Configuration::get(
                'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );

            $id_gender = (int) Tools::getValue('id_gender');
            $id_b2b_profile = Tools::getValue('id_b2b_profile');
            $firstname = pSQL(Tools::getValue('firstname'));
            $lastname = pSQL(Tools::getValue('lastname'));
            $middlename = pSQL(Tools::getValue('middle_name'));
            $company = pSQL(Tools::getValue('company'));
            $siret = pSQL(Tools::getValue('siret'));
            $website = pSQL(Tools::getValue('website'));
            $alias = pSQL(Tools::getValue('alias'));
            $address1 = pSQL(Tools::getValue('address1'));
            $city = pSQL(Tools::getValue('city'));
            $vat = pSQL(Tools::getValue('vat_number'));
            $email = pSQL(Tools::getValue('email'));
            $password = trim(Tools::getValue('passwd'));
            $name_suffix = pSQL(Tools::getValue('name_suffix'));
            $address2 = pSQL(Tools::getValue('address2'));
            $id_country = pSQL(Tools::getValue('id_country', $default_country));
            $day = Tools::getValue('days');
            $id_default_group = (int) Tools::getValue('id_default_group');
            $month = Tools::getValue('months');
            $year = Tools::getValue('years');
            $optin = (int) Tools::getValue('optin');
            $country = (int) Tools::getValue('country');
            $id_state = (int) Tools::getValue('state');
            $newsletter = (int) Tools::getValue('newsletter');
            $birthdate = trim(sprintf('%s-%s-%s', $year, $month, $day), '-');
            if (!empty($password)) {
                $password = Tools::encrypt($password);
            }
            $active = (true === $auto_approvel) ? true : (bool) Tools::getValue('active');

            $customer = ($id_customer) ? new Customer($id_customer) : new Customer();
            if (empty($firstname)) {
                $this->context->controller->errors[] = $this->l('Enter First Name');
            } elseif (!Validate::isName($firstname)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter valid first name'
                );
            } elseif (empty($lastname)) {
                $this->context->controller->errors[] = $this->l('Enter Last Name');
            } elseif (!Validate::isName($lastname)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter valid last name'
                );
            } elseif (empty($company)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter company name'
                );
            } elseif ($siret && !Validate::isSiret($siret) && _PS_VERSION_ < '1.7.0.0') {
                $this->context->controller->errors[] = $this->l(
                    'Please enter valid SIRET/identification number'
                );
            } elseif (empty($website)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter website link'
                );
            } elseif (empty($alias)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter address alias e.g Home '
                );
            } elseif (empty($address1)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter address'
                );
            } elseif (!Validate::isAddress($address1)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter valid address'
                );
            } elseif (empty($city)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter city'
                );
            } elseif (!Validate::isCityName($city)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter valid city name'
                );
            } elseif (empty($email)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter email address'
                );
            } elseif (!Validate::isEmail($email)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter valid email address'
                );
            } elseif ((!$id_customer && Customer::customerExists($email, true))
                || ($id_customer && $customer->email != $email && Customer::customerExists($email, true))) {
                $this->context->controller->errors[] = $this->l(
                    'Email Already Exists. Please choose another one'
                );
            } elseif (!Validate::isAddress($address2)) {
                $this->context->controller->errors[] = $this->l('Invalid address 2 value.');
            } elseif (!empty($birthdate) && !Validate::isDate($birthdate)) {
                $this->context->controller->errors[] = $this->l(
                    'Invalid birthdate'
                );
            } elseif (Tools::strlen($siret) > 16) {
                $this->context->controller->errors[] = $this->l(
                    'Siret number range is 1-16'
                );
            } elseif ($id_customer == 0 && empty($password)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter password'
                );
            }
            if ($enable_custom && ($customFields = Tools::getValue('fields'))) {
                $this->module->validateB2bFields($customFields);
            }
            if (!count($this->context->controller->errors)) {
                $customer->id_shop = $this->context->shop->id;
                $customer->id_shop_group = $this->context->shop->id_shop_group;
                $customer->id_default_group = $id_default_group;
                $customer->id_lang = $this->context->language->id;
                $customer->id_gender = $id_gender;
                $customer->firstname = $firstname;
                $customer->lastname = $lastname;
                $customer->birthday = $birthdate;
                $customer->email = $email;
                $customer->newsletter = $newsletter;
                $customer->optin = $optin;
                $customer->website = $website;
                $customer->company = $company;
                $customer->siret = $siret;
                if (!empty($password)) {
                    $customer->passwd = $password;
                }
                $customer->active = $active;
                $res = $customer->save();

                $result = true;
                if ($res) {
                    $addres = ($id_address) ? new Address($id_address) : new Address();
                    $addres->id_customer = (int) $customer->id;
                    $addres->company = $company;
                    $addres->firstname = $firstname;
                    $addres->lastname = $lastname;
                    $addres->vat_number = $vat;
                    $addres->address1 = $address1;
                    $addres->address2 = $address2;
                    $addres->alias = $alias;
                    $addres->city = $city;
                    $addres->id_country = $country;
                    $addres->id_state = $id_state;
                    $addres->save();
                    $b2b = ($id_b2bregistration) ?
                    new BusinessAccountModel($id_b2bregistration) : new BusinessAccountModel();
                    $b2b->id_customer = (int) $customer->id;
                    $b2b->id_b2b_profile = $id_b2b_profile;
                    $b2b->flag = $active;
                    $b2b->active = $active;
                    $b2b->middle_name = $middlename;
                    $b2b->name_suffix = $name_suffix;
                    $b2b->save();
                    $result = true;
                }

                if ($result && $enable_custom && isset($customFields)) {
                    $this->module->hookActionBBAccountAdd($customFields, $customer->id);
                }
                if ($enable_email == 1 && $result == true && $new) {
                    $subject = Mail::l('Customer Registration By Admin');
                    $templateVars = [
                        '{first_name}' => $firstname,
                        '{last_name}' => $lastname,
                        '{company_name}' => $company,
                        '{website}' => $website,
                        '{email}' => $email,
                    ];
                    $template_name = 'b2b_customer_registration';
                    $title = $subject;
                    $from = Configuration::get('PS_SHOP_EMAIL');
                    if ($email_sender == '') {
                        $email_sender = Configuration::get('PS_SHOP_NAME');
                    }
                    $fromName = $email_sender;
                    $mailDir = _PS_MODULE_DIR_ . 'b2bregistration/mails/';
                    $toName = $firstname;
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
                        $this->context->controller->confirmation[] = $this->l(
                            'Email sent successfully'
                        );
                    }
                }
            }
        } elseif (Tools::isSubmit('statusb2bregistration')) {
            $obj = $this->loadObject(true);
            if ($obj->id_customer) {
                $customer = new Customer((int) $obj->id_customer);
                $customer->active = !$customer->active;
                if (!$customer->update()) {
                    $this->errors[] = $this->l('Status update unsuccessful.');
                } else {
                    $this->confirmations[] = $this->l('Customer status updated successfully.');
                }
            }
        } elseif (Tools::isSubmit('deleteb2bregistration')) {
            $obj = $this->loadObject(true);
            if ($obj->id_customer) {
                $customer = new Customer((int) $obj->id_customer);

                if ($customer->delete()) {
                    BusinessAccountModel::extraFieldsDeletion($obj->id_customer);
                }
                $exist_user_fields = BusinessAccountModel::tableExists('b2b_fields_data');
                if ($exist_user_fields) {
                    BToBFieldsData::customFieldsDeletion((int) $obj->id_customer);
                }
            }
        }

        parent::postProcess();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->context->controller->addJS('back.js');
    }

    public function ajaxProcessGetStatesByCountry()
    {
        $id_country = Tools::getValue('id_country');
        if (class_exists('State')) {
            // Fetch states for the given country
            $states = State::getStatesByIdCountry($id_country);

            // Return states as a JSON response
            $this->ajaxDie(json_encode($states));
        } else {
            // Return an error message if the class is not found
            $this->ajaxDie(json_encode(['error' => 'Class State not found']));
        }
    }
}

<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FME Modules
 *  @copyright 2021 FME Modules
 *  @license   Comerical Licence
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class AdminB2BCustomFieldsController extends ModuleAdminController
{
    public $option_fields = [];

    public function __construct()
    {
        $this->table = 'bb_registration_fields';
        $this->className = 'BToBCustomFields';
        $this->identifier = 'id_bb_registration_fields';
        $this->lang = true;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->bootstrap = true;

        parent::__construct();

        $this->bulk_actions = [
            'delete' => ['text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')],
        ];
        $this->context = Context::getContext();
        $this->option_fields = ['multiselect', 'select', 'checkbox', 'radio'];

        $this->position_identifier = 'position';
        $this->_orderBy = 'position';

        $this->fields_list = [
            'id_bb_registration_fields' => [
                'title' => '#',
                'width' => 25,
            ],
            'field_name' => [
                'title' => $this->l('Field Name'),
                'width' => 'auto',
                'color' => 'red',
            ],
            'field_type' => [
                'title' => $this->l('Field Type'),
                'width' => 'auto',
            ],
            'id_b2b_profile' => [
                'title' => $this->l('Profile'),
                'width' => 'auto',
                'align' => 'center',
                'callback' => 'getProfileName',
            ],
            'active' => [
                'title' => $this->l('Enabled'),
                'width' => 70,
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
                'orderby' => false,
            ],
            'value_required' => [
                'title' => $this->l('Required'),
                'width' => 70,
                'active' => 'value_required',
                'type' => 'bool',
                'align' => 'center',
                'orderby' => false,
            ],
            'position' => [
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'position' => 'position',
            ],
        ];
    }

    public function getProfileName($id_b2b_profile)
    {
        if (isset($id_b2b_profile) && Validate::isLoadedObject($profile = new BBProfile((int) $id_b2b_profile, $this->context->employee->id_lang))) {
            return $profile->b2b_profile_name;
        }

        return $this->l('Default');
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->context->controller->addJqueryUI('ui.sortable');
    }

    public function renderList()
    {
        $b2b_group = new Group((int) Configuration::get(
            'B2BREGISTRATION_GROUPS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        // Adds an Edit button for each result
        $this->addRowAction('edit');

        // Adds a Delete button for each result
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['save']);
        unset($this->toolbar_btn['cancel']);
    }

    public function initPageHeaderToolbar()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
            if (empty($this->display)) {
                $this->page_header_toolbar_btn['new_customer'] = [
                    'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                    'desc' => $this->l('Add new Field'),
                    'icon' => 'process-icon-new',
                ];
            }
            parent::initPageHeaderToolbar();
        }
    }

    public function renderForm()
    {
        $fielder = new BToBCustomFields();
        $current_object = $this->loadObject(true);
        $back = Tools::safeOutput(Tools::getValue('back', ''));
        if (empty($back)) {
            $back = self::$currentIndex . '&token=' . $this->token;
        }

        $this->fields_form['submit'] = [
            'title' => $this->l(' Save '),
            'class' => 'button',
        ];

        $this->context->smarty->assign('mode', $this->display);
        $customFieldTypes = $this->getCustomFieldTypes();

        $shops = '';
        $selected_shops = '';
        if (Shop::isFeatureActive()) {
            $shops = $this->renderShops($current_object);
            $selected_shops = ($current_object && $current_object->assoc_shops) ? $current_object->assoc_shops : '';
        }
        $this->context->smarty->assign(['shops' => $shops, 'selected_shops' => $selected_shops]);
        $field_values = [];
        $id = (int) Tools::getValue('id_bb_registration_fields');
        $languages = Language::getLanguages();
        $list_options = (!empty($id)) ? $fielder->getOptionsById($id) : [];
        $ps_17 = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
        $fields_collection = $fielder->getFieldsCollection();
        $headings_collection = BBProfile::getAllProfiles();

        if ($id > 0 && !empty($fields_collection)) {
            foreach ($fields_collection as &$field) {
                $field['dep_check'] = $fielder->getDependantCheckedVal($id);
            }
        }

        if ($id > 0) {
            if ('boolean' == BToBCustomFields::getFieldType($current_object->dependant_field)) {
                $field_values = $this->getBooleanEquivalentVal($current_object->dependant_field);
            } else {
                $field_values = $fielder->getCustomFieldsOptions($current_object->dependant_field);
            }
            if (!empty($field_values)) {
                foreach ($field_values as &$field_val) {
                    $field_val['check'] = $fielder->getDependantCheckedValue($id);
                }
            }
        }

        $this->context->smarty->assign([
            'action_url' => self::$currentIndex . '&token=' . $this->token,
            'show_toolbar' => true,
            'toolbar_btn' => $this->toolbar_btn,
            'toolbar_scroll' => $this->toolbar_scroll,
            'title' => [$this->l('Custom Registration Fields')],
            'defaultCurrency' => Configuration::get('PS_CURRENCY_DEFAULT'),
            'id_lang_default' => Configuration::get('PS_LANG_DEFAULT'),
            'languages' => $languages,
            'currentToken' => $this->token,
            'currentIndex' => self::$currentIndex,
            'currentObject' => $current_object,
            'currentTab' => $this,
            'id_bb_registration_fields' => (int) $id,
            '$id_lang_default' => $this->context->language->id,
            'customFieldTypes' => $customFieldTypes,
            'list_options' => $list_options,
            'version' => _PS_VERSION_,
            'ps_17' => $ps_17,
            'fields_collection' => $fields_collection,
            'field_values' => $field_values,
            'headings_collection' => $headings_collection,
        ]);

        return parent::renderForm();
    }

    protected function beforeAdd($object)
    {
        if (empty($object->position) || !BToBCustomFields::positionOccupied($object->position)) {
            $object->position = BToBCustomFields::getHigherPosition() + 1;
        }
        parent::beforeAdd($object);
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAdd' . $this->table) || Tools::isSubmit('submitAdd' . $this->table . 'AndStay')) {
            if (Shop::isFeatureActive()) {
                $assoc_shops = Tools::getValue('checkBoxShopAsso_' . $this->table);
                if (isset($assoc_shops) && is_array($assoc_shops)) {
                    $assoc_shops = implode(',', $assoc_shops);
                    $_POST['assoc_shops'] = $assoc_shops;
                }
            } else {
                $_POST['assoc_shops'] = (int) $this->context->shop->id;
            }
            if (!in_array(Tools::getValue('field_type'), ['text', 'textarea'])) {
                $_POST['field_validation'] = '';
            }
        } elseif (Tools::isSubmit('delete' . $this->table)) {
            $id_bb_registration_fields = (int) Tools::getValue('id_bb_registration_fields');
            BToBCustomFields::removeOptionById($id_bb_registration_fields);
        } elseif (Tools::isSubmit('submitBulkdelete' . $this->table)) {
            $options = Tools::getValue('bb_registration_fieldsBox');
            if (isset($options) && $options) {
                foreach ($options as $id_bb_registration_fields) {
                    BToBCustomFields::removeOptionById($id_bb_registration_fields);
                }
            }
        } elseif (Tools::isSubmit('editable' . $this->table)) {
            $id_bb_registration_fields = (int) Tools::getValue('id_bb_registration_fields');
            if (!BToBCustomFields::updateStatus('editable', $id_bb_registration_fields)) {
                $this->errors[] = $this->l('Editable permissions update error.');
            } else {
                $this->confirmations[] = $this->l('Editable permissions updated successfully');
            }
        } elseif (Tools::isSubmit('value_required' . $this->table)) {
            $id_bb_registration_fields = (int) Tools::getValue('id_bb_registration_fields');
            if (!BToBCustomFields::updateStatus('value_required', $id_bb_registration_fields)) {
                $this->errors[] = $this->l('Requried status update error.');
            } else {
                $this->confirmations[] = $this->l('Required status updated successfully');
            }
        }
        parent::postProcess();
        if (Tools::isSubmit('submitAdd' . $this->table) || Tools::isSubmit('submitAdd' . $this->table . 'AndStay')) {
            $obj = $this->loadObject(true);

            // rearranging option values to match db
            $sorted_options = [];
            $languages = Language::getLanguages(false);
            foreach ($languages as $lang) {
                if (Tools::getValue('options_' . $lang['id_lang'])) {
                    foreach (Tools::getValue('options_' . $lang['id_lang']) as $k => $option) {
                        $sorted_options[$k][$lang['id_lang']]['id_lang'] = $lang['id_lang'];
                        $sorted_options[$k][$lang['id_lang']]['value'] = $option;
                    }
                }
            }

            // inserting option values
            if (isset($sorted_options) && $sorted_options && $obj->id) {
                BToBCustomFields::removeOptionById($obj->id);
                foreach ($sorted_options as $options) {
                    if ($id_option = BToBCustomFields::addOption($obj->id)) {
                        foreach ($options as $option) {
                            BToBCustomFields::addOptionValue($id_option, $option['id_lang'], $option['value']);
                        }
                    }
                }
            }
            $field_type = (Tools::getValue('field_type') ? Tools::getValue('field_type') : $obj->field_type);
            if (!in_array($field_type, $this->option_fields)) {
                BToBCustomFields::removeOptionById($obj->id);
            }
        }
    }

    public function renderShops($object)
    {
        $this->fields_form = [
            'form' => [
                'id_form' => 'field_shops',
                'input' => [
                    [
                        'type' => 'shop',
                        'label' => $this->l('Shop association:'),
                        'name' => 'checkBoxShopAsso',
                    ],
                ],
            ],
        ];
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get(
            'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
        ) ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->id = (int) $object->id;
        $helper->identifier = $this->identifier;
        $helper->tpl_vars = array_merge([
            'languages' => $this->getLanguages(),
            'id_language' => $this->context->language->id,
        ]);

        return $helper->renderAssoShop();
    }

    public function init()
    {
        parent::init();
        Shop::addTableAssociation($this->table, ['type' => 'shop']);
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'bb_registration_fields_shop` sa
                ON (a.`id_bb_registration_fields` = sa.`id_bb_registration_fields`
                AND sa.id_shop = ' . (int) $this->context->shop->id . ') ';
        }
        if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
            $this->_where = ' AND sa.`id_shop` = ' . (int) Context::getContext()->shop->id;
        }

        $ajax = (int) Tools::getValue('ajax');
        $field = (int) Tools::getValue('id_dep');
        $languages = Language::getLanguages(false);
        if (!$ajax && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $dependant = (int) Tools::getValue('dependant');
            $dependant_field = (int) Tools::getValue('dependant_field');
            $dependant_field_value = (int) Tools::getValue('dependant_value');
            if (!Tools::getValue('field_name_' . Configuration::get('PS_LANG_DEFAULT'))) {
                $this->errors[] = $this->l('Field name in default language must be filled.');
            }

            if (in_array(Tools::getValue('field_type'), $this->option_fields)) {
                if ($options = Tools::getValue('options_' . Configuration::get('PS_LANG_DEFAULT'))) {
                    foreach ($options as $option) {
                        if (empty($option)) {
                            $this->errors[] = $this->l('Field option(s) in default language must be filled.');
                        } elseif (!Validate::isGenericName($option)) {
                            $this->errors[] = $this->l('Field option(s) has invalid value');
                        }
                    }
                }

                foreach ($languages as $lang) {
                    if (Tools::getValue('options_' . $lang['id_lang'])) {
                        foreach (Tools::getValue('options_' . $lang['id_lang']) as $option) {
                            if (!Validate::isGenericName($option)) {
                                $this->errors[] = $this->l('Field option(s) has invalid value');
                            }
                        }
                    }
                }
            }
            if ($dependant > 0 && $dependant_field <= 0) {
                $this->errors[] = $this->l('Please choose dependant field OR turn off dependant option.');
            } elseif ($dependant > 0 && $dependant_field > 0) {
                $object = new BToBCustomFields((int) $dependant_field);
                $vals = $object->getCustomFieldsOptions($dependant_field);
                $vals = (int) count($vals);
                if ($dependant_field_value <= 0 && $vals > 0) {
                    $this->errors[] = $this->l('Please choose dependant field value OR turn off dependant option.');
                }
            }
        } elseif ($ajax > 0 && $field > 0) {
            $this->getAjaxFieldVals($field, Configuration::get('B2BREGISTRATION_TOKEN'));
        }
    }

    protected function getCustomFieldTypes()
    {
        return [
            'text' => $this->l('Text Field'),
            'textarea' => $this->l('Text Area'),
            'date' => $this->l('Date'),
            'boolean' => $this->l('Yes/No'),
            'multiselect' => $this->l('Multiple Select'),
            'select' => $this->l('Dropdown Select'),
            'checkbox' => $this->l('Checkbox(s)'),
            'radio' => $this->l('Radio Button(s)'),
            // 'image' => $this->l('Image'),
            // 'attachment' => $this->l('Attachment'),
            'message' => $this->l('Message (Display Only)'),
        ];
    }

    public function ajaxProcessUpdatePositions()
    {
        $way = (int) Tools::getValue('way');
        $id_field = (int) Tools::getValue('id');
        $positions = Tools::getValue('bb_registration_fields');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] === $id_field) {
                if ($field = new BToBCustomFields((int) $pos[2])) {
                    if (isset($position) && $field->updatePosition($way, $position)) {
                        echo 'ok position ' . (int) $position . ' for field ' . (int) $pos[1] . '\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update field ' .
                        (int) $id_field . ' to position ' . (int) $position . ' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This field (' . (int) $id_field . ') can t be loaded"}';
                }
                break;
            }
        }
    }

    public function processPosition()
    {
        if (Tools::getIsset('update' . $this->table)) {
            $object = new BToBCustomFields((int) Tools::getValue('id_bb_registration_fields'));
            self::$currentIndex = self::$currentIndex . '&update' . $this->table;
        } else {
            $object = new BToBCustomFields((int) Tools::getValue('id'));
        }
        if (!Validate::isLoadedObject($object)) {
            $this->errors[] = $this->l('An error occurred while updating the status for an object.') .
            ' <b>' . $this->table . '</b> ' . $this->l('(cannot load object)');
        } elseif (!$object->updatePosition((int) Tools::getValue('way'), (int) Tools::getValue('position'))) {
            $this->errors[] = $this->l('Failed to update the position.');
        } else {
            $id_identifier_str = ($id_identifier = (int) Tools::getValue(
                $this->identifier
            )) ? '&' . $this->identifier . '=' . $id_identifier : '';
            $redirect = self::$currentIndex . '&' . $this->table .
            'Orderby=position&' . $this->table . 'Orderway=asc&conf=5' .
            $id_identifier_str . '&token=' . $this->token;
            $this->redirect_after = $redirect;
        }

        return $object;
    }

    public function getAjaxFieldVals($field, $token)
    {
        if ($token == Configuration::get('B2BREGISTRATION_TOKEN')) {
            $return = ['hasError' => false, 'exist' => false, 'vals' => ''];
            $object = new BToBCustomFields((int) $field);
            $vals = [];
            if ('boolean' == $object->field_type) {
                $vals = $this->getBooleanEquivalentVal($object->id);
            } else {
                $vals = $object->getCustomFieldsValues($field);
            }

            $return['vals'] = $vals;
            $return['exist'] = (int) count($return['vals']);
            exit(json_encode($return));
        }
    }

    protected function getBooleanEquivalentVal($id_field)
    {
        return [
            [
                'id_bb_registration_fields' => $id_field,
                'field_value_id' => 1,
                'field_value' => $this->l('Yes'),
            ],
            [
                'id_bb_registration_fields' => $id_field,
                'field_value_id' => 0,
                'field_value' => $this->l('No'),
            ],
        ];
    }
}

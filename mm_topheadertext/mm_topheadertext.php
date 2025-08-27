<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */


if (!defined('_PS_VERSION_')) {
    exit;
}

class mm_topheadertext extends Module
{
    public function __construct()
    {
        $this->name = 'mm_topheadertext';
        $this->tab = 'front_office_features';
        $this->version = '1.2.1';
        $this->author = 'Major Media';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => _PS_VERSION_];
        parent::__construct();

        $this->displayName = $this->l('MM Top Banner');
        $this->description = $this->l('Adds a configurable Top Header.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        return parent::install()
            && $this->createTabs()
            && $this->registerHook('displayBanner')
            && Configuration::updateValue('HEADER_TEXT', 'Default Top Header Text')
            && Configuration::updateValue('HEADER_TEXT_ACTIVE', 1);
    }

    public function uninstall()
    {
        $this->removeTabs('AdminMmTopHeader');
        $this->removeTabs('AdminMmTopHeaderConfig');
        return parent::uninstall()
            && Configuration::deleteByName('HEADER_TEXT')
            && Configuration::deleteByName('HEADER_TEXT_ACTIVE');
    }

    public function hookDisplayBanner($params)
    {
        if (!Configuration::get('HEADER_TEXT_ACTIVE')) {
            return;
        }

        $this->context->smarty->assign([
            'header' => Configuration::get('HEADER_TEXT'),
        ]);

        return $this->display(__FILE__, 'views/templates/front/topheader.tpl');
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit' . $this->name)) {
            $header_text = Tools::getValue('HEADER_TEXT', '');
            Configuration::updateValue('HEADER_TEXT', $header_text);

            $header_text_active = Tools::getValue('HEADER_TEXT_ACTIVE', 0);
            Configuration::updateValue('HEADER_TEXT_ACTIVE', $header_text_active);

            $output .= $this->displayConfirmation($this->l('Settings updated.'));
        }

        return $output . $this->renderForm();
    }

    public function renderForm()
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = false;
        $helper->submit_action = 'submit' . $this->name;

        $helper->fields_value = [
            'HEADER_TEXT' => Configuration::get('HEADER_TEXT'),
            'HEADER_TEXT_ACTIVE' => Configuration::get('HEADER_TEXT_ACTIVE'),
        ];

        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Top Header Text Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Top Header Text'),
                        'name' => 'HEADER_TEXT',
                        'required' => true,
                        'desc' => $this->l('Enter the text for the top header.'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable Top Header Text'),
                        'name' => 'HEADER_TEXT_ACTIVE',
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
                        'desc' => $this->l('Enable or disable the header text display.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];

        return $helper->generateForm([$form]);
    }

    //Tab Section
    public function createTabs()
    {
        // Top-Header SubTab
        $tab = new Tab();
        $tab->name = [];
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('MM Top Banner');
        }
        $tab->class_name = 'AdminMmTopHeaderConfig';
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminMM');
        $tab->module = $this->name;
        $tab->add();
        return true;
    }

    public function removeTabs($class_name)
    {
        if ($tab_id = Tab::getIdFromClassName($class_name)) {
            $tab = new Tab($tab_id);
            $tab->delete();
        }
    }

}

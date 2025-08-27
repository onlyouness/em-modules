<?php

use PSpell\Config;

if (! defined('_PS_VERSION_')) {
    exit;
}

class AddressIpCheck extends Module
{
    public function __construct()
    {
        $this->name                   = 'addressipcheck';
        $this->tab                    = 'front_office_features';
        $this->version                = '1.0.0';
        $this->author                 = 'Youness Elbaz';
        $this->need_instance          = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Address IP Check', [], 'Modules.addressipcheck.Admin');
        $this->description = $this->trans('Allows you to display the mode catalogue .', [], 'Modules.addressipcheck.Admin');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.addressipcheck.Admin');

    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return (
            parent::install() && $this->registerHook('displayTop')
        );
    }
    public function uninstall()
    {
        return (
            parent::uninstall()
        );
    }

    public function hookDisplayTop($params)
    {
        $this->context->smarty->assign([
            'allowIP' => Tools::getValue('MMADDRESSIP', Configuration::get('MMADDRESSIP')),
            'myIPAddress' => Tools::getRemoteAddr(),
        ]);

    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit' . $this->name)) {
            $addressIp = Tools::getValue('MMADDRESSIP');
            Configuration::updateValue('MMADDRESSIP', pSQL($addressIp));
        }

        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Address IP Check'),
                    'icon'  => 'icon-cogs',
                ],
                'input'  => [
                    [
                        'type'     => 'text',
                        'label'    => $this->l('Enter Address IP'),
                        'name'     => 'MMADDRESSIP',
                        'required' => false,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper                  = new HelperForm();
        $helper->table           = $this->table;
        $helper->name_controller = $this->name;

        $helper->token         = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex  = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        $helper->fields_value['MMADDRESSIP'] = Tools::getValue('MMADDRESSIP', Configuration::get('MMADDRESSIP'));
        return $helper->generateForm([$form]);
    }
}

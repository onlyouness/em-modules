<?php

if (! defined('_PS_VERSION_')) {
    exit;
}

class SelectBrands extends Module
{
    public function __construct()
    {
        $this->name          = 'selectbrands';
        $this->tab           = 'administration';
        $this->version       = '1.0.0';
        $this->author        = 'Youness Major Media';
        $this->need_instance = 0;
        $this->bootstrap     = true;
        parent::__construct();
        $this->displayName = $this->l('Select Brands');
        $this->description = $this->l('Allows you to choose brands you want to display');
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayHome') && $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }
    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/productselect.css');
    }
    public function hookDisplayHome()
    {
        $brands      = Configuration::get('SELECT_BRANDS');
        $title       = Configuration::get('TITLE_SEECTION_BRANDS');
        $description = Configuration::get('DESCRIPTION_SEECTION_BRANDS');

        $brandsSerialized = json_decode($brands, true);
        $brands           = [];
        if (! empty($brandsSerialized)) {
            foreach ($brandsSerialized as $brand) {
                $brands[] = new Manufacturer($brand['id_manufacturer']);
            }
        }
        $variables = [
            'brands'      => $brands,
            'title'       => $title,
            'description' => $description,
            'link'        => $this->context->link,
        ];
        $this->smarty->assign($variables);
        return $this->display(__FILE__, 'views/templates/hook/displayHome.tpl');
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit' . $this->name)) {

            $brands      = Tools::getValue('SELECT_BRANDS');
            $title       = Tools::getValue('TITLE_SEECTION_BRANDS');
            $description = Tools::getValue('DESCRIPTION_SEECTION_BRANDS');

            $serializedBrands = json_encode($brands);

            if (empty($brands) && empty($title) && empty($description)) {
                $output = $this->displayError($this->l('Invalid Details'));
            } else {
                Configuration::updateValue('SELECT_BRANDS', $serializedBrands);
                Configuration::updateValue('TITLE_SEECTION_BRANDS', $title);
                Configuration::updateValue('DESCRIPTION_SEECTION_BRANDS', $description);
            }

            if (empty($output)) {
                $output = $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        $lang   = $this->context->language->id;
        $brands = Manufacturer::getManufacturers(false, $lang);

        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Brand Selection'),
                ],
                'input'  => [
                    [
                        'type'     => 'text',
                        'label'    => $this->l('Enter the title Of the section'),
                        'name'     => 'TITLE_SECTION_BRANDS',
                        'required' => false,
                    ],
                    [
                        'type'     => 'text',
                        'label'    => $this->l('Enter the Description Of the section'),
                        'name'     => 'DESCRIPTION_SEECTION_BRANDS',
                        'required' => false,
                    ],
                    [
                        'type'     => 'select',
                        'label'    => $this->l('Choose brands'),
                        'name'     => 'SELECT_BRANDS[]',
                        'multiple' => true,
                        'options'  => [
                            'query' => $brands,
                            'id'    => 'id_manufacturer',
                            'name'  => 'name',
                        ],
                        'required' => true,
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

        $selectedProducts                                    = json_decode(Configuration::get('SELECT_BRANDS'), true);
        $helper->fields_value['SELECT_BRANDS[]']             = $selectedProducts ?: [];
        $helper->fields_value['TITLE_SEECTION_BRANDS']       = Tools::getValue('TITLE_SEECTION_BRANDS', Configuration::get('TITLE_SEECTION_BRANDS'));
        $helper->fields_value['DESCRIPTION_SEECTION_BRANDS'] = Tools::getValue('DESCRIPTION_SEECTION_BRANDS', Configuration::get('DESCRIPTION_SEECTION_BRANDS'));

        return $helper->generateForm([$form]);
    }

}

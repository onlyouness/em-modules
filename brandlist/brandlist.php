<?php

if (! defined('_PS_VERSION_')) {
    exit;
}

class BrandList extends Module
{
    public function __construct()
    {
        $this->name          = 'brandlist';
        $this->tab           = 'administration';
        $this->version       = '1.0.0';
        $this->author        = 'Youness Major Media';
        $this->need_instance = 0;
        $this->bootstrap     = true;
        parent::__construct();
        $this->displayName = $this->l('Brand List');
        $this->description = $this->l('Allows you to show your brand list on the manufacturer page');
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }
    public function hookDisplayHome()
    {
        $id_lang      = $this->context->language->id;
        $brands       = [];
        $allBrans     = Manufacturer::getManufacturers(false, $id_lang);
        $currentBrand = Tools::getValue('id_manufacturer');
        foreach ($allBrans as $brand) {
            $brands[] = new Manufacturer($brand['id_manufacturer']);
        }
        $variables = [
            'link'         => Context::getContext(),
            'currentBrand' => $currentBrand,
            'brands'       => $brands,
        ];
        Tools::dieObject($variables);
        $this->smarty->assign($variables);
        return $this->display(__FILE__, 'views/templates/hook/displayHome.tpl');
    }
}

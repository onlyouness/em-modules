<?php

use Hp\Filterdproducts\FilteredProducts;

if (! defined('_PS_VERSION_')) {
    exit;
}

class TaefooFilteredProducts extends Module
{
    public function __construct()
    {
        $this->name          = 'taefoofilteredproducts';
        $this->tab           = 'administration';
        $this->version       = '1.0.0';
        $this->author        = 'Youness Major Media';
        $this->need_instance = 0;
        $this->bootstrap     = true;
        parent::__construct();
        $this->displayName = $this->l('Taefoo Filtered Products');
        $this->description = $this->l('Allows you get Filtered products and do the taefoo logic');
    }
    public function install()
    {
        return parent::install() && $this->registerHook('displayBeforeFooter');
    }
    public function uninstall()
    {
        return parent::uninstall();
    }
    public function hookDisplayBeforeFooter($params)
    {
        $ids = FilteredProducts::getFilteredIds();
        Tools::dieObject($ids);
        $controller = $this->context->controller;
        if (in_array($controller->php_self, ['authentication', 'password'])) {
            return ''; 
        }

        if ($this->context->customer->isLogged()) {
            return '';
        }

        $this->smarty->assign([
            'language' => $this->context->language,
            'link'     => $this->context->link,
            'customer' => [
                'is_logged' => false,
            ],
            'page'     => [
                'page_name' => $controller->php_self,
            ],
        ]);

        return $this->display(__FILE__, 'views/templates/hook/formpopup.tpl');
    }
    public function hookDisplayPopupLogin()
    {
        $this->smarty->assign([]);
        return $this->display(__FILE__, 'views/templates/hook/button.tpl');
    }
}

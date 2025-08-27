<?php

if (! defined('_PS_VERSION_')) {
    exit;
}

class SecondHomeBloc extends Module
{
    public function __construct()
    {
        $this->name          = 'secondhomebloc';
        $this->tab           = 'administration';
        $this->version       = '1.0.0';
        $this->author        = 'Youness Major Media';
        $this->need_instance = 0;
        $this->bootstrap     = true;
        parent::__construct();
        $this->displayName = $this->l('Second Home Bloc');
        $this->description = $this->l('Allows you to create a home bloc');
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

        $images = [
            _PS_BASE_URL_ . '/modules/' . $this->name . '/img/Groupe 489.png',
            _PS_BASE_URL_ . '/modules/' . $this->name . '/img/ITA.png',
            _PS_BASE_URL_ . '/modules/' . $this->name . '/img/Image 157.png',
        ];
        $variables = [
            'link'      => Context::getContext()->link,
            'secondHomeImages' => $images,
        ];
        // Tools::dieObject($holdImage);
        $this->smarty->assign($variables);
        return $this->display(__FILE__, 'views/templates/hook/displayHome.tpl');
    }
}

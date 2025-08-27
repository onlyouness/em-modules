<?php

class EnseignesContact extends Module
{
    public function __construct()
    {
        $this->name          = 'enseignescontact';
        $this->tab           = 'administration';
        $this->version       = '1.0.0';
        $this->author        = 'Youness MM';
        $this->bootstrap     = true;
        $this->need_instance = 0;
        parent::__construct();
        $this->displayName            = $this->l('Enseignes Contact', 'enseignescontact');
        $this->description            = $this->l('Module to edit and manage the enseigne inforamtion.');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }
    public function install()
    {
        return parent::install() && $this->registerHook('displayHome');
    }
    public function uninstall()
    {
        return parent::uninstall();
    }
    public function displayHome($params)
    {
        $isoLang = \Tools::strtoupper(\Context::getContext()->language->iso_code);
        $informations = Configuration::get('' . $isoLang);
        
        $this->context->smarty->assign([
            'checkout_information' => $informations,
        ]);
        return $this->display(__FILE__, 'views/templates/hook/checkoutinformation.tpl');
    }
    // public function hookDisplayCheckoutSummaryTop($params)
    // {
    //     $isoLang = \Tools::strtoupper(\Context::getContext()->language->iso_code);
    //     $informations = Configuration::get('CHECKOUT_INFORMATION_' . $isoLang);
        
    //     $this->context->smarty->assign([
    //         'checkout_information' => $informations,
    //     ]);
    //     return $this->display(__FILE__, 'views/templates/hook/checkoutinformation.tpl');
    // }
    public function hookDisplayBackOfficeHeader()
    {
        $controller = Context::getContext()->controller;
        $jsPath     = $this->_path . 'js/admin.js';
        if (file_exists(_PS_MODULE_DIR_ . 'checkoutinformation/js/admin.js')) {
            $controller->addJS($jsPath);
        }
    }
    public function getContent()
    {
        $homePage = Link::getUrlSmarty(['entity' => 'sf', 'route' => 'mm_ensigne_contact_index']);
        Tools::redirectAdmin($homePage);
    }
}
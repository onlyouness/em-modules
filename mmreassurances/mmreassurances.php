<?php
declare (strict_types = 1);

use Hp\Mmreassurances\Entity\Reassurance;
use Hp\Mmreassurances\Install\Installer;

class MmReassurances extends Module
{
    public function __construct()
    {
        $this->name          = 'mmreassurances';
        $this->tab           = 'administration';
        $this->version       = '1.0.0';
        $this->author        = 'Youness Elbaz MM';
        $this->need_instance = 0;
        $this->bootstrap     = true;

        parent::__construct();

        $this->displayName = $this->l('MM Labels Reassurances');
        $this->description = $this->l('Allows user to add a dynamic Labels.');

        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }
    public function install()
    {
        if (! parent::install()) {
            return false;
        }
        $installer = new Installer();
        return $installer->install($this);
    }
    public function uninstall()
    {
        $installer = new Installer();
        return $installer->uninstall() && parent::uninstall();
    }
    public function hookDisplayHome()
    {
        $em           = $this->get('doctrine.orm.entity_manager');
        $langId       = (int) $this->context->language->id;
        $reassurances = $em->getRepository(Reassurance::class)->findReassuranceByLangAndActive($langId);

        $this->context->smarty->assign(['reassurances' => (array) $reassurances]);
        return $this->display(__FILE__, 'views/templates/hook/home.tpl');
    }
    public function hookDisplayHeader()
    {
        $this->context->controller->addJS("{$this->_path}/views/js/admin.js");
        $this->context->controller->addCSS("{$this->_path}/views/css/admin.css");
    }
}

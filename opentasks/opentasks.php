<?php
declare(strict_types = 1);
if(!defined('_PS_VERSION_')){
    exit;
}
use Hp\Opentasks\Installer\Installer;

require_once __DIR__.'/vendor/autoload.php';

class OpenTasks extends  Module{
    public function __construct()
    {
        $this->name = 'opentasks';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'youness';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Open Tasks');
        $this->description = $this->l('Allows user to add manage their tasks.');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        if(!parent::install()){
            return;
        }
        $installer = new Installer();
        return $installer->install($this);
    }
    public function uninstall()
    {
        $installer = new Installer();
        return  $installer->uninstall() && parent::uninstall();
    }

    public function getContent(){
        return "Hello world!";
    }
    public function hookModuleRoutes (){

    }
}
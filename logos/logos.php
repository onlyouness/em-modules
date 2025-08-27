<?php

class Logos extends Module {
     public function __construct()
    {
        $this->name    = 'logos';
        $this->tab     = 'front_office_features';
        $this->version = '1.0.0';
        $this->author  = 'Youness Major media';
        parent::__construct();
        $this->need_instance          = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];
        $this->bootstrap              = true;
        $this->displayName            = $this->l('Logos', 'Groupbanner');
        $this->description            = $this->l('Allows you to display some logos');
    }

    public function install()
    {
        return parent::install() && $this->installDb() &&
        $this->registerHook('displayHome');
    }

    public function uninstall()
    {

        return $this->uninstallDb() && parent::uninstall();
    }
    public function installDb()
    {
        $queries   = [];
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'logos` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `image` INT ,
            `title` varchar(255) NULL ,
            `link` varchar(255) Null,
            `image` varchar(255) NOT NULL,
            `link` varchar(255),
            `active` INT default 1
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';
        foreach ($queries as $query) {
            if (! Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }
    public function uninstallDb()
    {
        $queries   = [];
        $queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'logos`';
        foreach ($queries as $query) {
            if (! Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }
    public function hookDisplayHome(){

    }
}
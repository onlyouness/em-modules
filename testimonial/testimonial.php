<?php

declare(strict_types=1);

use Hp\Testimonial\Install\Installer;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Testimonial extends Module
{
    public function __construct()
    {
        $this->name = "testimonial";
        $this->tab = "front_office_features";
        $this->version = '1.0.0';
        $this->author = 'Youness Elbaz';
        parent::__construct();
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;
        $this->displayName = $this->l('Testimonial', 'testimonial');
        $this->description = $this->l('Allows you to manage your testimonial.');
    }

    public function install()
    {
        $install = new Installer;
        if (!parent::install()) {
            return;
        }
        return  $install->install($this);
    }
    public function uninstall()
    {
        $install = new Installer;
        return parent::uninstall() && $install->uninstall();
    }
    public function hookDisplayHome($params) {}
}

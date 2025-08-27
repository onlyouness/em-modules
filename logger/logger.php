<?php

use Hp\Logger\Services\LoggerService;

class Logger extends Module
{
    public function __construct()
    {
        $this->name    = 'logger';
        $this->tab     = 'front_office_features';
        $this->version = '1.0.0';
        $this->author  = 'Youness Major media';
        parent::__construct();
        $this->need_instance          = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];
        $this->bootstrap              = true;
        $this->displayName            = $this->l('Logger', 'Logger');
        $this->description            = $this->l('Allows you to log all actions');
    }

    public function install()
    {

        return parent::install() && $this->registerHook('displayHome');
    }
    public function uninstall()
    {
        return parent::uninstall();
    }
    public function hookDisplayHome($params)
    {
        $loggerService = new LoggerService();
        $loggerService->logError('This has an Error.');

        return '';
    }

}

<?php

declare(strict_types=1);

use Hp\Services\Entity\Service;
use Hp\Services\Entity\ServiceLang;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Services extends Module
{

    public function __construct()
    {
        $this->name = 'services';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Major media';
        parent::__construct();
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;
        $this->displayName = $this->l('MM Services', 'services');
        $this->description = $this->l('Allows you to manage your services.');
    }
    public function install()
    {
        return parent::install() && $this->installDb() && $this->installTab() && $this->registerHook('displayBackOfficeHeader') && $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        return $this->uninstallDb() && $this->uninstallTab() && parent::uninstall();
    }
    public function installDb()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'services` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `image` VARCHAR(255) NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `description` LONGTEXT NOT NULL,
            `active` boolean DEFAULT 0,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';
        $sql1 = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'section_services` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `short_description` VARCHAR(255) NOT NULL,
            `description` LONGTEXT NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';

        return Db::getInstance()->execute($sql) && Db::getInstance()->execute($sql1) ;
    }

    public function uninstallDb()
    {
        $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'services`';
        $sql1 = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'section_services`';
        return Db::getInstance()->execute($sql) && Db::getInstance()->execute($sql1) ;
    }
    public function installTab()
    {
        $languages = Language::getLanguages();
        // The Global services parent
        $parentTabClassName = 'AdminMM';

        $tabClassName = 'ServiceController';
        if (!Tab::getIdFromClassName($tabClassName)) {
            $tab = new Tab();
            $tab->active = true;
            $tab->enabled = true;
            $tab->module = $this->name;
            $tab->class_name = $tabClassName;
            $tab->id_parent = Tab::getIdFromClassName($parentTabClassName);
            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = 'MM Services';
            }
            $tab->icon = '';
            $tab->wording = 'Services Manager';
            $tab->wording_domain = 'Modules.Services.Admin';
            try {
                $tab->save();
                return true;
            } catch (Exception $e) {
                PrestaShopLogger::addLog('Error creating tab: ' . $e->getMessage(), 3);
                return false;
            }
        }
        return true;
    }
    public function getContent(){
        $router =\PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance()->get('router');
        $url =  $router->generate('oil_service_index');
        Tools::redirect($url);
        return '';

    }

    public function hookDisplayHome()
    {
        $langId = $this->context->language->id;
        $em = $this->get('doctrine.orm.entity_manager');
        $services = $em->getRepository(Service::class)->findBy(['active' => 1]);
        
        $serviceData = [];


        foreach ($services as $service) {
                $serviceData[] = [
                    'id' => $service->getId(),
                    'active' => $service->getActive(),
                    'image' => $service->getImage(), // Assuming you have an image field in the Service entity
                    'title' => $service->getTitle(), // Get the translated title
                    'description' => $service->getDescription(), // Get the translated description
                ];
        }
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'section_services`';
        $section = Db::getInstance()->getRow($sql);
        $this->context->smarty->assign(['services' => $serviceData, 'section' => $section]);

        return $this->display(__FILE__, 'views/templates/hook/home.tpl');
    }
    public function uninstallTab()
    {
        $id = (int)Tab::getIdFromClassName('ServiceController');
        if ($id) {
            $tab = new Tab($id);
            try {
                $tab->delete();
                return true;
            } catch (Exception $e) {
                PrestaShopLogger::addLog('Error creating tab: ' . $e->getMessage(), 3);
                return false;
            }
        } else {
            return true;
        }
    }
    public function hookDisplayBackOfficeHeader() {}
}

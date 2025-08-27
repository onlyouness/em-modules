<?php
if (! defined('_PS_VERSION_')) {
    exit;
}
use Hp\Logger\Services\LoggerService;

class BulkPriceChange extends Module
{
    public function __construct()
    {
        $this->name          = 'bulkpricechange';
        $this->tab           = 'administration';
        $this->version       = '1.0.0';
        $this->author        = 'Youness Major Media';
        $this->need_instance = 0;
        $this->bootstrap     = true;
        parent::__construct();
        $this->displayName = $this->l('Bulk Price Change');
        $this->description = $this->l('allows you to update product prices of a selected category.');
    }

    public function install()
    {
        return parent::install() && $this->registerhook(['displayAdminProductsExtra']); ;
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function installDb()
    {
        $queries = [];
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'faq` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `category_id` INT ,
            `percentage` INT default 1,
            `price` INT ,
            `active` INT default 1
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'faq_lang` (
            `id_faq` INT,
            `id_lang` INT,
            `question` varchar(250) ,
            `response` varchar(250) ,
            primary key(`id_faq`,`id_lang`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';
        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }
    public function uninstallDb()
    {
        $queries = [];
        $queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'faq`';
        $queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'faq_lang`';
        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }

    protected function installTab(): bool
    {
        $languages = Language::getLanguages();
        $parentTabClassName = 'AdminFaqTab'; // Unique class name for parent tab
        if (!Tab::getIdFromClassName($parentTabClassName)) {
            $parentTab = new Tab();
            $parentTab->active = true;
            $parentTab->enabled = true;
            $parentTab->module = $this->name;
            $parentTab->class_name = $parentTabClassName;
            $parentTab->id_parent = 0; // Root tab (no parent)

            foreach ($languages as $language) {
                $parentTab->name[$language['id_lang']] = 'FAQ'; // Display name for parent tab
            }

            $parentTab->icon = 'settings'; // Optional icon for the parent tab
            $parentTab->wording = 'FAQ';
            $parentTab->wording_domain = 'Modules.AdminFaq.Admin';

            try {
                $parentTab->save();
            } catch (Exception $e) {
                PrestaShopLogger::addLog('Error creating parent tab: ' . $e->getMessage(), 3);
                return false;
            }
        } else {
            $parentTab = new Tab(Tab::getIdFromClassName($parentTabClassName));
        }
        $exists = Tab::getIdFromClassName('AdminFaq');
        if (!$exists) {
            $tab = new Tab();
            $tab->active = true;
            $tab->enabled = true;
            $tab->module = $this->name;
            $tab->class_name = 'AdminFaq';
            $tab->id_parent = $parentTab->id;
            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = 'FAQ';
            }
            $tab->icon = '';
            $tab->wording = 'FAQ';
            $tab->wording_domain = 'Modules.AdminFaq.Admin';
            try {
                $tab->save();
            } catch (Exception $e) {
                PrestaShopLogger::addLog('Error tab: ' . $e->getMessage(), 3);
                return false;
            }
        }
        return true;
    }

    protected function unInstallTab(): bool
    {
        $ids = [
            'AdminFaqTab',
            'AdminFaq',
        ];
        foreach ($ids as $i) {
            $id = Tab::getIdFromClassName($i);
            if ($id) {
                $tab = new Tab($id);
                $tab->delete();
            }
        }
        return true;
    }
}
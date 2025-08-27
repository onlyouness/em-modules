<?php

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

if (!defined('_PS_VERSION_')) {

    exit;
}

class Blogs extends Module

{

    public function __construct()

    {

        $this->name = 'blogs';

        $this->tab = 'front_office_features';

        $this->version = '1.0.0';

        $this->author = 'Major media';

        parent::__construct();

        $this->need_instance = 0;

        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];

        $this->bootstrap = true;

        $this->displayName = $this->l('Blogs', 'blogs');

        $this->description = $this->l('Allows you to manage your blogs.');
    }

    public function install()

    {

        return parent::install() && $this->installDb() && $this->installTab() && $this->registerHook('displayBackOfficeHeader') && $this->registerHook('moduleRoutes') && $this->registerHook('displayHome');
    }



    public function uninstall()
    {

        return $this->uninstallDb() && $this->uninstallTab() && parent::uninstall();
    }

    public function installDb()
    {

        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'blogs` (

            `id` INT AUTO_INCREMENT PRIMARY KEY,

            `title` VARCHAR(255) NOT NULL,

            `description` LONGTEXT NOT NULL,

            `short_description` varchar(256) NOT NULL,

            `image` VARCHAR(255) NOT NULL,

            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

            INDEX (`title`) 

        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';

        $sql1 = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'blog_sections` (

            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `blog_id` INT NOT NULL,
            `title` VARCHAR(255) ,
            `products` varchar(255) NOT NULL,
            `position` boolean,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP

        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';



        return Db::getInstance()->execute($sql) && Db::getInstance()->execute($sql1);
    }



    public function uninstallDb()
    {

        $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'blogs`';

        $sql1 = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'blog_sections`';

        return Db::getInstance()->execute($sql) && Db::getInstance()->execute($sql1);
    }

    public function installTab()

    {

        $languages = Language::getLanguages();
        $parentTabClassName = 'AdminBlogs'; // Unique class name for parent tab
        if (!Tab::getIdFromClassName($parentTabClassName)) {
            $parentTab = new Tab();
            $parentTab->active = true;
            $parentTab->enabled = true;
            $parentTab->module = $this->name;
            $parentTab->class_name = $parentTabClassName;
            $parentTab->id_parent = 0; // Root tab (no parent)

            foreach ($languages as $language) {
                $parentTab->name[$language['id_lang']] = 'Blog Manager'; // Display name for parent tab
            }

            $parentTab->icon = 'settings'; // Optional icon for the parent tab
            $parentTab->wording = 'Blog Manager';
            $parentTab->wording_domain = 'Modules.Blogs.Admin';

            try {
                $parentTab->save();
            } catch (Exception $e) {
                PrestaShopLogger::addLog('Error creating parent tab: ' . $e->getMessage(), 3);
                return false;
            }
        } else {
            $parentTab = new Tab(Tab::getIdFromClassName($parentTabClassName));
        }

        $tabClassName = 'BlogController';

        if (!Tab::getIdFromClassName($tabClassName)) {

            $tab = new Tab();

            $tab->active = true;

            $tab->enabled = true;

            $tab->module = $this->name;

            $tab->class_name = $tabClassName;

            $tab->id_parent = (int)$parentTab->id;

            foreach ($languages as $language) {

                $tab->name[$language['id_lang']] = 'Blogs';
            }

            $tab->icon = 'settings.applications';

            $tab->wording = 'Blogs Manager';

            $tab->wording_domain = 'Modules.Blogs.Admin';

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

    public function uninstallTab()

    {
        $ids = [
            'BlogController',
            'AdminBlogs'
        ];

        foreach ($ids as $id) {
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
    }
    // public function hookDisplayHeader($params)
    // {
    //     $this->context->controller->addJS($this->_path . 'views/js/changeplan.js', 'all', true, 9999);
    //     $this->context->controller->addJS($this->_path . 'views/js/add-to-cart.js', 'all', true, 9999);
    //     $this->context->controller->addCSS($this->_path . 'views/css/nosabonnements.css', 'all');
    // }



    public function hookDisplayHome()
    {

        $sql = '
        SELECT 
            b.id,
            b.title ,
            b.short_description AS `shortDescription`,
            b.image
        FROM  `' . _DB_PREFIX_ . 'blogs` b
        
        ORDER BY  b.created_at Asc
    ';

        // Execute the query
        $results = Db::getInstance()->executeS($sql);

        $this->context->smarty->assign(array(
            'blogs' => $results,
            'link' => $this->context->link,
        ));
        return $this->display(__FILE__, 'views/templates/hook/displayHome.tpl');
    }

    public function hookDisplayBackOfficeHeader()
    {
        $controller = Context::getContext()->controller;
        $controller->addJS('/modules/blogs/js/admin.js');
        $this->context->controller->addJS('https://code.jquery.com/ui/1.14.0/jquery-ui.js');
        $this->context->controller->addCSS('https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css');
    }

    public function hookModuleRoutes($params)
    {
        return [
            'module-blogs' => [
                'controller' => 'blog',
                'rule' => 'blogs/{id_blog}',
                'keywords' => [
                    'id_blog' => ['regexp' => '[0-9]+', 'param' => 'id_blog'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'blogs',
                ],
            ],
        ];
    }
}

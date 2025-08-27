<?php

use Hp\Groupbanner\Entity\GroupBanner as EntityGroupBanner;

class Groupbanner extends Module
{

    public function __construct()
    {
        $this->name    = 'groupbanner';
        $this->tab     = 'front_office_features';
        $this->version = '1.0.0';
        $this->author  = 'Youness Major media';
        parent::__construct();
        $this->need_instance          = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];
        $this->bootstrap              = true;
        $this->displayName            = $this->l('Group Banner', 'Groupbanner');
        $this->description            = $this->l('Allows you to create banners for each group');
    }

    public function install()
    {
        return parent::install() && $this->installDb() && $this->installTab() &&
        $this->registerHook('displayHome') && $this->registerHook('displayBackOfficeHeader') && $this->registerHook('displayHeader');
    }

    public function uninstall()
    {

        return $this->uninstallDb() && $this->uninstallTab() && parent::uninstall();
    }
    public function installDb()
    {
        $queries   = [];
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'group_banner` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `group_id` INT ,
            `section_id` INT ,
            `image` varchar(255),
            `link` varchar(255),
            `active` INT default 1
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'group_banner_lang` (
            `id_banner` INT,
            `id_lang` INT,
            `title` varchar(250) ,
            `description` LONGTEXT,
            primary key(`id_banner`,`id_lang`)
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
        $queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'group_banner`';
        $queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'group_banner_lang`';
        foreach ($queries as $query) {
            if (! Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }

    protected function installTab(): bool
    {
        $languages          = Language::getLanguages();
        $parentTabClassName = 'AdminBannerGroup'; // Unique class name for parent tab
        if (! Tab::getIdFromClassName($parentTabClassName)) {
            $parentTab             = new Tab();
            $parentTab->active     = true;
            $parentTab->enabled    = true;
            $parentTab->module     = $this->name;
            $parentTab->class_name = $parentTabClassName;
            $parentTab->id_parent  = 0; // Root tab (no parent)

            foreach ($languages as $language) {
                $parentTab->name[$language['id_lang']] = 'Group Banner'; // Display name for parent tab
            }

            $parentTab->icon           = 'settings'; // Optional icon for the parent tab
            $parentTab->wording        = 'Groupbanner';
            $parentTab->wording_domain = 'Modules.AdminBannerGroup.Admin';

            try {
                $parentTab->save();
            } catch (Exception $e) {
                PrestaShopLogger::addLog('Error creating parent tab: ' . $e->getMessage(), 3);
                return false;
            }
        } else {
            $parentTab = new Tab(Tab::getIdFromClassName($parentTabClassName));
        }
        $exists = Tab::getIdFromClassName('AdminBannerGroupController');
        if (! $exists) {
            $tab             = new Tab();
            $tab->active     = true;
            $tab->enabled    = true;
            $tab->module     = $this->name;
            $tab->class_name = 'AdminBannerGroupController';
            $tab->id_parent  = $parentTab->id;
            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = 'Banner Group';
            }
            $tab->icon           = '';
            $tab->wording        = 'Group banner';
            $tab->wording_domain = 'Modules.AdminBannerGroup.Admin';
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
            'AdminBannerGroup',
            'AdminBannerGroupController',
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
    public function hookDisplayHome($params)
    {
        $em           = $this->get('doctrine.orm.entity_manager'); // Doctrine entity manager
        $groupbanners = [];
        $id_customer  = null;
        $langId       = $this->context->language->id;
        if ($this->context->customer->isLogged()) {
            $id_customer = $this->context->customer->id;
            $sql         = new DbQuery();
            $sql->select('id_group');
            $sql->from('customer_group', 'l');
            $sql->Where('l.id_customer = ' . (int) $id_customer);
            $group        = Db::getInstance()->getValue($sql);
            $groupbanners = $em->getRepository(EntityGroupBanner::class)->findBannerByLangAndActiveAndGroup($langId, $group);
        } else {

            $groupbanners = $em->getRepository(EntityGroupBanner::class)->findBannerByLangAndActiveAndGroup((int) $langId, 3);
        }
        $GroupBannerDatas = [
            1 => [],
            2 => [],
        ];
        foreach ($groupbanners as $groupbanner) {
            $image = $groupbanner['image'];
            $image_url = $image ? $this->context->link->getMediaLink('/modules/'. $this->name . '/img/' . $image) : '';

            $GroupBannerData = [
                'id'          => $groupbanner['id'],
                'active'      => $groupbanner['active'],
                'link'       => $groupbanner['link'],
                'title'       => $groupbanner['title'],
                'image'       =>  $image_url,
                'description' => $groupbanner['description'],
                'section'     => $groupbanner['section'],
            ];
            if ($GroupBannerData['section'] == 1) {
                $GroupBannerDatas[1] = $GroupBannerData;
            } elseif ($GroupBannerData['section'] == 2) {
                $GroupBannerDatas[2] = $GroupBannerData;
            }
        }
        $this->context->smarty->assign([
            'group_banner_1' => $GroupBannerDatas[1],
            'group_banner_2' => $GroupBannerDatas[2],
        ]);

        return $this->display(__FILE__, 'views/templates/hook/home.tpl');
    }

    public function hookDisplayHeader()
    {

        $this->context->controller->addCSS($this->_path . 'views/css/bannergroup.css', 'all');
        $this->context->controller->addJs($this->_path . 'views/js/bannergroup.js', 'all');
    }

    public function hookDisplayBackOfficeHeader()
    {
        $controller = Context::getContext()->controller;
        $jsPath     = $this->_path . 'js/admin.js';
        if (file_exists(_PS_MODULE_DIR_ . 'groupbanner/js/admin.js')) {
            $controller->addJS($jsPath);
        }
    }
    public function getContent()
    {
        $homePage = Link::getUrlSmarty(['entity' => 'sf', 'route' => 'mm_group_banner_index']);
        Tools::redirectAdmin($homePage);
    }
}

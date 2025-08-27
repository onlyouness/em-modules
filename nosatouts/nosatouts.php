<?php

use Hp\Nosatouts\Entity\NosAtout;

if (! defined('_PS_VERSION_')) {
    exit;
}

class NosAtouts extends Module
{
    public function __construct()
    {
        $this->name                   = 'nosatouts';
        $this->tab                    = 'front_office_features';
        $this->version                = '1.0.0';
        $this->author                 = 'Youness Elbaz';
        $this->need_instance          = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName      = $this->trans('Nos Atouts', [], 'Modules.nosatouts.Admin');
        $this->description      = $this->trans('Edit and add and manage', [], 'Modules.nosatouts.Admin');
        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.nosatouts.Admin');
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        return (
            parent::install() && $this->installDb() && $this->registerHook('displayHome')
        );
    }
    public function uninstall()
    {
        return (
            $this->uninstallDb() && parent::uninstall()
        );
    }

    public function installDb()
    {
        $queries   = [];
        $queries[] = '
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'nos_atout`(
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `active` INT DEFAULT 1,
                `image` VARCHAR(255)
            )ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ';
        $queries[] = '
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'nos_atout_lang` (
                `id_atout` INT,
                `id_lang` INT,
                `title` VARCHAR(250),
                `description` LONGTEXT,
                PRIMARY KEY(`id_atout`, `id_lang`)
            )ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ';
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
        $queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'nos_atout`';
        $queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'nos_atout_lang`';
        foreach ($queries as $query) {
            if (! Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }

    public function hookDisplayHome()
    {
        $em          = $this->get('doctrine.orm.entity_manager');
        $context     = \Context::getContext();
        $langId      = (int) $context->language->id;
        $atoutbanner = $em->getRepository(NosAtout::class)->findAtoutByLang($langId);
        $this->context->smarty->assign([
            'atoutbanner' => $atoutbanner,
        ]);
        return $this->display(__FILE__, 'views/templates/hook/home.tpl');
    }

    public function getContent()
    {
        $homePage = Link::getUrlSmarty(['entity' => 'sf', 'route' => 'mm_atout_banner_index']);
        Tools::redirectAdmin($homePage);
    }

}

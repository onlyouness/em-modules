<?php

use  Hp\Cmsaddictional\Form\Modifier\MmCmsPageFormModifier;

if (! defined('_PS_VERSION_')) {
    exit;
}

class CmsAddictional extends Module
{
    public function __construct()
    {
        $this->name          = 'cmsaddictional';
        $this->tab           = 'administration';
        $this->version       = '1.0.0';
        $this->author        = 'Youness Major Media';
        $this->need_instance = 0;
        $this->bootstrap     = true;

        parent::__construct();

        $this->displayName = $this->l('Cms addictional');
        $this->description = $this->l('allows to add a custom field to cms');
    }

    public function install()
    {
        if (! parent::install()
            || ! $this->installDb()
            || ! $this->registerHook(['actionCmsPageFormBuilderModifier', 'actionAfterUpdateCmsPageFormHandler', 'actionAfterCreateCmsPageFormHandler'])
        ) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (! $this->uninstallDb()
            || ! parent::uninstall()) {
            return false;
        }
        return true;
    }
    public function installDb()
    {
        $queries   = [];

        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'custom_cms_lang` (
            `id_cms` INT,
            `description` LONGTEXT NULL,
            `id_lang` INT,
            primary key(`id_cms`,`id_lang`)
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
        $queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'custom_cms_lang`';
        foreach ($queries as $query) {
            if (! Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }

    public function hookActionCmsPageFormBuilderModifier(array $params): void
    {
        /** @var MmCmsPageFormModifier $productFormModifier */
        $productFormModifier = $this->get(MmCmsPageFormModifier::class);
        $productId           = (int) $params['id'];
        $productFormModifier->modify($productId, $params['form_builder']);
    }
    public function hookActionAfterCreateCmsPageFormHandler($params)
    {
        $this->dataHandler($params);
    }
    public function hookActionAfterUpdateCmsPageFormHandler($params)
    {
        $this->dataHandler($params);
    }
    /**
     * this handles the thematic saving after getting the form data in params
     *
     * @param Object $params
     * @return void
     */
    public function dataHandler($params)
    {
        // $db          = Db::getInstance();
        // $query = new DbQuery();
        // $logger      = new LoggerService();
        // //get data 
        // $productId   = (int) $params['id'];
        // $id_thematic = $params['form_data']['description']['thematic_custom'];

        // $logger->logInfo('Product tags', [$productId, $id_thematic]);
        // //search for the thematic in db
        // $query->select('count(*)')->from('product_thematic')->where('id_product = ' . $productId);
        // $existance = $db->getValue($query);

        // if (! $existance) {
        //     $db->insert('product_thematic', [
        //         'id_tag'     => $id_thematic,
        //         'id_product' => $productId,
        //     ]);
        // } else {
        //     $db->update('product_thematic', [
        //         'id_tag' => $id_thematic,
        //     ], 'id_product = ' . $productId);
        // }
    }

}

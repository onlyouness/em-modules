<?php

use Hp\Producttag\Form\Modifier\ProductFormModifier;

if (! defined('_PS_VERSION_')) {
    exit;
}

class ProductTag extends Module
{
    public function __construct()
    {
        $this->name          = 'producttag';
        $this->tab           = 'administration';
        $this->version       = '1.0.0';
        $this->author        = 'Youness Major Media';
        $this->need_instance = 0;
        $this->bootstrap     = true;

        parent::__construct();

        $this->displayName = $this->l('Product More Info');
        $this->description = $this->l('allows to add a tag to a product');
    }

    public function install()
    {
        if (! parent::install()
            || ! $this->installDb()
            || ! $this->registerHook(['actionProductFormBuilderModifier', 'actionAfterUpdateProductFormHandler', 'actionAfterCreateProductFormHandler'])
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
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'product_more_info` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `id_product` varchar(255),
            `description` LONGTEXT ,
            `active` INT default 1
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'product_more_info_lang` (
            `id_info` INT,
            `id_lang` INT,
            `title` varchar(255),
            `description` LONGTEXT,
            primary key(`id_info`,`id_lang`)
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
        $queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'product_more_info`';
        $queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'product_more_info_lang`';
        foreach ($queries as $query) {
            if (! Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }

    public function hookActionProductFormBuilderModifier(array $params): void
    {
        /** @var ProductFormModifier $productFormModifier */
        $productFormModifier = $this->get(ProductFormModifier::class);
        $productId           = (int) $params['id'];
        $productFormModifier->modify($productId, $params['form_builder']);
    }
    public function hookActionAfterCreateProductFormHandler($params)
    {
        $this->dataHandler($params);
    }
    public function hookActionAfterUpdateProductFormHandler($params)
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
        $db          = Db::getInstance();
        $query = new DbQuery();
        //get data 
        $productId   = (int) $params['id'];
        $id_thematic = $params['form_data']['description']['more_info'];

        //search for the thematic in db
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

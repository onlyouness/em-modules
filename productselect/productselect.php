<?php

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

class ProductSelect extends Module
{
    public $hooks = [
        'displayHome',
        'displayBackOfficeHeader',
        'actionAdminControllerSetMedia',
        'actionProductDelete',
        'actionCategoryDelete',
    ];
    public function __construct()
    {

        $this->name = 'productselect';

        $this->tab = 'front_office_features';

        $this->version = '1.0.0';

        $this->author = 'Youness Major media';

        parent::__construct();

        $this->need_instance = 0;

        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];

        $this->bootstrap = true;

        $this->displayName = $this->l('Product Select', 'Modules.ProductSelect');

        $this->description = $this->l('Allows you choose the products for your store', 'Modules.ProductSelect');

    }

    public function install()
    {
        foreach ($this->hooks as $hook) {
            $this->registerHook($hook);
        }
        return parent::install() && $this->installDb();

    }

    public function uninstall()
    {

        return $this->uninstallDb() && parent::uninstall();

    }

    public function installDb()
    {

        $queries = [];

        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'select_product` (

            `id` INT AUTO_INCREMENT PRIMARY KEY,

            `products` varchar(255) NOT NULL,

            `title` VARCHAR(255) NULL,

            `link` VARCHAR(255) NULL,

            `category_id` INT NULL,

            `position` INT NOT NULL DEFAULT 0,

            `created_at` datetime default CURRENT_TIMESTAMP

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
        $queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'select_product`';
        foreach ($queries as $query) {
            if (! Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;

    }

    public function hookActionAdminControllerSetMedia()
    {
        $controller = Context::getContext()->controller;
        $router     = $this->get('router')->generate('mm_productselect_position');
        Media::addJsDef([
            'urlProductSelect' => $router,
        ]);
        // $controller->addJS($this->_path . '/js/sortable-init.js');

        $controller->addJS($this->_path . '/js/chosen-init.js');

    }
    public function hookDisplayBackOfficeHeader()
    {
        $controller = Context::getContext()->controller;
        $this->context->controller->addJS('https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js');
        $this->context->controller->addCSS('https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css');
        $this->context->controller->addJS('https://code.jquery.com/ui/1.13.2/jquery-ui.min.js');
        $this->context->controller->addCSS('https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');
    }
    public function hookActionProductDelete($params)
    {
        $id_product = $params['id_product'];
        $sections   = $this->getSections();

        foreach ($sections as $section) {
            $sectionId  = $section['id'];
            $productIds = @unserialize($section['products']);
            if (! is_array($productIds)) {
                continue;
            }

            $newProductIds = array_filter($productIds, function ($id) use ($id_product) {
                return $id != $id_product;
            });

            if ($productIds !== $newProductIds) {
                $success = $this->updateProduct($sectionId, $newProductIds);
                if (! $success) {
                    PrestaShopLogger::addLog("Failed to update select_product section $sectionId after deleting product $id_product", 3);
                }
            }
        }
    }

    public function updateProduct(int $id, array $newProductIds)
    {
        $db       = \Db::getInstance();
        $products = serialize(array_values($newProductIds));
        return $db->update('select_product', ['products' => $products], 'id = ' . (int) $id);
    }

    public function hookActionCategoryDelete($params)
    {
        if (! isset($params['category']) || ! $params['category'] instanceof \Category) {
            return;
        }

        $categoryId = (int) $params['category']->id;

        $success = $this->deleteSectionsByCategory($categoryId);
        if (! $success) {
            PrestaShopLogger::addLog("Failed to delete select_product entries for category ID $categoryId", 3);
        }
    }

    public function deleteSectionsByCategory(int $categoryId)
    {
        $db = \Db::getInstance();
        return $db->delete('select_product', 'category_id = ' . $categoryId);
    }

    // public function updateProduct()
    // {
    //     $db = \Db::getInstance();

    //     // 1. Backup the table
    //     $backupQuery   = 'CREATE TABLE `' . _DB_PREFIX_ . 'select_product_backup_2` AS SELECT * FROM `' . _DB_PREFIX_ . 'select_product`;';
    //     $backupSuccess = $db->execute($backupQuery);

    //     if ($backupSuccess) {
    //         // 2. Modify to add position column
    //         $alterQuery   = 'ALTER TABLE `' . _DB_PREFIX_ . 'select_product` ADD COLUMN `position` INT NOT NULL DEFAULT 0;';
    //         $alterSuccess = $db->execute($alterQuery);

    //         return $alterSuccess;
    //     }

    //     return false;
    // }

    public function hookDisplayHome($params)
    {
        $sections = $this->getSections();
        $db       = Db::getInstance();

        $data = [];

        foreach ($sections as $section) {
            $title             = $section['title'];
            $link              = $section['link'];
            $category_id       = $section['category_id'];
            $products          = unserialize($section['products']);
            $formattedProducts = $this->formatProducts($products);

            if (! empty($category_id)) {
                $category = new Category($category_id, $this->context->language->id);
                $link     = $this->context->link->getCategoryLink($category);
                $title    = $category->name;
            }

            $data[] = [
                'id'       => $section['id'],
                'position' => $section['position'],
                'title'    => $title,
                'link'     => $link,
                'products' => $formattedProducts,
            ];
        }
        // Tools::dieObject($data);

        $this->context->smarty->assign([
            'products'   => $data,
            'module_dir' => $this->_path,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/products.tpl');

    }

    public function getContent()
    {

        $router = $this->get('router')->generate('mm_productselect_index');

        Tools::redirectAdmin($router);

    }

    public function formatProducts($productIds)
    {
        if (! empty($productIds)) {
            $products = [];
            foreach ($productIds as $pr) {
                $products[] = new Product($pr, false, $this->context->language->id);
            }
            $assembler            = new ProductAssembler($this->context);
            $presenterFactory     = new ProductPresenterFactory($this->context);
            $presentationSettings = $presenterFactory->getPresentationSettings();
            $presenter            = new ProductListingPresenter(
                new ImageRetriever(
                    $this->context->link
                ),
                $this->context->link,
                new PriceFormatter(),
                new ProductColorsRetriever(),
                $this->context->getTranslator()
            );
            $products_f = [];
            foreach ($products as $rawProduct) {
                $productData = $rawProduct->getFields();
                if (! isset($productData['id_product'])) {
                    $productData['id_product'] = $rawProduct->id;
                }
                $products_f[] = $presenter->present(
                    $presentationSettings,
                    $assembler->assembleProduct($productData),
                    $this->context->language
                );
            }
        } else {
            $products_f = null;
        }
        return $products_f;
    }

    public function getSections()
    {

        $db = Db::getInstance();

        $query = new DbQuery();

        $query->select('*')

            ->from('select_product');

        $query->orderBy('position ASC');

        return $db->executeS($query);

    }

}

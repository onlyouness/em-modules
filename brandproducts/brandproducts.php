<?php

use Hp\Brandproducts\Entity\BrandProduct;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class BrandProducts extends Module
{
    public function __construct()
    {
        $this->name    = 'brandproducts';
        $this->tab     = 'front_office_features';
        $this->version = '1.0.0';
        $this->author  = 'Youness Major media';
        parent::__construct();
        $this->need_instance          = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];
        $this->bootstrap              = true;
        $this->displayName            = $this->l('BrandProducts', 'brandproducts');
        $this->description            = $this->l('Allows you to display product of a certain brand.');
    }
    public function install()
    {
        return parent::install() && $this->installDb() && $this->installTab() && $this->registerHook('displayHome');
    }
    public function uninstall()
    {
        return parent::uninstall() && $this->uninstallDb() && $this->uninstallTab();
    }
    public function installDb()
    {
        $db        = Db::getInstance();
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'brand_product` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `display_type` ENUM(\'brand\', \'category\') NOT NULL,
            `brand_id` INT NULL,
            `category_id` INT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX (`brand_id`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';
        foreach ($queries as $query) {
            if (! $db->execute($query)) {
                return false;
            }
            return true;
        }
    }

    public function uninstallDb()
    {
        $db        = Db::getInstance();
        $queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'brand_product`';
        foreach ($queries as $query) {
            if (! $db->execute($query)) {
                return false;
            }
            return true;
        }
    }
    public function installTab()
    {
        $languages          = Language::getLanguages();
        $parentTabClassName = 'AdminBrandProducts';
        if (! Tab::getIdFromClassName($parentTabClassName)) {
            $parentTab             = new Tab();
            $parentTab->active     = true;
            $parentTab->enabled    = true;
            $parentTab->module     = $this->name;
            $parentTab->class_name = $parentTabClassName;
            $parentTab->id_parent  = 0;
            foreach ($languages as $language) {
                $parentTab->name[$language['id_lang']] = 'Brand Products'; // Display name for parent tab
            }
            $parentTab->icon           = 'settings';
            $parentTab->wording        = 'Brand Products';
            $parentTab->wording_domain = 'Modules.BrandProducts.Admin';
            try {
                $parentTab->save();
            } catch (Exception $e) {
                PrestaShopLogger::addLog('Error creating parent tab: ' . $e->getMessage(), 3);
                return false;
            }
        } else {
            $parentTab = new Tab(Tab::getIdFromClassName($parentTabClassName));
        }

        $tabClassName = 'BrandProductController';
        if (! Tab::getIdFromClassName($tabClassName)) {
            $tab             = new Tab();
            $tab->active     = true;
            $tab->enabled    = true;
            $tab->module     = $this->name;
            $tab->class_name = $tabClassName;
            $tab->id_parent  = (int) $parentTab->id;
            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = 'Brand Products';
            }
            $tab->icon           = 'settings.applications';
            $tab->wording        = 'Brand Products';
            $tab->wording_domain = 'Modules.BrandProducts.Admin';
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
            'BrandProductController',
            'AdminBrandProducts',
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
    public function hookDisplayHome()
    {
        // Retrieve the BrandProduct repository
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $brandProducts = $entityManager->getRepository(BrandProduct::class)->findAll();

        // Initialize the data array
        $data = [];

        foreach ($brandProducts as $brandProduct) {
            $sectionData = $this->getSectionData($brandProduct);
            if ($sectionData) {
                $data[] = $sectionData;
            }
        }
        // Tools::dieObject($data);
        $this->context->smarty->assign([
            'data' => $data,
            'link' => $this->context->link,
        ]);
        return $this->display(__FILE__, 'views/templates/hook/home.tpl');
    }

    /**
     * Get section data for a specific BrandProduct
     *
     * @param BrandProduct $brandProduct
     * @return array|null
     */
    private function getSectionData($brandProduct)
    {
        $row      = $brandProduct->toArray();
        $type     = $row['displayType'];
        $title    = '';
        $products = [];

        if ($type === 'brand' && ! empty($row['brand'])) {
            $brand = new Manufacturer($row['brand'], $this->context->language->id);
            if (! Validate::isLoadedObject($brand)) {
                return null;
            }

            $title          = $brand->name;
            $searchProvider = new \PrestaShop\PrestaShop\Adapter\Manufacturer\ManufacturerProductSearchProvider(
                $this->context->getTranslator(),
                $brand
            );
        } elseif ($type === 'category' && ! empty($row['category'])) {
            $category = new Category($row['category'], $this->context->language->id);
            if (! Validate::isLoadedObject($category)) {
                return null;
            }

            $title          = $category->name;
            $searchProvider = new \PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider(
                $this->context->getTranslator(),
                $category
            );
        } else {
            return null;
        }

        $products = $this->getFormattedProducts($searchProvider);

        return [
            'title'    => $title,
            'products' => $products,
        ];
    }

    /**
     * Retrieve and format products using a search provider
     *
     * @param ProductSearchProviderInterface $searchProvider
     * @return array
     */
    private function getFormattedProducts($searchProvider)
    {
        $context = new ProductSearchContext($this->context);
        $query   = (new ProductSearchQuery())
            ->setResultsPerPage(10)
            ->setPage(1)
            ->setSortOrder(new SortOrder('product', 'position', 'asc'));

        $result = $searchProvider->runQuery($context, $query);

        $assembler            = new ProductAssembler($this->context);
        $presenterFactory     = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter            = new \PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter(
            new ImageRetriever($this->context->link),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );

        return array_map(
            fn($rawProduct) => $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            ),
            $result->getProducts()
        );
    }

}

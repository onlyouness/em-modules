<?php
/**
 * MIT License
 *
 * Copyright (c) 2020 Alessandro Capezzera
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *  @author    Alessandro Capezzera <alessandro.rozzano@gmail.com>
 *  @copyright 2020 Alessandro Capezzera
 *  @license   https://spdx.org/licenses/MIT.html  MIT License
 */

if (! defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

include_once _PS_MODULE_DIR_ . 'categoryproducts/CategoryProductsClass.php';

class Categoryproducts extends Module
{
    protected $config_form = false;
    private $templateFile;
    private $category;
    public function __construct()
    {
        $this->name          = 'categoryproducts';
        $this->tab           = 'front_office_features';
        $this->version       = '1.0.0';
        $this->author        = 'Demacri';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Products Block filtered by categories');
        $this->description = $this->l('Shows products block filtered by categories in your home page.');

        $this->confirmUninstall = $this->l('');

        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->templateFile           = 'module:categoryproducts/views/templates/hook/categoryproducts.tpl';
    }

    public function install()
    {
        include dirname(__FILE__) . '/sql/install.php';
        return parent::install() &&
        $this->registerHook('header') &&
        $this->registerHook('backOfficeHeader') &&
        $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        include dirname(__FILE__) . '/sql/uninstall.php';
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $html                = '';
        $id_categoryproducts = (int) Tools::getValue('id_categoryproducts');

        if (Tools::isSubmit('savecategoryproducts')) { //SAVE
            if ($id_categoryproducts) {
                $categoryproducts = new CategoryProductsClass((int) $id_categoryproducts);
            } else {
                $categoryproducts = new CategoryProductsClass();
            }

            $categoryproducts->copyFromPost();

            if ($categoryproducts->validateFields(false)) {
                $categoryproducts->save();
            } else {
                $html .= '<div class="conf error">' . $this->trans('An error occurred while attempting to save.', [], 'Admin.Notifications.Error') . '</div>';
            }
        }

        if (Tools::isSubmit('updatecategoryproducts') || Tools::isSubmit('addcategoryproducts')) { //UPDATE/ADD
            $helper = $this->initForm();
            if ($id_categoryproducts) {
                $categoryproducts     = new CategoryProductsClass((int) $id_categoryproducts);
                $helper->fields_value = [
                    'category_id' => $categoryproducts->category_id,
                    'nproducts'   => $categoryproducts->nproducts,
                    'randomize'   => $categoryproducts->randomize,
                ];
            }
            if ($id_categoryproducts = Tools::getValue('id_categoryproducts')) {
                $this->fields_form[0]['form']['input'][]     = ['type' => 'hidden', 'name' => 'id_categoryproducts'];
                $helper->fields_value['id_categoryproducts'] = (int) $id_categoryproducts;
            }

            return $html . $helper->generateForm($this->fields_form);
        } elseif (Tools::isSubmit('deletecategoryproducts')) { //DELETE
            $categoryproducts = new CategoryproductsClass((int) $id_categoryproducts);
            $categoryproducts->delete();
            Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'));
        } else { //BROWSE
            $content           = $this->getListContent();
            $helper            = $this->initList();
            $helper->listTotal = count($content);
            return $html . $helper->generateList($content, $this->fields_list);
        }

        //if (isset($_POST['submitModule'])) {}
    }
    protected function getListContent()
    {
        return Db::getInstance()->executeS('
            SELECT r.`id_categoryproducts`,r.`category_id`, r.`nproducts`, r.`randomize`
            FROM `' . _DB_PREFIX_ . 'categoryproducts` r');
    }
    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar             = false;
        $helper->table                    = $this->table;
        $helper->module                   = $this;
        $helper->default_form_language    = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier    = $this->identifier;
        $helper->submit_action = 'submitCategoryproductsModule';
        $helper->currentIndex  = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    protected function initForm()
    {
        $this->fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->trans('New Category Products'),
            ],
            'input'  => [

                [
                    'col'    => 3,
                    'type'   => 'text',
                    'prefix' => '',
                    'desc'   => $this->l('Enter a valid category id.'),
                    'name'   => 'category_id',
                    'label'  => $this->l('Category id'),
                ],
                [
                    'col'    => 3,
                    'type'   => 'text',
                    'prefix' => '',
                    'desc'   => $this->l('Set the number of products that you would like to display on homepage (default: 8).'),
                    'name'   => 'nproducts',
                    'label'  => $this->l('Number of products to be displayed'),
                ],
                [
                    'type'   => 'switch',
                    'label'  => $this->trans('Randomly display products'),
                    'name'   => 'randomize',
                    'class'  => 'fixed-width-xs',
                    'desc'   => $this->trans('Enable if you wish the products to be displayed randomly (default: no).'),
                    'values' => [
                        [
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', [], 'Admin.Global'),
                        ],
                        [
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('No', [], 'Admin.Global'),
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
        ];

        $helper                  = new HelperForm();
        $helper->module          = $this;
        $helper->name_controller = 'categoryproducts';
        $helper->identifier      = $this->identifier;
        $helper->token           = Tools::getAdminTokenLite('AdminModules');

        $helper->currentIndex   = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->toolbar_scroll = true;
        $helper->title          = $this->displayName;
        $helper->submit_action  = 'savecategoryproducts';
        $helper->toolbar_btn    = [
            'save' => [
                'desc' => $this->trans('Save', [], 'Admin.Actions'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->trans('Back to list', [], 'Admin.Actions'),
            ],
        ];
        return $helper;
    }
    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    public function hookDisplayHome()
    {
        if (! $this->isCached($this->templateFile, $this->getCacheId('categoryproducts'))) {
            $blocks    = $this->getListContent();
            $variables = ['blocks' => []];
            foreach ($blocks as &$block) {
                $products          = $this->getProducts($block);
                $filteredIds       = $this->getFilteredIds();
                $productDisplaying = $this->getFilteredProducts($products, $filteredIds);
                if (! empty($productDisplaying)) {
                    $variables['blocks'][] = [
                        'products'        => $productDisplaying,
                        'allProductsLink' => Context::getContext()->link->getCategoryLink($block['category_id']),
                        'categoryName'    => $this->category->name[(int) Configuration::get('PS_LANG_DEFAULT')],
                    ];
                }
            }
            if (count($variables['blocks']) == 0) {
                return false;
            }
            $this->smarty->assign($variables);
        }
        return $this->fetch($this->templateFile, $this->getCacheId('categoryproducts'));
    }
    public function getFilteredProducts($products, $filteredIds)
    {
        $filteredProducts = [];
        foreach ($products as $key => $product) {
            if (in_array($product['id_product'], $filteredIds)) {
                $filteredProducts[] = $product;
            }
        }
        return array_slice($filteredProducts, 0, 10);
    }
    public function getFilteredIds()
    {
        $context       = Context::getContext();
        $id_lang       = $context->language->id;
        $iso_code      = $context->language->iso_code;
        $customer      = $context->customer;
        $id_customer   = $customer->id;
        $productIdList = [];

        if (! is_null($id_customer)) {
            $address        = Address::getFirstCustomerAddressId($id_customer);
            $group_id       = $customer->id_default_group;
            $countryId      = Context::getContext()->cookie->selected_country;
            $country        = Country::getNameById($id_lang, $countryId);
            $groupCondition = "";

            if ($group_id == 4) {
                $groupCondition = "cg.id_default_group = 4";
            }
            if ($group_id == 5) {
                $groupCondition = "cg.id_default_group = 4 OR cg.id_default_group = 5";
            }
            if ($group_id == 3 || $group_id == 6) {
                $groupCondition = "cg.id_default_group = 5";

                $continent_query = "SELECT c.continent
                            FROM `country` c
                            INNER JOIN `country_translation` ct ON ct.country_id = c.id
                            WHERE ct.name = '" . pSQL($country) . "'";

                $seller_continent = Db::getInstance()->getValue($continent_query);

                $countries_query = "SELECT ct.name
                            FROM `country` c
                            INNER JOIN `country_translation` ct ON ct.country_id = c.id
                            WHERE c.continent = '" . pSQL($seller_continent) . "'";

                $continent_countries = Db::getInstance()->executeS($countries_query);

                $country_list = [];
                foreach ($continent_countries as $continent_country) {
                    $country_list[] = "'" . pSQL($continent_country['name']) . "'";
                }

                $country_condition = empty($country_list) ? "" : "AND cl.name IN (" . implode(",", $country_list) . ")";

                $query = "SELECT sp.id_product
                  FROM `ps_seller_product` sp
                  INNER JOIN `ps_seller` s ON s.id_seller = sp.id_seller
                  INNER JOIN `ps_customer` cg ON cg.id_customer = s.id_customer
                  INNER JOIN `ps_country_lang` cl ON cl.name = s.country
                  WHERE ($groupCondition) $country_condition";
            } else {
                $query = "SELECT sp.id_product
                  FROM `ps_seller_product` sp
                  INNER JOIN `ps_seller` s ON s.id_seller = sp.id_seller
                  INNER JOIN `ps_customer` cg ON cg.id_customer = s.id_customer
                  WHERE $groupCondition";
            }

            $productIds    = Db::getInstance()->executeS($query);
            $productIdList = [];

            foreach ($productIds as $productItem) {
                $productIdList[] = $productItem['id_product'];
            }
        }
        return $productIdList;

    }
    protected function getProducts($block)
    {
        $this->category = new Category($block['category_id']);

        $searchProvider = new CategoryProductSearchProvider(
            $this->context->getTranslator(),
            $this->category
        );

        $context = new ProductSearchContext($this->context);

        $query = new ProductSearchQuery();

        $nProducts = $block['nproducts'];

        $query
            ->setResultsPerPage($nProducts)
            ->setPage(1)
        ;

        if ($block['randomize']) {
            $query->setSortOrder(SortOrder::random());
        } else {
            $query->setSortOrder(new SortOrder('product', 'position', 'asc'));
        }

        $result = $searchProvider->runQuery(
            $context,
            $query
        );

        $assembler = new ProductAssembler($this->context);

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

        $products_for_template = [];

        foreach ($result->getProducts() as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        }

        return $products_for_template;
    }

    protected function initList()
    {
        $this->fields_list = [
            'id_categoryproducts' => [
                'title'   => $this->trans('ID', [], 'Admin.Global'),
                'width'   => 120,
                'type'    => 'text',
                'search'  => false,
                'orderby' => false,
            ],
            'category_id'         => [
                'title'   => $this->trans('Category ID'),
                'width'   => 140,
                'type'    => 'text',
                'search'  => false,
                'orderby' => false,
            ],
            'nproducts'           => [
                'title'   => $this->trans('Number of displayed products'),
                'width'   => 140,
                'type'    => 'text',
                'search'  => false,
                'orderby' => false,
            ],
            'randomize'           => [
                'title'   => $this->trans('Randomize view', [], 'Admin.Global'),
                'width'   => 140,
                'type'    => 'text',
                'search'  => false,
                'orderby' => false,
            ],
        ];

        $helper                     = new HelperList();
        $helper->shopLinkType       = '';
        $helper->simple_header      = false;
        $helper->identifier         = 'id_categoryproducts';
        $helper->actions            = ['edit', 'delete'];
        $helper->show_toolbar       = true;
        $helper->toolbar_btn['new'] = [
            'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&add' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->trans('Add new', [], 'Admin.Actions'),
        ];

        $helper->title        = $this->displayName;
        $helper->table        = $this->name;
        $helper->token        = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        return $helper;
    }
}

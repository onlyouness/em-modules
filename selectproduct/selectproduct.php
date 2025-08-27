<?php

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

if (! defined('_PS_VERSION_')) {
    exit;
}

class SelectProduct extends Module
{
    public function __construct()
    {
        $this->name          = 'selectproduct';
        $this->tab           = 'administration';
        $this->version       = '1.0.0';
        $this->author        = 'Youness Major Media';
        $this->need_instance = 0;
        $this->bootstrap     = true;
        parent::__construct();
        $this->displayName = $this->l('Select Product');
        $this->description = $this->l('Allows you to product you want to display');
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayHome') && $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }
    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/productselect.css');
    }
    public function hookDisplayHome()
    {
        $products = Configuration::get('SELECT_PRODUCT');
        $title    = Configuration::get('TITLE_FIRST_SECTION');

        $productUnserialized = json_decode($products, true);
        if (! empty($productUnserialized)) {
            $products = [];
            foreach ($productUnserialized as $pr) {
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

                $products_f[] = $presenter->present(
                    $presentationSettings,
                    $assembler->assembleProduct($productData),
                    $this->context->language
                );
            }
        } else {
            $products_f = null;
        }
        $variables = [
            'products' => $products_f,
            'title'    => $title,
        ];
        $this->smarty->assign($variables);
        return $this->display(__FILE__, 'views/templates/hook/displayHome.tpl');
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit' . $this->name)) {

            $products = Tools::getValue('SELECT_PRODUCT');
            $title    = Tools::getValue('TITLE_FIRST_SECTION');

            $serializedProducts = json_encode($products);

            if (empty($products) && empty($title)) {
                $output = $this->displayError($this->l('Invalid Details'));
            } else {
                Configuration::updateValue('SELECT_PRODUCT', $serializedProducts);
                Configuration::updateValue('TITLE_FIRST_SECTION', $title);
            }

            if (empty($output)) {
                $output = $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        $lang     = $this->context->language->id;
        $products = Product::getProducts($lang, 0, 0, 'id_product', 'DESC');

        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Products Selection'),
                ],
                'input'  => [
                    [
                        'type'     => 'text',
                        'label'    => $this->l('Enter the title Of the section'),
                        'name'     => 'TITLE_FIRST_SECTION',
                        'required' => false,
                    ],
                    [
                        'type'     => 'select',
                        'label'    => $this->l('Choose Product'),
                        'name'     => 'SELECT_PRODUCT[]',
                        'multiple' => true,
                        'options'  => [
                            'query' => $products,
                            'id'    => 'id_product',
                            'name'  => 'name',
                        ],
                        'required' => true,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper                  = new HelperForm();
        $helper->table           = $this->table;
        $helper->name_controller = $this->name;

        $helper->token         = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex  = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        $selectedProducts                            = json_decode(Configuration::get('SELECT_PRODUCT'), true);
        $helper->fields_value['SELECT_PRODUCT[]']    = $selectedProducts ?: [];
        $helper->fields_value['TITLE_FIRST_SECTION'] = Tools::getValue('TITLE_FIRST_SECTION', Configuration::get('TITLE_FIRST_SECTION'));

        return $helper->generateForm([$form]);
    }

}

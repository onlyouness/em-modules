<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class MmProductBanner extends Module
{
    public function __construct()
    {
        $this->name = 'mmproductbanner';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Youness Elbaz';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('MM Product Banner', [], 'Modules.mmproductbanner.Admin');
        $this->description = $this->trans('Allows you to make great product banner for your store .', [], 'Modules.mmproductbanner.Admin');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.mmproductbanner.Admin');

        if (!Configuration::get('MM_PRODUCT_NAME')) {
            $this->warning = $this->trans('No name provided', [], 'Modules.mmproductbanner.Admin');
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return (
            parent::install() && $this->registerHook('displayHome') && $this->registerHook('displayHeader')
            && Configuration::updateValue('MM_PRODUCT_NAME', 'mmproductbanner')
        );
    }
    public function uninstall()
    {
        return (
            parent::uninstall()
            && Configuration::deleteByName('MM_PRODUCT_NAME')
        );
    }
    public function hookDisplayHeader(){
        $this->context->controller->addCSS($this->_path.'views/css/productbanner.css');
    }
    public function hookDisplayHome($params)
    {

        $currentImage = Configuration::get('BANNER_PRODUCT_IMAGE');
        $produitID = Configuration::get('PRODUCT_BANNER_PRODUCT');
        $produit = new Product($produitID,$this->context->language->id);
        $bannerInfo = [
            'title'=>Configuration::get('TITLE_BANNER_PRODUCT'),
            'description'=>Configuration::get('SHORT_DESCRIPTION_BANNER_PRODUCT'),
            'shortDescription'=> Configuration::get('DESCRIPTION_BANNER_PRODUCT'),
        ];

        $image_url = $currentImage ? $this->context->link->getMediaLink('/modules/'. $this->name . '/views/img/' . $currentImage) : '';
        $this->context->smarty->assign(array(
            'link' => $this->context->link,
            'bannerInfo'=>$bannerInfo,
            "image" => $image_url,
            "product" => $produit,
        ));
        return $this->display(__FILE__, 'views/templates/hook/displayHome.tpl');
    }

    public function getContent()
    {
        $output = '';

        // This part is executed only when the form is submitted
        if (Tools::isSubmit('submit' . $this->name)) {
            // Retrieve the values set by the user
            $productDiscount = Tools::getValue('PRODUCT_BANNER_PRODUCT');
            $title = Tools::getValue('TITLE_BANNER_PRODUCT');
            $shortDescription = Tools::getValue('SHORT_DESCRIPTION_BANNER_PRODUCT');
            $description = Tools::getValue('DESCRIPTION_BANNER_PRODUCT');
            $imageBanner =  Tools::getValue('BANNER_PRODUCT_IMAGE');

            if (empty($productDiscount) && empty($title) && empty($description) && empty($shortDescription)) {
                $output = $this->displayError($this->l('Invalid Product' . $productDiscount));
            } else {
                Configuration::updateValue('PRODUCT_BANNER_PRODUCT', $productDiscount);
                Configuration::updateValue('TITLE_BANNER_PRODUCT', $title);
                Configuration::updateValue('SHORT_DESCRIPTION_BANNER_PRODUCT', $shortDescription);
                Configuration::updateValue('DESCRIPTION_BANNER_PRODUCT', $description);
            }

            if (isset($_FILES['BANNER_PRODUCT_IMAGE']) && $_FILES['BANNER_PRODUCT_IMAGE']['error'] === UPLOAD_ERR_OK) {
                $imageBanner = $_FILES['BANNER_PRODUCT_IMAGE'];

                // Check if the upload directory exists, otherwise create it
                $uploadDir = _PS_MODULE_DIR_ . $this->name . '/views/img/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $imageName = Tools::safeOutput($imageBanner['name']);
                $imagePath = $uploadDir . $imageName;

                // Move the uploaded file to the desired directory
                if (move_uploaded_file($imageBanner['tmp_name'], $imagePath)) {
                    Configuration::updateValue('BANNER_PRODUCT_IMAGE', $imageName);
                } else {
                    $output .= $this->displayError($this->l('Failed to upload image'));
                }
            } elseif (isset($_FILES['BANNER_PRODUCT_IMAGE']) && $_FILES['BANNER_PRODUCT_IMAGE']['error'] !== UPLOAD_ERR_NO_FILE) {
                $output .= $this->displayError($this->l('Invalid Image Banner'));
            }


            if (empty($output)) {
                $output = $this->displayConfirmation($this->l('Settings updated with id.' . Configuration::get('BANNER_PRODUCT_IMAGE') . ' image ' . Configuration::get('BANNER_PRODUCT_IMAGE')));
            }
        }

        return $output . $this->displayForm();
    }


    public function displayForm()
    {
//        $products = $this->getDiscountProducts();
        $lang = $this->context->language->id;
        $products = Product::getProducts($lang, 0, 0, 'id_product', 'DESC');
        $currentImage = Configuration::get('BANNER_PRODUCT_IMAGE');
        $image_url = $currentImage ? $this->context->link->getMediaLink('/modules/'. $this->name . '/views/img/' . $currentImage) : '';
        $image = '<div class="col-lg-6"><img src="' . $image_url . '" class="img-thumbnail" width="400"></div>';

        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Product Banner'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Enter the title'),
                        'name' => 'TITLE_BANNER_PRODUCT',
                        'required' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Enter the short description'),
                        'name' => 'SHORT_DESCRIPTION_BANNER_PRODUCT',
                        'required' => false,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Enter Description'),
                        'name' => 'DESCRIPTION_BANNER_PRODUCT',
                        'cols' => 8,
                        'rows' => 4,
                        'autoload_rte' => 'rte',
                        'required' => false,

                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Choose Product'),
                        'name' => 'PRODUCT_BANNER_PRODUCT',
                        'options' => [
                            'query' => $products,
                            'id' => 'id_product',
                            'name' => 'name',
                        ],
                        'required' => true,
                    ],
                    [
                        'type' => 'file',
                        'label' => $this->l('Banner Image'),
                        'name' => 'BANNER_PRODUCT_IMAGE',
                        'display_image' => true,
                        'image' => $image,                      
                        'required' => false,
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->table = $this->table;
        $helper->name_controller = $this->name;

        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        $currentImage = Configuration::get('BANNER_PRODUCT_IMAGE');
        if ($currentImage) {
            $helper->fields_value['BANNER_PRODUCT_IMAGE'] = $this->context->link->getMediaLink(_PS_MODULE_DIR_ . $this->name . '/views/img/' . $currentImage);
        } else {
            $helper->fields_value['BANNER_PRODUCT_IMAGE'] = '';
        }

        $helper->fields_value['PRODUCT_BANNER_PRODUCT'] = Tools::getValue('PRODUCT_BANNER_PRODUCT', Configuration::get('PRODUCT_BANNER_PRODUCT'));
        $helper->fields_value['TITLE_BANNER_PRODUCT'] = Tools::getValue('TITLE_BANNER_PRODUCT', Configuration::get('TITLE_BANNER_PRODUCT'));
        $helper->fields_value['SHORT_DESCRIPTION_BANNER_PRODUCT'] = Tools::getValue('SHORT_DESCRIPTION_BANNER_PRODUCT', Configuration::get('SHORT_DESCRIPTION_BANNER_PRODUCT'));
        $helper->fields_value['DESCRIPTION_BANNER_PRODUCT'] = Tools::getValue('DESCRIPTION_BANNER_PRODUCT', Configuration::get('DESCRIPTION_BANNER_PRODUCT'));

        return $helper->generateForm([$form]);
    }

    public function getDiscountProducts()
    {
        $sql = 'SELECT pl.*        FROM ' . _DB_PREFIX_ . 'product p
        INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON p.id_product = pl.id_product';
        return Db::getInstance()->executeS($sql);
    }
}

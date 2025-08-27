<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class DiscountBanner extends Module
{
    public function __construct()
    {
        $this->name = 'discountbanner';
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

        $this->displayName = $this->trans('MM Discount banner', [], 'Modules.discountbanner.Admin');
        $this->description = $this->trans('Description of my module discount banner.', [], 'Modules.discountbanner.Admin');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.discountbanner.Admin');

        if (!Configuration::get('DISCOUNTBANNER_NAME')) {
            $this->warning = $this->trans('No name provided', [], 'Modules.discountbanner.Admin');
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return (
            parent::install() && $this->registerHook('displayHome') && $this->registerHook('displayHeader')
            && Configuration::updateValue('DISCOUNTBANNER_NAME', 'discountbanner')
        );
    }
    public function uninstall()
    {
        return (
            parent::uninstall()
            && Configuration::deleteByName('DISCOUNTBANNER_NAME')
        );
    }
    public function hookDisplayHeader(){
        $this->context->controller->addCSS($this->_path.'views/css/discountbanner.css');
    }
    public function hookDisplayHome($params)
    {

        $currentImage = Configuration::get('BANNER_IMAGE');
        $produitID = Configuration::get('PRODUCT_DISCOUNT');
        $produit = new Product($produitID,$this->context->language->id);
        $title = Configuration::get('TITLE_BANNER_DISCOUNT');
        $shortDescription = Configuration::get('SHORT_DESCRIPTION_BANNER_DISCOUNT');
        $description = Configuration::get('DESCRIPTION_BANNER_DISCOUNT');


        $image_url = $currentImage ? $this->context->link->getMediaLink('/modules/'. $this->name . '/views/img/' . $currentImage) : '';
        // var_dump($produit);
//         Tools::dieObject($title);
        $this->context->smarty->assign(array(
            'link' => $this->context->link,
            'title'=>$title,
            'description'=>$description,
            'shortDescription'=>$shortDescription,
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
            $productDiscount = Tools::getValue('PRODUCT_DISCOUNT');
            $title = Tools::getValue('TITLE_BANNER_DISCOUNT');
            $shortDescription = Tools::getValue('SHORT_DESCRIPTION_BANNER_DISCOUNT');
            $description = Tools::getValue('DESCRIPTION_BANNER_DISCOUNT');
            $imageBanner =  Tools::getValue('BANNER_IMAGE');



            if (empty($productDiscount) && empty($title) && empty($description) && empty($shortDescription)) {
                $output = $this->displayError($this->l('Invalid Product' . $productDiscount));
            } else {
                Configuration::updateValue('PRODUCT_DISCOUNT', $productDiscount);
                Configuration::updateValue('TITLE_BANNER_DISCOUNT', $title);
                Configuration::updateValue('SHORT_DESCRIPTION_BANNER_DISCOUNT', $shortDescription);
                Configuration::updateValue('DESCRIPTION_BANNER_DISCOUNT', $description);
            }

            if (isset($_FILES['BANNER_IMAGE']) && $_FILES['BANNER_IMAGE']['error'] === UPLOAD_ERR_OK) {
                $imageBanner = $_FILES['BANNER_IMAGE'];

                // Check if the upload directory exists, otherwise create it
                $uploadDir = _PS_MODULE_DIR_ . $this->name . '/views/img/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $imageName = Tools::safeOutput($imageBanner['name']);
                $imagePath = $uploadDir . $imageName;

                // Move the uploaded file to the desired directory
                if (move_uploaded_file($imageBanner['tmp_name'], $imagePath)) {
                    Configuration::updateValue('BANNER_IMAGE', $imageName);
                } else {
                    $output .= $this->displayError($this->l('Failed to upload image'));
                }
            } elseif (isset($_FILES['BANNER_IMAGE']) && $_FILES['BANNER_IMAGE']['error'] !== UPLOAD_ERR_NO_FILE) {
                $output .= $this->displayError($this->l('Invalid Image Banner'));
            }


            if (empty($output)) {
                $output = $this->displayConfirmation($this->l('Settings updated with id.' . Configuration::get('PRODUCT_DISCOUNT') . ' image ' . Configuration::get('BANNER_IMAGE')));
            }
        }

        return $output . $this->displayForm();
    }


    public function displayForm()
    {
//        $products = $this->getDiscountProducts();
        $lang = $this->context->language->id;
        $products = Product::getProducts($lang, 0, 0, 'id_product', 'DESC');
        $currentImage = Configuration::get('BANNER_IMAGE');
        $image_url = $currentImage ? $this->context->link->getMediaLink('/modules/'. $this->name . '/views/img/' . $currentImage) : '';
        $image = '<div class="col-lg-6"><img src="' . $image_url . '" class="img-thumbnail" width="400"></div>';

        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Category Section'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Enter the title'),
                        'name' => 'TITLE_BANNER_DISCOUNT',
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Enter the short description'),
                        'name' => 'SHORT_DESCRIPTION_BANNER_DISCOUNT',
                        'required' => true,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Enter Description'),
                        'name' => 'DESCRIPTION_BANNER_DISCOUNT',
                        'cols' => 8,
                        'rows' => 4,
                        'autoload_rte' => 'rte'

                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Choose Product'),
                        'name' => 'PRODUCT_DISCOUNT',
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
                        'name' => 'BANNER_IMAGE',
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

        $currentImage = Configuration::get('BANNER_IMAGE');
        if ($currentImage) {
            $helper->fields_value['BANNER_IMAGE'] = $this->context->link->getMediaLink(_PS_MODULE_DIR_ . $this->name . '/views/img/' . $currentImage);
        } else {
            $helper->fields_value['BANNER_IMAGE'] = '';
        }

        $helper->fields_value['PRODUCT_DISCOUNT'] = Tools::getValue('PRODUCT_DISCOUNT', Configuration::get('PRODUCT_DISCOUNT'));
        $helper->fields_value['TITLE_BANNER_DISCOUNT'] = Tools::getValue('TITLE_BANNER_DISCOUNT', Configuration::get('TITLE_BANNER_DISCOUNT'));
        $helper->fields_value['SHORT_DESCRIPTION_BANNER_DISCOUNT'] = Tools::getValue('SHORT_DESCRIPTION_BANNER_DISCOUNT', Configuration::get('SHORT_DESCRIPTION_BANNER_DISCOUNT'));
        $helper->fields_value['DESCRIPTION_BANNER_DISCOUNT'] = Tools::getValue('DESCRIPTION_BANNER_DISCOUNT', Configuration::get('DESCRIPTION_BANNER_DISCOUNT'));

        return $helper->generateForm([$form]);
    }

    public function getDiscountProducts()
    {
        $sql = 'SELECT pl.*        FROM ' . _DB_PREFIX_ . 'product p
        INNER JOIN ' . _DB_PREFIX_ . 'specific_price sp ON p.id_product = sp.id_product
        INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON p.id_product = pl.id_product
        WHERE sp.reduction > 0';
        return Db::getInstance()->executeS($sql);
    }
}

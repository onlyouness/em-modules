<?php


if (!defined('_PS_VERSION_')) {
    exit;
}

class FooterDiscount extends Module
{
    public function __construct()
    {
        $this->name = 'footerdiscount';
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

        $this->displayName = $this->trans('MM Discount banner footer', [], 'Modules.discountbanner.Admin');
        $this->description = $this->trans('Manager the banner of footers.', [], 'Modules.discountbanner.Admin');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.discountbanner.Admin');

        if (!Configuration::get('FOOTER_DISCOUNT_BANNER_NAME')) {
            $this->warning = $this->trans('No name provided', [], 'Modules.discountbanner.Admin');
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return (
            parent::install() && $this->registerHook('displayHome')&& $this->registerHook('displayFooterBefore')
            && Configuration::updateValue('FOOTER_DISCOUNT_BANNER_NAME', 'footerdiscountbanner')
        );
    }

    public function uninstall()
    {
        return (
            parent::uninstall()
            && Configuration::deleteByName('FOOTER_DISCOUNT_BANNER_NAME')
        );
    }



    public function getVariables($hookName, array $params)
    {
        $currentImage = Configuration::get('FOOTER_BANNER_IMAGE');
        $produitID = Configuration::get('FOOTER_PRODUCT_DISCOUNT');

        $title = Configuration::get('FOOTER_BANNER_TITLE');
        $description = Configuration::get('FOOTER_BANNER_DESCRIPTION');
        $shortDescription = Configuration::get('FOOTER_BANNER_DESCRIPTION_SHORT');
        $produit = new Product($produitID, $this->context->language->id);

        $image_url = $currentImage
            ? $this->context->link->getMediaLink('/modules/' . $this->name . '/views/img/' . $currentImage)
            : '';

        

        return [
            'link' => $this->context->link,
            'image' => $image_url,
            'product' => $produit,
            'title' => $title,
            'description' => $description,
            'shortDescription' => $shortDescription,
        ];
    }

    public function hookDisplayHome($params)
    {
        $variables = $this->getVariables('displayHome', $params);
        $this->smarty->assign($variables);
        return $this->display(__FILE__, 'views/templates/hook/displayHome.tpl');
    }
    public function hookDisplayFooterProduct($params)
    {
        $variables = $this->getVariables('displayHome', $params);
        $this->smarty->assign($variables);
        return $this->display(__FILE__, 'views/templates/hook/displayHome.tpl');
    }
    public function hookDisplayFooterBefore($params)
    {
        $controller = Tools::getValue('controller');
        if($controller != 'index'){
            $variables = $this->getVariables('displayHome', $params);
            $this->smarty->assign($variables);
            return $this->display(__FILE__, 'views/templates/hook/displayHome.tpl');
        }
    
    }

    public function getContent()
    {
        $output = '';

        // This part is executed only when the form is submitted
        if (Tools::isSubmit('submit' . $this->name)) {
            // Retrieve the values set by the user
            $productDiscount = Tools::getValue('FOOTER_PRODUCT_DISCOUNT');
            $title = Tools::getValue('FOOTER_BANNER_TITLE');
            $description = Tools::getValue('FOOTER_BANNER_DESCRIPTION');
            $shortDescription = Tools::getValue('FOOTER_BANNER_DESCRIPTION_SHORT');
            $imageBanner = Tools::getValue('FOOTER_BANNER_IMAGE');

            if (empty($productDiscount) && empty($title) && empty($shortDescription) ) {
                $output = $this->displayError($this->l('Invalid info'));
            } else {
                Configuration::updateValue('FOOTER_PRODUCT_DISCOUNT', $productDiscount);
                Configuration::updateValue('FOOTER_BANNER_TITLE', $title);
                Configuration::updateValue('FOOTER_BANNER_DESCRIPTION', $description);
                Configuration::updateValue('FOOTER_BANNER_DESCRIPTION_SHORT', $shortDescription);
            }

            if (isset($_FILES['FOOTER_BANNER_IMAGE']) && $_FILES['FOOTER_BANNER_IMAGE']['error'] === UPLOAD_ERR_OK) {
                $imageBanner = $_FILES['FOOTER_BANNER_IMAGE'];

                // Check if the upload directory exists, otherwise create it
                $uploadDir = _PS_MODULE_DIR_ . $this->name . '/views/img/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $imageName = Tools::safeOutput($imageBanner['name']);
                $imagePath = $uploadDir . $imageName;

                // Move the uploaded file to the desired directory
                if (move_uploaded_file($imageBanner['tmp_name'], $imagePath)) {
                    Configuration::updateValue('FOOTER_BANNER_IMAGE', $imageName);
                } else {
                    $output .= $this->displayError($this->l('Failed to upload image'));
                }
            } elseif (isset($_FILES['FOOTER_BANNER_IMAGE']) && $_FILES['FOOTER_BANNER_IMAGE']['error'] !== UPLOAD_ERR_NO_FILE) {
                $output .= $this->displayError($this->l('Invalid Image Banner'));
            }


            if (empty($output)) {
                $output = $this->displayConfirmation($this->l('Successfully did'));
            }
        }

        return $output . $this->displayForm();
    }


    public function displayForm()
    {
        $products = $this->getDiscountProducts();
        $currentImage = Configuration::get('FOOTER_BANNER_IMAGE');
        $image_url = $currentImage ? $this->context->link->getMediaLink('/modules/' . $this->name . '/views/img/' . $currentImage) : '';
        $image = '<div class="col-lg-6"><img src="' . $image_url . '" class="img-thumbnail" width="400"></div>';

        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Footer Banner Selection'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Banner Title'),
                        'name' => 'FOOTER_BANNER_TITLE',
                        'required' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Banner Description'),
                        'name' => 'FOOTER_BANNER_DESCRIPTION',
                        'required' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Banner Short Description'),
                        'name' => 'FOOTER_BANNER_DESCRIPTION_SHORT',
                        'required' => false,
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Choose Product'),
                        'name' => 'FOOTER_PRODUCT_DISCOUNT',
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
                        'name' => 'FOOTER_BANNER_IMAGE',
                        'display_image' => true,
                        'image' => $image,
                        'required' => false,
                    ],

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

        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');

        $currentImage = Configuration::get('FOOTER_BANNER_IMAGE');
        if ($currentImage) {
            $helper->fields_value['FOOTER_BANNER_IMAGE'] = $this->context->link->getMediaLink(_PS_MODULE_DIR_ . $this->name . '/views/img/' . $currentImage);
        } else {
            $helper->fields_value['FOOTER_BANNER_IMAGE'] = '';
        }

        $helper->fields_value['FOOTER_PRODUCT_DISCOUNT'] = Tools::getValue('FOOTER_PRODUCT_DISCOUNT', Configuration::get('FOOTER_PRODUCT_DISCOUNT'));
        $helper->fields_value['FOOTER_BANNER_TITLE'] = Tools::getValue('FOOTER_BANNER_TITLE', Configuration::get('FOOTER_BANNER_TITLE'));
        $helper->fields_value['FOOTER_BANNER_DESCRIPTION'] = Tools::getValue('FOOTER_BANNER_DESCRIPTION', Configuration::get('FOOTER_BANNER_DESCRIPTION'));
        $helper->fields_value['FOOTER_BANNER_DESCRIPTION_SHORT'] = Tools::getValue('FOOTER_BANNER_DESCRIPTION_SHORT', Configuration::get('FOOTER_BANNER_DESCRIPTION_SHORT'));

        return $helper->generateForm([$form]);
    }

    public function getDiscountProducts()
    {
        $sql = 'SELECT pl.*
        FROM ' . _DB_PREFIX_ . 'product p
        INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON p.id_product = pl.id_product';
        return Db::getInstance()->executeS($sql);
    }
}

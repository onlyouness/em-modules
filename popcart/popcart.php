<?php
if (! defined('_PS_VERSION_')) {
    exit;
}

class PopCart extends Module
{
    public function __construct()
    {
        $this->name          = 'popcart';
        $this->tab           = 'administration';
        $this->version       = '1.0.0';
        $this->author        = 'Youness Major Media';
        $this->need_instance = 0;
        $this->bootstrap     = true;

        parent::__construct();

        $this->displayName = $this->l('Pop up Cart');
        $this->description = $this->l('allows to check the cart price compare to a config price');
    }

    public function install()
    {
        return parent::install() && Configuration::updateValue('CART_LOWEST_PRICE',500) && $this->registerhook('displayHeader') && $this->registerHook('displayFooter');
    }

    public function uninstall()
    {
        return parent::uninstall() && Configuration::deleteByName('CART_LOWEST_PRICE');
    }

    public function hookDisplayHeader()
    {
        $context = Context::getContext();
        $adminLowestPrice = Configuration::get('CART_LOWEST_PRICE');
        $defaultCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $cartCurrency = new Currency($context->cart->id_currency);
        $convertedLowestPrice = Tools::convertPriceFull($adminLowestPrice, $defaultCurrency, $cartCurrency);

        $this->context->controller->addJS($this->_path . 'views/js/popcart.js', 'all');
        Media::addJsDef([
            'popcartprice'=> $convertedLowestPrice,
        ]);
    }
    public function hookDisplayFooter()
    {
        $context = Context::getContext();
        $adminLowestPrice = Configuration::get('CART_LOWEST_PRICE');
        $defaultCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $cartCurrency = new Currency($context->cart->id_currency);
        $convertedLowestPrice = Tools::convertPriceFull($adminLowestPrice, $defaultCurrency, $cartCurrency);
        
        $this->smarty->assign([
            "cartCurrency"=> $defaultCurrency,
            'lowest_price'=>$convertedLowestPrice,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/popup.tpl');
    }
    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit' . $this->name)) {
            $lowestPrice = Tools::getValue('CART_LOWEST_PRICE');

            if (empty($lowestPrice)) {
                $output = $this->displayError($this->l('Invalid info'));
            } else {
                Configuration::updateValue('CART_LOWEST_PRICE', $lowestPrice);
            }
            if (empty($output)) {
                $output = $this->displayConfirmation($this->l('Successfully did'));
            }
        }

        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Lowest Cart'),
                ],
                'input'  => [
                    [
                        'type'     => 'text',
                        'label'    => $this->l('Lowest Price'),
                        'name'     => 'CART_LOWEST_PRICE',
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

        $helper->fields_value['CART_LOWEST_PRICE'] = Tools::getValue('CART_LOWEST_PRICE', Configuration::get('CART_LOWEST_PRICE'));
        return $helper->generateForm([$form]);
    }
}

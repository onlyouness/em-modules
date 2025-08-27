<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Shipping extends Module
{
    public function __construct()
    {
        $this->name = 'shipping';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'Your Name';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->trans('Custom Carrier Price', [], 'Modules.CustomCarrierPrice.Admin');
        $this->description = $this->trans('Override carrier price using hook.', [], 'Modules.CustomCarrierPrice.Admin');
    }

    public function install()
    {
        return parent::install() && $this->registerHook('actionCarrierProcess');
    }

    public function hookActionCarrierProcess($params)
    {
        // Get the carrier ID
        $carrierId = $params['cart']->id_carrier;

        // Define new price (example: Add 5€)
        $new_price = $params['shipping_cost'] + 5;

        // Apply the new price
        $params['shipping_cost'] = $new_price;

        return $params;
    }
}
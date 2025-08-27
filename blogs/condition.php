<?php
class SellerConditionsConditionModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $this->ajax = true;
        $data = Tools::getValue('condition_seller');
        die(Tools::jsonEncode(['result' => 'ok', 'data' => $data]));

    }
}

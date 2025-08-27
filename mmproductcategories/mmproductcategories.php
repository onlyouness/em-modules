<?php 

declare(strict_types=1);
if (!defined('_PS_VERSION_')) {
    exit;
}

class MmProductCategories extends Module{
    public function __construct()
    {
        $this->name = 'mmproductcategories';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Major Media Youness';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('MM Product Categories and SKU', [], 'Modules.MmProductCategories.Admin');
        $this->description = $this->trans('Displays the categories and SKU of a product', [], 'Modules.MmProductCategories.Admin');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.MmProductCategories.Admin');

    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return (
            parent::install() && $this->registerHook('displayProductCategory') && $this->registerHook('displayHeader')
        );
    }

    public function uninstall()
    {
        return (
            parent::uninstall()
        
        );
    }

    public function hookDisplayProductCategory($params){
        $product_id = $params['product'];

        $categories = Product::getProductCategoriesFull($product_id,$this->context->language->id);

        //get the product Ref
        $query = new DbQuery();
        $query->select('p.reference');
        $query->from('product', 'p');
        $query->where('p.id_product = ' . (int)$product_id );


        $ref= Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

        // Tools::dieObject($categories);
        $this->smarty->assign([
            'categories'=>$categories,
            'ref'=> $ref
        ]);

        return $this->display(__FILE__, 'views/templates/hook/category.tpl');
    }
    public function hookDisplayHeader(){
        $this->context->controller->addCSS($this->_path.'views/css/productcategory.css','all');
    }

}
<?php
declare(strict_types = 1);
if(!defined('_PS_VERSION_')){
    exit;
}
use Hp\Mmbanners\Install\Installer;

require_once __DIR__.'/vendor/autoload.php';

class MmBanners extends Module
{
    public function __construct()
    {
        $this->name = 'mmbanners';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'youness major media';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('MM Banners');
        $this->description = $this->l('Allows you to manage your banners.');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }


    public function install()
    {
        if(!parent::install()){
            return;
        }
        $installer = new Installer();
        return $installer->install($this);
    }
    public function uninstall()
    {
        $installer = new Installer();
        return  $installer->uninstall() && parent::uninstall();
    }
    public function hookDisplayHome()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $banners = $em->getRepository(\Hp\Mmbanners\Entity\Banner::class)->findBy(['active' => 1]);
        $bannerData = [];

        foreach ($banners as $banner) {
            $lang = $this->context->language->id;
            $product = new Product($banner->getProduct(),false,$lang);
            $link = new Link();
            $url = $link->getProductLink($product);
            $bannerData[] = [
                'id' => $banner->getId(),
                'title' => $banner->getTitle(),
                'image' => '/modules/mmbanners/img/'.$banner->getImage(),
                'description' => $banner->getDescription(),
                'shortDescription' => $banner->getShortDescription(),
                'url'=>$url,
                'product_name'=> $product->name,
            ];
        }
//        Tools::dieObject($bannerData);
        $this->context->smarty->assign(['banners' => (array)$bannerData]);


        return $this->display(__FILE__, 'views/templates/hook/home.tpl');

    }
    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path.'views/css/mmbanner.css', 'all');
    }

}
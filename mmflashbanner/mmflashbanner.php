<?php
declare(strict_types = 1);
if(!defined('_PS_VERSION_')){
    exit;
}

use Hp\MmFlashBanner\Install\Installer;

require_once __DIR__.'/vendor/autoload.php';

class MmFlashBanner extends Module
{
    public function __construct()
    {
        $this->name = 'mmflashbanner';
        $this->tab = 'administration';
        $this->version = '1.1.0';
        $this->author = 'youness major media';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('MM Flash Banners');
        $this->description = $this->l('Allows to manage flash Ventes .');

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
        $banners = $em->getRepository(\Hp\MmFlashBanner\Entity\Banner::class)->findBy(['active' => 1]);
        $header = $em->getRepository(\Hp\MmFlashBanner\Entity\Section::class)->find(1);
        $bannerData = [];
        $headerData= [];
        if(!empty($banners)){
        $headerData = [
            'header_title' => $header->getTitle(),
            'header_description'=> $header->getDescription(),
            'header_shortDescription'=> $header->getShortDescription(),
        ];

        foreach ($banners as $banner) {
            $lang = $this->context->language->id;
            $category = new Category($banner->getCategory(),false,$lang);
            $link = new Link();
            $url = $link->getCategoryLink($category);

            // != start adding the br attribute after the first word
            $titleValue = explode(' ',$banner->getTitle()); //convert the string to array
            array_splice( $titleValue, 1, 0, '<br>' ); //add the br after the first word
            $title = implode(" ",$titleValue); // convert the array to string again
            //end adding the br attribute

            $bannerData[] = [
                'id' => $banner->getId(),
                'title' => $title,
                'image' => '/modules/mmflashbanner/img/'.$banner->getImage(),
                'description' => $banner->getDescription(),
                'url'=>$url,
                'category_name'=> $category->name,
            ];
        }
        }

        $this->context->smarty->assign(['banners' => (array)$bannerData,'header' => $headerData]);


        return $this->display(__FILE__, 'views/templates/hook/home.tpl');
    }
    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path.'views/css/mmflashbanner.css', 'all');
    }

}
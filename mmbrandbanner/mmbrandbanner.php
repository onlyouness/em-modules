<?php
declare (strict_types = 1);
if (! defined('_PS_VERSION_')) {
    exit;
}

use Hp\Mmbrandbanner\Install\Installer;

require_once __DIR__ . '/vendor/autoload.php';

class Mmbrandbanner extends Module
{
    public function __construct()
    {
        $this->name          = 'mmbrandbanner';
        $this->tab           = 'administration';
        $this->version       = '1.1.0';
        $this->author        = 'youness major media';
        $this->need_instance = 0;
        $this->bootstrap     = true;
        parent::__construct();
        $this->displayName            = $this->l('MM Brand Banners');
        $this->description            = $this->l('Allows to manage brand banners .');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        if (! parent::install()) {
            return;
        }
        $installer = new Installer();
        return $installer->install($this);
    }
    public function uninstall()
    {
        $installer = new Installer();
        return $installer->uninstall() && parent::uninstall();
    }
    public function hookDisplayHome()
    {
        $em         = $this->get('doctrine.orm.entity_manager');
        $banners    = $em->getRepository(\Hp\Mmbrandbanner\Entity\BrandBanner::class)->findBy(['active' => 1]);
        $bannerData = [];
        if (! empty($banners)) {
            foreach ($banners as $banner) {
                $lang     = $this->context->language->id;
                $brand = new Manufacturer($banner->getManufacturer(), false, $lang);
                $link     = new Link();
            

                                                                 // != start adding the br attribute after the first word
                $titleValue = explode(' ', $banner->getTitle()); //convert the string to array
                array_splice($titleValue, 1, 0, '<br>');         //add the br after the first word
                $title = implode(" ", $titleValue);              // convert the array to string again
                                                                 //end adding the br attribute

                $bannerData[] = [
                    'id'            => $banner->getId(),
                    'title'         => $title,
                    'image'         => '/modules/mmflashbanner/img/' . $banner->getImage(),
                    'description'   => $banner->getDescription(),
                    'category_name' => $brand->name,
                ];
            }
        }

        $this->context->smarty->assign(['banners' => (array) $bannerData]);

        return $this->display(__FILE__, 'views/templates/hook/home.tpl');
    }
    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/mmbrandbanner.css', 'all');
    }

}

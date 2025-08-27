<?php

use Hp\Collectionimages\Entity\QbCollection;
use Hp\Collectionimages\Entity\QbCollectionImage;

class CollectionImages extends Module
{

    public function __construct()

    {
        $this->name = 'collectionimages';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Youness Major media';
        parent::__construct();
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;
        $this->displayName = $this->l('Collection of images', 'collectionimages');
        $this->description = $this->l('Allows you to create collection of images as a slider');
    }

    public function install()
    {
        return parent::install() && $this->installDb()  && $this->installTab() &&
            $this->registerHook('displayHome');
    }

    public function uninstall()
    {

        return $this->uninstallDb()  && $this->uninstallTab() && parent::uninstall();
    }
    public function installDb()
    {
        $queries = [];
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'qb_collection` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `url` varchar(250) 
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'qb_collection_image` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `id_collection` INT,
            `image_path` varchar(250) 
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';
        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }
    public function uninstallDb()
    {
        $queries = [];
        $queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'qb_collection`';
        $queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'qb_collection_image`';
        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }

    protected function installTab(): bool
    {
        $languages = Language::getLanguages();
        $parentTabClassName = 'AdminCollectionImages'; // Unique class name for parent tab
        if (!Tab::getIdFromClassName($parentTabClassName)) {
            $parentTab = new Tab();
            $parentTab->active = true;
            $parentTab->enabled = true;
            $parentTab->module = $this->name;
            $parentTab->class_name = $parentTabClassName;
            $parentTab->id_parent = 0; // Root tab (no parent)

            foreach ($languages as $language) {
                $parentTab->name[$language['id_lang']] = 'Images Collection'; // Display name for parent tab
            }

            $parentTab->icon = 'settings'; // Optional icon for the parent tab
            $parentTab->wording = 'Images Collection';
            $parentTab->wording_domain = 'Modules.CollectionImages.Admin';

            try {
                $parentTab->save();
            } catch (Exception $e) {
                PrestaShopLogger::addLog('Error creating parent tab: ' . $e->getMessage(), 3);
                return false;
            }
        } else {
            $parentTab = new Tab(Tab::getIdFromClassName($parentTabClassName));
        }
        $exists = Tab::getIdFromClassName('AdminCollection');
        if (!$exists) {
            $tab = new Tab();
            $tab->active = true;
            $tab->enabled = true;
            $tab->module = $this->name;
            $tab->class_name = 'AdminCollection';
            $tab->id_parent = $parentTab->id;
            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = 'Collection';
            }
            $tab->icon =  '';
            $tab->wording = 'Collection';
            $tab->wording_domain = 'Modules.AdminCollection.Admin';
            try {
                $tab->save();
            } catch (Exception $e) {
                PrestaShopLogger::addLog('Error tab: ' . $e->getMessage(), 3);
                return false;
            }
        }
        return true;
    }

    protected function unInstallTab(): bool
    {
        $id = Tab::getIdFromClassName('AdminCollection');
        if ($id) {
            $tab = new Tab($id);
            $tab->delete();
        }
        return true;
    }

    public function hookDisplayHome(){
        $em = $this->get('doctrine.orm.entity_manager');
        $collections = $em->getRepository(QbCollection::class)->findAll();
        $collectionData= [];
        foreach ($collections as $collection){
            $collectionId = $collection->getId();
            $images = $em->getRepository(QbCollectionImage::class)->findBy(['collection' => $collectionId]);
            // $collectionData[] = $
            $imagesData = [];
            foreach($images as $image){
                $imagesData[]= $image->getImage();
            }

            $collectionData[] = [
                'id' => $collection->getId(),
                'url'=>$collection->getUrl(),
                'images'=>$imagesData,
            ];
        }
        $this->context->smarty->assign(array('homeslider_slides' => $collectionData));

        return $this->display(__FILE__, 'slider.tpl');

    }
}

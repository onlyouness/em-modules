<?php

namespace Hp\Mmbanners\Install;

use Db;
use Module;
use Language;
use Tab;
use PrestaShopLogger;
use Exception;

class Installer
{
    private $tabs=[
        [
            'class_name'=> 'BannerController',
            'name'=> 'MM Banners',
            'icon' => '',
            'wording'=>'Manager Banners',
            'wording_domain'=> 'Modules.MmBanners.Admin'
        ]
    ];

    public function install(Module $module){
        if(!$this->installTab($module)){
            return false;
        }
        if(!$this->registerHooks($module)){
            return false;
        }
        if(!$this->installDb()){
            return false;
        }

        return true;
    }

    public function uninstall(){

        return $this->uninstallDb() && $this->unInstallTab();

    }
    public function installDb (){
        return $this->executeQueries(Database::installQueries());
    }

    public function uninstallDb (){
        return $this->executeQueries(Database::unInstallQueries());
    }
    public function executeQueries($queries):bool
    {
        if(empty($queries)) {
            return true;
        }
        foreach ($queries as $query){
            if(!Db::getInstance()->execute($query)){
                return false;
            }
        }
        return true;
    }
    public function registerHooks(Module $module){
        $hooks = [
            'displayHome',
            'displayHeader'
        ];
        return (bool) $module->registerHook($hooks);
    }

    protected function installTab(Module $module) :bool
    {
        $languages = Language::getLanguages();
        $parentTabClassName = 'AdminBanners'; // Unique class name for parent tab
        if (!Tab::getIdFromClassName($parentTabClassName)) {
            $parentTab = new Tab();
            $parentTab->active = true;
            $parentTab->enabled = true;
            $parentTab->module = $module->name;
            $parentTab->class_name = $parentTabClassName;
            $parentTab->id_parent = 0; // Root tab (no parent)

            foreach ($languages as $language) {
                $parentTab->name[$language['id_lang']] = 'Banner Manager'; // Display name for parent tab
            }

            $parentTab->icon = 'settings'; // Optional icon for the parent tab
            $parentTab->wording = 'Banners Manager';
            $parentTab->wording_domain = 'Modules.MmBanner.Admin';

            try {
                $parentTab->save();
            } catch (Exception $e) {
                PrestaShopLogger::addLog('Error creating parent tab: ' . $e->getMessage(), 3);
                return false;
            }
        } else {
            $parentTab = new Tab(Tab::getIdFromClassName($parentTabClassName));
        }
        foreach ($this->tabs as $t) {
            $exists = Tab::getIdFromClassName($t['class_name']);
            if (!$exists) {
                $tab = new Tab();
                $tab->active = true;
                $tab->enabled = true;
                $tab->module = $module->name;
                $tab->class_name = $t['class_name'];
                $tab->id_parent = $parentTab->id;
                foreach ($languages as $language) {
                    $tab->name[$language['id_lang']] = $t['name'];
                }
                $tab->icon = $t['icon'];
                $tab->wording = $t['wording'];
                $tab->wording_domain = $t["wording_domain"];
                try {
                    $tab->save();
                } catch (Exception $e) {
                    PrestaShopLogger::addLog('Error tab: ' . $e->getMessage(), 3);
                    return false;
                }
            }
        }
        return true;
    }

    protected function unInstallTab():bool{
        foreach($this->tabs as $t){
            $id = Tab::getIdFromClassName($t['class_name']);
            if($id){
                $tab = new Tab($id);
                $tab->delete();
            }
        }
        return true;
    }
}
<?php

namespace Hp\Mmbrandbanner\Install;

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
            'class_name'=> 'AdminBrandController',
            'name'=> 'MM Brand Banner',
            'icon' => '',
            'wording'=>'Manager Brand Banners',
            'wording_domain'=> 'Modules.Mmbrandbanner.Admin'
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
        $parentTabId = Tab::getIdFromClassName('AdminMM');

        if (!$parentTabId) {
            PrestaShopLogger::addLog('Parent tab AdminMM does not exist.', 3);
            return false;
        }
        $languages = Language::getLanguages();
        foreach ($this->tabs as $t) {
            $exists = Tab::getIdFromClassName($t['class_name']);
            if (!$exists) {
                $tab = new Tab();
                $tab->active = true;
                $tab->enabled = true;
                $tab->module = $module->name;
                $tab->class_name = $t['class_name'];
                $tab->id_parent = $parentTabId;
                foreach ($languages as $language) {
                    $tab->name[$language['id_lang']] = $t['name'];
                }
                $tab->icon = $t['icon'];
                $tab->wording = $t['wording'];
                $tab->wording_domain = $t["wording_domain"];
                $tab->save();
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
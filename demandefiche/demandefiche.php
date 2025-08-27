<?php

class DemandeFiche extends Module
{

    public function __construct()
    {
        $this->name    = 'demandefiche';
        $this->tab     = 'front_office_features';
        $this->version = '1.0.0';
        $this->author  = 'Youness Major media';
        parent::__construct();
        $this->need_instance          = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];
        $this->bootstrap              = true;
        $this->displayName            = $this->l('Demande Fiche', 'demandefiche');
        $this->description            = $this->l('Allows you to display the files and demandes');
    }

    public function install()
    {
        return parent::install() && $this->installTab();
    }

    public function uninstall()
    {
        return $this->uninstallTab() && parent::uninstall();
    }

    protected function installTab(): bool
    {
        $languages          = Language::getLanguages();
        $parentTabClassName = 'AdminDemandeFiche'; // Unique class name for parent tab
        if (! Tab::getIdFromClassName($parentTabClassName)) {
            $parentTab             = new Tab();
            $parentTab->active     = true;
            $parentTab->enabled    = true;
            $parentTab->module     = $this->name;
            $parentTab->class_name = $parentTabClassName;
            $parentTab->id_parent  = 0; // Root tab (no parent)

            foreach ($languages as $language) {
                $parentTab->name[$language['id_lang']] = 'Demandes'; // Display name for parent tab
            }

            $parentTab->icon           = 'settings'; // Optional icon for the parent tab
            $parentTab->wording        = 'Demande';
            $parentTab->wording_domain = 'Modules.DemandeFiche.Admin';

            try {
                $parentTab->save();
            } catch (Exception $e) {
                PrestaShopLogger::addLog('Error creating parent tab: ' . $e->getMessage(), 3);
                return false;
            }
        } else {
            $parentTab = new Tab(Tab::getIdFromClassName($parentTabClassName));
        }
        $exists = Tab::getIdFromClassName('AdminDemandeFicheController');
        if (! $exists) {
            $tab             = new Tab();
            $tab->active     = true;
            $tab->enabled    = true;
            $tab->module     = $this->name;
            $tab->class_name = 'AdminDemandeFicheController';
            $tab->id_parent  = $parentTab->id;
            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = 'Demande';
            }
            $tab->icon           = '';
            $tab->wording        = 'Demande';
            $tab->wording_domain = 'Modules.DemadeFiche.Admin';
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
        $ids = [
            'AdminDemandeFiche',
            'AdminDemandeFicheController',
        ];
        foreach ($ids as $i) {
            $id = Tab::getIdFromClassName($i);
            if ($id) {
                $tab = new Tab($id);
                $tab->delete();
            }
        }
        return true;
    }
}

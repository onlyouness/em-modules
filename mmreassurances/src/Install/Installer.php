<?php
namespace Hp\Mmreassurances\Install;

use Db;
use Exception;
use Language;
use Module;
use PrestaShopLogger;
use Tab;

class Installer
{
    private $tabs = [
        [
            'class_name'     => 'AdminReassurances',
            'name'           => 'MM Reassurances',
            'icon'           => '',
            'wording'        => 'Gestion des Reassurances',
            'wording_domain' => 'Modules.Mmreassurances.Admin',
        ],
    ];

    public function install(Module $module)
    {
        if (! $this->installTab($module)) {
            return false;
        }
        if (! $this->registerHooks($module)) {
            return false;
        }
        if (! $this->installDb()) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {

        return $this->uninstallDb() && $this->unInstallTab();

    }
    public function installDb()
    {
        return $this->executeQueries(Database::installQueries());
    }

    public function uninstallDb()
    {
        return $this->executeQueries(Database::unInstallQueries());
    }
    public function executeQueries($queries): bool
    {
        if (empty($queries)) {
            return true;
        }
        foreach ($queries as $query) {
            if (! Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }
    public function registerHooks(Module $module)
    {
        $hooks = [
            'displayHome',
            'displayHeader',
        ];
        return (bool) $module->registerHook($hooks);
    }

    protected function installTab(Module $module): bool
    {
        $languages          = Language::getLanguages();
        $parentTabClassName = 'AdminMMConfig'; // Unique class name for parent tab
        if (! Tab::getIdFromClassName($parentTabClassName)) {
            $parentTab             = new Tab();
            $parentTab->active     = true;
            $parentTab->enabled    = true;
            $parentTab->module     = $module->name;
            $parentTab->class_name = $parentTabClassName;
            $parentTab->id_parent  = 0; // Root tab (no parent)

            foreach ($languages as $language) {
                $parentTab->name[$language['id_lang']] = 'MM Management'; // Display name for parent tab
            }

            $parentTab->icon           = 'settings'; // Optional icon for the parent tab
            $parentTab->wording        = 'MM Management';
            $parentTab->wording_domain = 'Modules.Mmreassurances.Admin';

            try {
                $parentTab->save();
            } catch (Exception $e) {
                PrestaShopLogger::addLog('Error creating parent tab: ' . $e->getMessage(), 3);
                return false;
            }
        } else {
            $parentTab = new Tab(Tab::getIdFromClassName($parentTabClassName));
        }

        $superTabName = 'AdminMM';
        if (! Tab::getIdFromClassName($superTabName)) {
            $superTab             = new Tab();
            $superTab->active     = true;
            $superTab->enabled    = true;
            $superTab->module     = $module->name;
            $superTab->class_name = $superTabName;
            $superTab->id_parent  = $parentTab->id;
            foreach ($languages as $language) {
                $superTab->name[$language['id_lang']] = 'MM Management'; // Display name for parent tab
            }

            $superTab->icon           = 'settings'; // Optional icon for the parent tab
            $superTab->wording        = 'MM Management';
            $superTab->wording_domain = 'Modules.Mmreassurances.Admin';

            try {
                $superTab->save();
            } catch (Exception $e) {
                PrestaShopLogger::addLog('Error creating parent tab: ' . $e->getMessage(), 3);
                return false;
            }
        } else {
            $superTab = new Tab(Tab::getIdFromClassName($superTabName));
        }

        foreach ($this->tabs as $t) {
            $exists = Tab::getIdFromClassName($t['class_name']);
            if (! $exists) {
                $tab             = new Tab();
                $tab->active     = true;
                $tab->enabled    = true;
                $tab->module     = $module->name;
                $tab->class_name = $t['class_name'];
                $tab->id_parent  = $superTab->id;

                foreach ($languages as $language) {
                    $tab->name[$language['id_lang']] = $t['name'];
                }
                $tab->icon           = $t['icon'];
                $tab->wording        = $t['wording'];
                $tab->wording_domain = $t["wording_domain"];
                $tab->save();

            }
        }
        return true;
    }

    protected function unInstallTab(): bool
    {
        $idParent = Tab::getIdFromClassName('AdminMM');
        if ($idParent) {
            $tab = new Tab($idParent);
            $tab->delete();
        }
        foreach ($this->tabs as $t) {
            $id = Tab::getIdFromClassName($t['class_name']);
            if ($id) {
                $tab = new Tab($id);
                $tab->delete();
            }
        }
        return true;
    }

}

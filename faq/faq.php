<?php

use Hp\Faq\Entity\Faq as EntityFaq;

class Faq extends Module
{

    public function __construct()
    {
        $this->name = 'faq';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Youness Major media';
        parent::__construct();
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;
        $this->displayName = $this->l('FAQ', 'Faq');
        $this->description = $this->l('Allows you to create faq');
    }

    public function install()
    {
        return parent::install() && $this->installDb() && $this->installTab() &&
        $this->registerHook('displayHome') && $this->registerHook('displayHeader');
    }

    public function uninstall()
    {

        return $this->uninstallDb() && $this->uninstallTab() && parent::uninstall();
    }
    public function installDb()
    {
        $queries = [];
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'faq` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `group_id` INT ,
            `section_id` INT ,
            `active` INT default 1
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';
        $queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'faq_lang` (
            `id_faq` INT,
            `id_lang` INT,
            `question` varchar(250) ,
            `response` varchar(250) ,
            primary key(`id_faq`,`id_lang`)
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
        $queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'faq`';
        $queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'faq_lang`';
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
        $parentTabClassName = 'AdminFaqTab'; // Unique class name for parent tab
        if (!Tab::getIdFromClassName($parentTabClassName)) {
            $parentTab = new Tab();
            $parentTab->active = true;
            $parentTab->enabled = true;
            $parentTab->module = $this->name;
            $parentTab->class_name = $parentTabClassName;
            $parentTab->id_parent = 0; // Root tab (no parent)

            foreach ($languages as $language) {
                $parentTab->name[$language['id_lang']] = 'FAQ'; // Display name for parent tab
            }

            $parentTab->icon = 'settings'; // Optional icon for the parent tab
            $parentTab->wording = 'FAQ';
            $parentTab->wording_domain = 'Modules.AdminFaq.Admin';

            try {
                $parentTab->save();
            } catch (Exception $e) {
                PrestaShopLogger::addLog('Error creating parent tab: ' . $e->getMessage(), 3);
                return false;
            }
        } else {
            $parentTab = new Tab(Tab::getIdFromClassName($parentTabClassName));
        }
        $exists = Tab::getIdFromClassName('AdminFaq');
        if (!$exists) {
            $tab = new Tab();
            $tab->active = true;
            $tab->enabled = true;
            $tab->module = $this->name;
            $tab->class_name = 'AdminFaq';
            $tab->id_parent = $parentTab->id;
            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = 'FAQ';
            }
            $tab->icon = '';
            $tab->wording = 'FAQ';
            $tab->wording_domain = 'Modules.AdminFaq.Admin';
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
            'AdminFaqTab',
            'AdminFaq',
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
    public function hookDisplayHome($params)
    {
        $em = $this->get('doctrine.orm.entity_manager'); // Doctrine entity manager
        $faqs = [];
        $id_customer = null;
        $langId = $this->context->language->id;
        if ($this->context->customer->isLogged()) {
            $id_customer = $this->context->customer->id;
            
            $sql = new DbQuery();
            $sql->select('id_group');
            $sql->from('customer_group', 'l');
            $sql->Where('l.id_customer = ' . (int) $id_customer);
            $group = Db::getInstance()->getValue($sql); // Get the group id of the customer
            
            $faqs = $em->getRepository(EntityFaq::class)->findFaqsByLangAndActiveAndGroup( $langId,$group);
        } else {
            // Tools::dieObject(['langId'=>(int)$langId, 'group' => 1]);
            $faqs = $em->getRepository(EntityFaq::class)->findFaqsByLangAndActiveAndGroup((int)$langId, 1);
        }
    
        // Group FAQs by section
        $faqDatas = [
            1 => [],
            2 => []
        ];
        foreach ($faqs as $faq) {
            $faqData = [
                'id' => $faq['id'],
                'active' => $faq['active'],
                'question' => $faq['question'],
                'response' => $faq['response'],
                'section' => $faq['section'],
            ];
    
            // Group by section
            if ($faqData['section'] == 1) {
                $faqDatas[1][] = $faqData;
            } elseif ($faqData['section'] == 2) {
                $faqDatas[2][] = $faqData;
            }
        }

        $this->context->smarty->assign([
            'faqs_section_1' => $faqDatas[1], // Section 1 FAQs
            'faqs_section_2' => $faqDatas[2], // Section 2 FAQs
        ]);
        
    
        return $this->display(__FILE__, 'views/templates/hook/home.tpl');
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path.'views/css/faq.css', 'all');
        $this->context->controller->addJs($this->_path.'views/js/faq.js', 'all');
    }
    

}

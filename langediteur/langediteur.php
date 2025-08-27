<?php
if (! defined('_PS_VERSION_')) {
    exit;
}

class LangEditeur extends Module
{
    public function __construct()
    {
        $this->name                   = 'langediteur';
        $this->tab                    = 'front_office_features';
        $this->version                = '1.0.0';
        $this->author                 = 'Youness Elbaz';
        $this->need_instance          = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Language Editeur', [], 'Modules.mmproductbanner.Admin');
        $this->description = $this->trans('Allows you to edit the language iso code and langauge code.', [], 'Modules.mmproductbanner.Admin');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.mmproductbanner.Admin');

    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return (
            parent::install()
        );
    }
    public function uninstall()
    {
        return (
            parent::uninstall()
        );
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit' . $this->name)) {
            $lang = Tools::getValue('MMLANGUAGES');
            $isoLang = Tools::getValue('MMISOCODELANGUAGE');
            $langcode = Tools::getValue('MMCODELANGUAGE');
            $language = new Language($lang);
            $db = DB::getInstance();
            $db->update('lang', array(
                'iso_code' => pSQL($isoLang),
                'language_code' => pSQL($langcode),
            ), 'id_lang = '.$language->id);
        }

        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        $lang      = $this->context->language->id;
        $languages = Language::getLanguages();

        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Language Editeur'),
                ],
                'input'  => [
                    
                    [
                        'type'     => 'select',
                        'label'    => $this->l('Choose Language'),
                        'name'     => 'MMLANGUAGES',
                        'options'  => [
                            'query' => $languages,
                            'id'    => 'id_lang',
                            'name'  => 'name',
                        ],
                        'required' => true,
                    ],
                    [
                        'type'     => 'text',
                        'label'    => $this->l('Enter iso code'),
                        'name'     => 'MMISOCODELANGUAGE',
                        'required' => false,
                    ],
                    [
                        'type'     => 'text',
                        'label'    => $this->l('Enter language code'),
                        'name'     => 'MMCODELANGUAGE',
                        'required' => false,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper                  = new HelperForm();
        $helper->table           = $this->table;
        $helper->name_controller = $this->name;

        $helper->token         = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex  = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        $helper->fields_value['MMLANGUAGES'] = Tools::getValue('MMLANGUAGES', Configuration::get('MMLANGUAGES'));
        $helper->fields_value['MMISOCODELANGUAGE'] = Tools::getValue('MMISOCODELANGUAGE', Configuration::get('MMISOCODELANGUAGE'));
        $helper->fields_value['MMCODELANGUAGE'] = Tools::getValue('MMCODELANGUAGE', Configuration::get('MMCODELANGUAGE'));
        return $helper->generateForm([$form]);
    }
}

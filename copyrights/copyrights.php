<?php

if (! defined('_PS_VERSION_')) {

    exit;

}

class CopyRights extends Module
{

    public function __construct()
    {

        $this->name = 'copyrights';

        $this->tab = 'administration';

        $this->version = '1.0.0';

        $this->author = 'Youness Major Media';

        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Copy Rights Section');

        $this->description = $this->l('Allows you to display the copyrights in the footer');

    }

    public function install()
    {

        return parent::install() && $this->registerHook('displayFooterAfter');

    }

    public function uninstall()
    {

        return parent::uninstall();

    }

    public function hookDisplayFooterAfter()
    {

        $description = Configuration::get('COPY_RIGHT_DESCRIPTION_FOOTER');

        $currentImage = Configuration::get('COPY_RIGHT_IMAGE_FOOTER');

        $image_url = $currentImage ? $this->context->link->getMediaLink('/modules/' . $this->name . '/views/img/' . $currentImage) : '';

        $variables = [

            'description' => $description,

            'image_url'   => $image_url,

            'link'        => $this->context->link,

        ];

        // Tools::dieObject($variables);

        $this->smarty->assign($variables);

        return $this->display(__FILE__, 'views/templates/hook/footerafter.tpl');

    }

    public function getContent()
    {

        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {

            $description = Tools::getValue('COPY_RIGHT_DESCRIPTION_FOOTER', '', true);

            if (empty($description)) {

                $output = $this->displayError($this->l('Invalid Details'));

            } else {

                Configuration::updateValue('COPY_RIGHT_DESCRIPTION_FOOTER', $description);

            }

            if (isset($_FILES['COPY_RIGHT_IMAGE_FOOTER']) && $_FILES['COPY_RIGHT_IMAGE_FOOTER']['error'] === UPLOAD_ERR_OK) {

                $imageBanner = $_FILES['COPY_RIGHT_IMAGE_FOOTER'];

                // Check if the upload directory exists, otherwise create it

                $uploadDir = _PS_MODULE_DIR_ . $this->name . '/img/';

                if (! is_dir($uploadDir)) {

                    mkdir($uploadDir, 0755, true);

                }

                $imageName = Tools::safeOutput($imageBanner['name']);

                $imagePath = $uploadDir . $imageName;
                // Move the uploaded file to the desired directory

                if (move_uploaded_file($imageBanner['tmp_name'], $imagePath)) {

                    Configuration::updateValue('COPY_RIGHT_IMAGE_FOOTER', $imageName);

                } else {

                    $output .= $this->displayError($this->l('Failed to upload image'));

                }

            } elseif (isset($_FILES['COPY_RIGHT_IMAGE_FOOTER']) && $_FILES['COPY_RIGHT_IMAGE_FOOTER']['error'] !== UPLOAD_ERR_NO_FILE) {

                $output .= $this->displayError($this->l('Invalid Image'));

            }

            if (empty($output)) {

                $output = $this->displayConfirmation($this->l('Settings updated'));

            }

        }

        return $output . $this->displayForm();

    }

    public function displayForm()
    {

        $lang = $this->context->language->id;

        $currentImage = Configuration::get('COPY_RIGHT_IMAGE_FOOTER');

        $image_url = $currentImage ? $this->context->link->getMediaLink(_PS_BASE_URL_ . '/modules/' . $this->name . '/img/' . $currentImage) : '';

        $image = '<div class="col-lg-6"><img src="' . $image_url . '" class="img-thumbnail" width="400"></div>';

        $form = [

            'form' => [

                'legend' => [

                    'title' => $this->l('Copy right section'),

                ],

                'input'  => [

                    [

                        'type'         => 'textarea',

                        'label'        => $this->l('Enter the Description Of the section'),

                        'name'         => 'COPY_RIGHT_DESCRIPTION_FOOTER',

                        'autoload_rte' => true, // Enable WYSIWYG editor

                        'required'     => false,

                    ],
                    [
                        'type'          => 'file',
                        'label'         => $this->l('Image'),
                        'name'          => 'COPY_RIGHT_IMAGE_FOOTER',
                        'display_image' => true,
                        'image'         => $image,
                        'required'      => false,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();

        $helper->table = $this->table;

        $helper->name_controller = $this->name;

        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);

        $helper->submit_action = 'submit' . $this->name;

        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        $helper->fields_value['COPY_RIGHT_DESCRIPTION_FOOTER'] = Tools::getValue('COPY_RIGHT_DESCRIPTION_FOOTER', Configuration::get('COPY_RIGHT_DESCRIPTION_FOOTER'));

         $currentImage = Configuration::get('COPY_RIGHT_IMAGE_FOOTER');

        if ($currentImage) {

            $helper->fields_value['COPY_RIGHT_IMAGE_FOOTER'] = $this->context->link->getMediaLink(_PS_MODULE_DIR_ . $this->name . '/views/img/' . $currentImage);

        } else {

            $helper->fields_value['COPY_RIGHT_IMAGE_FOOTER'] = '';

        }

        return $helper->generateForm([$form]);

    }

}

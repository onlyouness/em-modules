<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class ImageImporter extends Module
{
    public function __construct()
    {
        $this->name = 'imageimporter';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Youness Major Media';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Product Image Importer');
        $this->description = $this->l('Import product images automatically based on product references.');
    }

    public function install()
    {
        return parent::install() && 
            Configuration::updateValue('IMAGEIMPORTER_FOLDER', '');
    }

    public function uninstall()
    {
        return parent::uninstall() && 
            Configuration::deleteByName('IMAGEIMPORTER_FOLDER');
    }

    public function getContent()
    {
        $output = '';
        
        // Handle form submission for settings
        if (Tools::isSubmit('submit' . $this->name)) {
            $folder = Tools::getValue('IMAGEIMPORTER_FOLDER');
            Configuration::updateValue('IMAGEIMPORTER_FOLDER', $folder);
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
        
        // Handle import button click
        if (Tools::isSubmit('importImages')) {
            $result = $this->processImageImport();
            $output .= $result;
        }

        return $output . $this->renderForm();
    }

    private function processImageImport()
    {
        $imageDir = Configuration::get('IMAGEIMPORTER_FOLDER');
        $images = glob($imageDir . "/*.png");
        
        if (!$imageDir || !is_dir($imageDir)) {
            return $this->displayError($this->l('Invalid image directory'));
        }
        $supportedExtensions = ['jpg', 'jpeg', 'png', 'gif','avif','webp'];
        $errors = [];
        $success = [];
        $countSuccess = 0;
        $images = glob($imageDir . '/*.*');
        foreach ($images as $imagePath) {
            $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
            $imagePath = preg_replace('/\.[^.]+$/', '.' . $extension, $imagePath);
            if (!in_array($extension, $supportedExtensions)) {
                $errors[] = "Unsupported file type for: " . basename($imagePath);
                continue;
            }

           $reference = str_replace(' ', '-', pathinfo($imagePath, PATHINFO_FILENAME));

            // dump($reference);
            try {
                // Find product by reference
               
                $productId =Product::getIdByReference($reference);
                
                if (!$productId) {
                    //  dump($productId);
                    $errors[] = "No product found for reference: $reference";
                    continue;
                }
               

                $product = new Product($productId);
                if (!Validate::isLoadedObject($product)) {
                    $errors[] = "Could not load product for reference: $reference";
                    continue;
                }
                $image = new Image();
                $image->id_product = $productId;
                $image->position = Image::getHighestPosition($productId) + 1;
                
                if ($image->add()) {
                    $destinationDir = dirname(_PS_PROD_IMG_DIR_ . $image->getExistingImgPath() . '.' . $extension);
                    if (!file_exists($destinationDir)) {
                        mkdir($destinationDir, 0755, true);
                    }
                    if (file_exists($destinationDir)) {
                        dump('exists with path'.$destinationDir);
                    }
                    if (copy($imagePath, _PS_PROD_IMG_DIR_ . $image->getExistingImgPath() . '.jpg')) {
                        $success[] = "Successfully imported image for reference: $reference";
                        $countSuccess +=1;
                           // Generate different image sizes
                            $imageTypes = ImageType::getImagesTypes('products');
                            foreach ($imageTypes as $imageType) {
                                $resizedImagePath = _PS_PROD_IMG_DIR_ . $image->getExistingImgPath() . '-' . $imageType['name'] . '.jpg';
                                dump($resizedImagePath);
                                if (!ImageManager::resize($imagePathNew, $resizedImagePath, $imageType['width'], $imageType['height'])) {
                                    $errors[] = "Failed to generate " . $imageType['name'] . " for reference: $reference";
                                }
                            }
                    } else {
                        $errors[] = "Failed to copy image file for reference: $reference";
                        $image->delete();
                    }
                } else {
                    $errors[] = "Failed to create image record for reference: $reference";
                }  

            //       $images = Image::getImages($this->context->language->id,$productId);
            //   foreach($images as $image){
            //       $newimage = new Image($image['id_image']);
            //     //   $newimage->delete();
            //       dump($newimage->id_product);
            //   }
            } catch (Exception $e) {
            $errors[] = "Error processing $reference: " . $e->getMessage();
        }
            
        }
                
              
            //   dump($images);
        //  Tools::dieObject($success);

        // Prepare result message
        $html = '<div class="bootstrap">';
        
        if (!empty($success)) {
            $html .= '<div class="alert alert-success">';
            $html .= '<p><strong>' . $this->l('Successful imports') . ' (' . count($success) . '):</strong></p>';
            $html .= '<ul>';
            foreach ($success as $msg) {
                $html .= '<li>' . $msg . '</li>';
            }
            $html .= '</ul></div>';
        }
        
        if (!empty($errors)) {
            $html .= '<div class="alert alert-warning">';
            $html .= '<p><strong>' . $this->l('Errors') . ' (' . count($errors) . '):</strong></p>';
            $html .= '<ul>';
            foreach ($errors as $msg) {
                $html .= '<li>' . $msg . '</li>';
            }
            $html .= '</ul></div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Images Folder Path'),
                        'name' => 'IMAGEIMPORTER_FOLDER',
                        'desc' => $this->l('Absolute path to the folder containing your product images'),
                        'required' => true
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save Settings'),
                    'class' => 'btn btn-default pull-right'
                ),
                'buttons' => array(
                    array(
                        'type' => 'submit',
                        'title' => $this->l('Import Images Now'),
                        'icon' => 'process-icon-download',
                        'name' => 'importImages',
                        'class' => 'btn btn-default pull-right'
                    )
                )
            )
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    protected function getConfigFormValues()
    {
        return array(
            'IMAGEIMPORTER_FOLDER' => Configuration::get('IMAGEIMPORTER_FOLDER'),
        );
    }
}
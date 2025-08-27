<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*/

if (!defined('_PS_VERSION_')) { exit; }
require_once dirname(__FILE__).'/classes/EtsHdProduct.php';

class Ets_hotdeals extends Module
{
    private $errorMessage;
    public $configs;
    public $baseAdminPath;
    private $_html;
    public $is17 = false;
    public $templates;
    public $fields_form;
    protected static $_specificPriceCache = array();
    protected static $_filterOutCache = array();
    protected static $_cache_priorities = array();
    protected static $_no_specific_values = array();
    public $refs;
    public function __construct()
	{
		$this->name = 'ets_hotdeals';
		$this->tab = 'front_office_features';
		$this->version = '1.0.7';
		$this->author = 'PrestaHero';
		$this->need_instance = 0;
		$this->module_key = Tools::encrypt($this->name);
		$this->bootstrap = true;

		parent::__construct();

        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $this->is17 = true; 
        if((string)Tools::substr(sprintf('%o', fileperms(dirname(__FILE__))), -4)!='0755')
            chmod(dirname(__FILE__),0755);
        if((string)Tools::substr(sprintf('%o', fileperms(dirname(__FILE__).'/ajax_products_list.php')), -4)!='0755')
            chmod(dirname(__FILE__).'/ajax_products_list.php',0755);
        $this->displayName = $this->l('Hot deals PRO');
		$this->description = $this->l('Display discounted products in carousel slider with count-down clock');
$this->refs = 'https://prestahero.com/';
		$this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);
        if(isset($this->context->controller->controller_type) && $this->context->controller->controller_type =='admin')
            $this->baseAdminPath = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $optionRows = array(
            array(
                'id' => 1,
                'name' => $this->l('1'),
            ),
            array(
                'id' => 2,
                'name' => $this->l('2'),
            ),
            array(
                'id' => 3,
                'name' => $this->l('3'),
            ),
            array(
                'id' => 4,
                'name' => $this->l('4'),
            ),
            array(
                'id' => 6,
                'name' => $this->l('6'),
            ),
        );
        //Config fields        
        $this->configs = array(
            'ETS_HOTDEALS_TITLE' => array(
                'label' => $this->l('Title'),
                'type' => 'text',                
                'lang' => true,   
                'class'=>'col-lg-4',
                'default' => $this->l('Hot deals'),
                'required' => true,         
                'form_group_class'=>'mw_600'               
            ),
            'ETS_HOTDEALS_SELECT_DISCOUNTED_PRODUCTS' => array(
                'label' => $this->l('Auto select discounted products'),
                'type' => 'switch',
                'default' => 1,
            ),
            'ETS_HOTDEALS_PRODUCT_COUNT' => array(
                'label' => $this->l('Product count'),
                'type' => 'text',
                'default' => '5',
                'suffix' => 'items',
                'class'=>'col-lg-4',
                'form_group_class'=>'product_count mw_600'                          
            ),
            'ETS_HOTDEALS_PRODUCT_IDS'=>array(
                'type'=>'hidden',
            ),
            'ETS_HOTDEALS_SEARCH_PRODUCT_IDS'=>array(
                'label'=> $this->l('Products'),
                'type'=>'text',
                'suffix' => '<i class="icon icon-search"></i>',
                'placeholder'=> $this->l('Search product by id, name, reference'),
                'form_group_class'=>'product_list_id mw_600'
            ),
            'ETS_HOTDEALS_ORDER_PRODUCTS_BY' => array(
                'label' => $this->l('Order products by'),
                'type' => 'radio',
                'values'=>array(
                    array(
                        'id'=>'most_discounted',
                        'value'=>'most_discounted',
                        'label'=>$this->l('Most discounted first'),
                    ),
                    array(
                        'id'=>'new_products_discounted',
                        'value'=>'new_products_discounted',
                        'label'=>$this->l('New products discounted first'),
                    ),
                    array(
                        'id'=>'nearly_expired',
                        'value'=>'nearly_expired',
                        'label'=>$this->l('Nearly expired first'),
                    ),
                    array(
                        'id'=>'random',
                        'value'=>'random',
                        'label'=>$this->l('Random')
                    )
                ),
                'default'=>'most_discounted',    
                'form_group_class'=>'product-order mw_600',                    
            ),
            'ETS_HOTDEALS_DISPLAY_PRODUCT_ATTRIBUTE' =>array(
                'label' => $this->l('Display product attribute'),
                'type' => 'switch',   
                'default'=>1,
            ),
            'ETS_HOTDEALS_DISPLAY_RATING' =>array(
                'label' => $this->l('Display rating'),
                'type' => 'switch',
                'default'=>1,   
            ),
            'ETS_HOTDEALS_DISPLAY_DESCRIPTION' =>array(
                'label' => $this->l('Display description'),
                'type' => 'switch', 
                'default'=>1,  
            ),
            'ETS_HOTDEALS_DISPLAY_DISCOUNTED_AMOUNT' =>array(
                'label' => $this->l('Display discounted amount'),
                'type' => 'switch', 
                'default'=>1,  
            ),     
            'ETS_HOTDEALS_DISPLAY_COUNTDOWN_CLOCK' =>array(
                'label' => $this->l('Display countdown clock'),
                'type' => 'switch', 
                'default'=>1,  
            ),
            'ETS_HOTDEALS_DISPLAY_TYPE' =>array(
                'label' => $this->l('Display type'),
                'type' => 'radio',
                'values'=>array(
                    array(
                        'id'=>'carousel_slider',
                        'value'=>'carousel_slider',
                        'label'=>$this->l('Carousel slider'),
                    ),
                    array(
                        'id'=>'grid_items',
                        'value'=>'grid_items',
                        'label'=>$this->l('Grid items'),
                    ),
                ),
                'default'=>'carousel_slider',   
            ),
            'ETS_HOTDEALS_AUTO_PLAY_SLIDER' =>array(
                'label' => $this->l('Auto play slider'),
                'type' => 'switch',  
                'form_group_class' =>'display_slider' ,
                'default'=>1,
            ),  
            'ETS_HOTDEALS_SPEED' => array(
                'label' => $this->l('Speed'),
                'type' => 'text',
                'default' => '5000',
                'suffix' => 'milliseconds',
                'class'=>'col-lg-4',
                'required' => true,
                'form_group_class'=>'display_slider autoplay_slider',
            ),
            'ETS_HOTDEALS_STOP_WHEN_HOVER'=>array(
                'label'=>$this->l('Stop when hover'),
                'type'=>'switch',
                'form_group_class' =>'display_slider autoplay_slider',
                'default'=>1,
            ),
            'ETS_HOTDEALS_PRODUCT_PER_ROW_DESKTOP' =>array(
                'label' => $this->l('Products per row Desktop'),
                'type' => 'select',
                'default' => '4',
                'class'=>'col-lg-4',
                'options' => array(
                    'id' => 'id',
                    'name' => 'name',
                    'query' => $optionRows
                ),
            ),
            'ETS_HOTDEALS_PRODUCT_PER_ROW_TABLETLARGE' =>array(
                'label' => $this->l('Products per row Tablet Horizontal'),
                'type' => 'select',
                'default' => '3',
                'class'=>'col-lg-4',
                'options' => array(
                    'id' => 'id',
                    'name' => 'name',
                    'query' => $optionRows
                ),
            ),
            'ETS_HOTDEALS_PRODUCT_PER_ROW_TABLET' =>array(
                'label' => $this->l('Products per row Tablet Vertical'),
                'type' => 'select',
                'default' => '2',
                'class'=>'col-lg-4',
                'options' => array(
                    'id' => 'id',
                    'name' => 'name',
                    'query' => $optionRows
                ),
            ),
            'ETS_HOTDEALS_PRODUCT_PER_ROW_MOBILE' =>array(
                'label' => $this->l('Products per row mobile'),
                'type' => 'select',

                'default' => '1',
                'class'=>'col-lg-4',
                'options' => array(
                    'id' => 'id',
                    'name' => 'name',
                    'query' => $optionRows
                ),
            ),
            'ETS_HOTDEALS_HOOK_TO' =>array(
                'label' =>$this->l('Hook to'),
                'type'=>'select',
                'options' => array(
        			 'query' => array(                              
                            array(
                                'id_option' => 'display_home', 
                                'name' => $this->l('Display home')
                            ), 
                            array(
                                'id_option' => 'custom_hook', 
                                'name' => $this->l('Custom hook')
                            )
                        ),                             
                     'id' => 'id_option',
        			 'name' => 'name',
                ),
                'default'=>'display_home',
                'desc' => $this->l('Place “ {hook h = “hotdeal”} ” to tpl file where you want to display the module'),
            )                        
        );        
    }
    /**
	 * @see Module::install()
	 */
    public function install()
	{
	    return parent::install()        
            && $this->registerHook('displayCustomDiscount')
            && $this->registerHook('displayFooter')
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayHome')
            && $this->registerHook('hotdeal')
            && $this->_installDb();
    }
    /**
	 * @see Module::uninstall()
	 */
	public function uninstall()
	{
        return parent::uninstall() && $this->_uninstallDb();
    }    
    public function _installDb()
    {
        $languages = Language::getLanguages(false);
        if($this->configs)
        {
            foreach($this->configs as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values,true);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '',true);
            }
        }        
        return true;
    }    
    private function _uninstallDb()
    {
        if($this->configs)
        {
            foreach($this->configs as $key => $config)
            {
                Configuration::deleteByName($key);
                unset($config);
            }
        } 
        $dirs = array('config');
        foreach($dirs as $dir)
        {
            $files = glob(dirname(__FILE__).'/images/'.$dir.'/*'); 
            foreach($files as $file){ 
              if(is_file($file))
                @unlink($file); 
            }
        }       
        return true;
    }    
    public function getContent()
	{
	   if(Tools::getValue('action')=='updateProductList')
       {
            Configuration::updateValue('ETS_HOTDEALS_PRODUCT_IDS',Tools::getValue('productIds'));
            $actionType = Tools::getValue('type');
            $msg = '';
            if($actionType == 'add'){
                $msg = $this->l('Added product successfully');
            }
            elseif($actionType == 'delete'){
                $msg = $this->l('Deleted product successfully');
            }
            die(json_encode(array(
                'status' => 1,
                'msg' => $msg
            )));
       }
	   $this->_postConfig();       
       //Display errors if have
       if($this->errorMessage)
            $this->_html .= $this->errorMessage;       
       //Render views
       $this->renderConfig(); 
       $this->context->smarty->assign(
            array(
                'ets_hotdeals_module_dir' => $this->_path,
            )
       );
       $products= EtsHdProduct::getHotDealProducts();
       $this->context->smarty->assign('products',$products);
       return $this->_html.$this->display(__FILE__,'admin_js.tpl').$this->displayIframe();
    } 
    public function renderConfig()
    {
        $configs = $this->configs;
        $fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Hot deals'),
					'icon' => 'icon-AdminAdmin'
				),
				'input' => array(),
                'submit' => array(
					'title' => $this->l('Save'),
				)
            ),
		);
        if($configs)
        {
            foreach($configs as $key => $config)
            {
                $confFields = array(
                    'name' => $key,
                    'type' => $config['type'],
                    'label' => isset($config['label'])?$config['label']:'',
                    'placeholder' => isset($config['placeholder'])?$config['placeholder']:'',
                    'desc' => isset($config['desc']) ? $config['desc'] : false,
                    'required' => isset($config['required']) && $config['required'] ? true : false,
                    'autoload_rte' => isset($config['autoload_rte']) && $config['autoload_rte'] ? true : false,
                    'options' => isset($config['options']) && $config['options'] ? $config['options'] : array(),
                    'suffix' => isset($config['suffix']) && $config['suffix'] ? $config['suffix']  : false,
                    'class' => isset($config['class'])?$config['class']:'',
                    'form_group_class'=>isset($config['form_group_class'])?$config['form_group_class']:'',
                    'values' =>isset($config['values'])?$config['values']: array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
                    'lang' => isset($config['lang']) ? $config['lang'] : false
                );
                if($key == 'ETS_HOTDEALS_SPEED' || $key == 'ETS_HOTDEALS_STOP_WHEN_HOVER'){
                    $autoPlay = Tools::isSubmit('saveConfig') ? (int)Tools::getValue('ETS_HOTDEALS_AUTO_PLAY_SLIDER') : (int)Configuration::get('ETS_HOTDEALS_AUTO_PLAY_SLIDER');
                    if(!$autoPlay){
                        $confFields['form_group_class'] .= ' hide';
                    }
                }
                if(!$confFields['suffix'])
                    unset($confFields['suffix']);
                if($config['type'] == 'file')
                {
                    if($imageName = Configuration::get($key))
                    {
                        $confFields['display_img'] = $this->_path.'images/config/'.$imageName;
                        if(!isset($config['required']) || (isset($config['required']) && !$config['required']))
                            $confFields['img_del_link'] = $this->baseAdminPath.'&delimage=yes&image='.$key; 
                    }
                }
                $fields_form['form']['input'][] = $confFields;
                
                if($confFields['name']=='YBC_SHOPMSG_DATEFROM' || $confFields['name']=='YBC_SHOPMSG_DATETO')
                    $this->context->controller->addJqueryUI('ui.datepicker');
            }
        }        
        $helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'saveConfig';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=config';
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $fields = array();        
        $languages = Language::getLanguages(false);
        $helper->override_folder = '/';
        if(Tools::isSubmit('saveConfig'))
        {            
            if($configs)
            {                
                foreach($configs as $key => $config)
                {
                    if(isset($config['lang']) && $config['lang'])
                        {                        
                            foreach($languages as $l)
                            {
                                $fields[$key][$l['id_lang']] = Tools::getValue($key.'_'.$l['id_lang'],isset($config['default']) ? $config['default'] : '');
                            }
                        }
                        else
                            $fields[$key] = Tools::getValue($key,isset($config['default']) ? $config['default'] : '');
                }
            }
        }
        else
        {
            if($configs)
            {
                    foreach($configs as $key => $config)
                    {
                        if(isset($config['lang']) && $config['lang'])
                        {                    
                            foreach($languages as $l)
                            {
                                $fields[$key][$l['id_lang']] = Configuration::get($key,$l['id_lang']);
                            }
                        }
                        else
                            $fields[$key] = Configuration::get($key);                   
                    }
            }
        }
        $helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $fields,
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,                     
        );
        
        $this->_html .= $helper->generateForm(array($fields_form));		
     }
     private function _postConfig()
     {
        $errors = array();
        $languages = Language::getLanguages(false);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $configs = $this->configs;
        
        //Delete image
        if(Tools::isSubmit('delimage'))
        {
            $image = Tools::getValue('image');
            if(isset($configs[$image]) && !isset($configs[$image]['required']) || (isset($configs[$image]['required']) && !$configs[$image]['required']))
            {
                $imageName = Configuration::get($image);
                $imagePath = dirname(__FILE__).'/images/config/'.$imageName;
                if($imageName && file_exists($imagePath))
                {
                    @unlink($imagePath);
                    Configuration::updateValue($image,'');
                }
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
            }
            else
                $errors[] = $configs[$image]['label'].$this->l(' is required');
        }
        if(Tools::isSubmit('saveConfig'))
        {  
            if(!Validate::isInt(Tools::getValue('ETS_HOTDEALS_PRODUCT_COUNT'))&&Tools::getValue('ETS_HOTDEALS_SELECT_DISCOUNTED_PRODUCTS') && Tools::getValue('ETS_HOTDEALS_PRODUCT_COUNT'))
                $errors[]= $this->l('Product count is invalid.');
            if(Tools::getValue('ETS_HOTDEALS_SPEED') && !Validate::isInt(Tools::getValue('ETS_HOTDEALS_SPEED')))
                $errors[]=$this->l('Speed is invalid.');
            if((Tools::getValue('ETS_HOTDEALS_PRODUCT_PER_ROW_DESKTOP')&&!Validate::isInt(Tools::getValue('ETS_HOTDEALS_PRODUCT_PER_ROW_DESKTOP')))||(Tools::getValue('ETS_HOTDEALS_PRODUCT_PER_ROW_TABLETLARGE')&&!Validate::isInt(Tools::getValue('ETS_HOTDEALS_PRODUCT_PER_ROW_TABLETLARGE')))||(Tools::getValue('ETS_HOTDEALS_PRODUCT_PER_ROW_TABLET')&& !Validate::isInt(Tools::getValue('ETS_HOTDEALS_PRODUCT_PER_ROW_TABLET'))) || (Tools::getValue('ETS_HOTDEALS_PRODUCT_PER_ROW_MOBILE')&&!Validate::isInt(Tools::getValue('ETS_HOTDEALS_PRODUCT_PER_ROW_MOBILE'))))
            {
                $errors[]=$this->l('Item value is invalid.');
            }
            if($configs)
            {
                foreach($configs as $key => $config)
                {
                    if(isset($config['lang']) && $config['lang'])
                    {
                        if(isset($config['required'])
                            && $config['required'] && $config['type']!='switch'
                            && trim(Tools::getValue($key.'_'.$id_lang_default) == ''))
                        {
                            $errors[] = $config['label'].' '.$this->l('is required');
                        }                        
                    }
                    else
                    {
                        if(isset($config['required']) && $config['required'] && isset($config['type']) && $config['type']=='file')
                        {
                            if(Configuration::get($key)=='' && !isset($_FILES[$key]['size']))
                                $errors[] = $config['label'].' '.$this->l('is required');
                            elseif(isset($_FILES[$key]['size']))
                            {
                                $fileSize = round((int)$_FILES[$key]['size'] / (1024 * 1024));
                    			if($fileSize > 100)
                                    $errors[] = $config['label'].$this->l(' can not be larger than 100Mb');
                            }   
                        }
                        else
                        {
                            if(isset($config['required']) && $config['required'] && $config['type']!='switch' && trim(Tools::getValue($key) == ''))
                            {
                                if($key !== 'ETS_HOTDEALS_SPEED' || ($key == 'ETS_HOTDEALS_SPEED' && (int)Tools::getValue('ETS_HOTDEALS_AUTO_PLAY_SLIDER')))
                                    $errors[] = $config['label'].' '.$this->l('is required');
                            }
                            elseif(!Validate::isCleanHtml(trim(Tools::getValue($key))))
                            {
                                    $errors[] = $config['label'].' '.$this->l('is invalid');
                            } 
                        }                          
                    }                    
                }
            }            
            
            //Custom validation
            if(($productIds = Tools::getValue('YBC_SHOPMSG_PRODUCT')) && !preg_match('/^[0-9]+(,[0-9]+)*$/', $productIds))
                $errors[] = $this->l('Product IDs is not in valid format');
            
            if(!$errors)
            {
                if($configs)
                {
                    foreach($configs as $key => $config)
                    {
                        if(isset($config['lang']) && $config['lang'])
                        {
                            $valules = array();
                            foreach($languages as $lang)
                            {
                                if($config['type']=='switch')                                                           
                                    $valules[$lang['id_lang']] = (int)trim(Tools::getValue($key.'_'.$lang['id_lang'])) ? 1 : 0;                                
                                else
                                    $valules[$lang['id_lang']] = trim(Tools::getValue($key.'_'.$lang['id_lang'])) ? trim(Tools::getValue($key.'_'.$lang['id_lang'])) : trim(Tools::getValue($key.'_'.$id_lang_default));
                            }
                            Configuration::updateValue($key,$valules,true);
                        }
                        else
                        {
                            if($config['type']=='switch')
                            {                           
                                Configuration::updateValue($key,(int)trim(Tools::getValue($key)) ? 1 : 0,true);
                            }
                            if($config['type']=='file')
                            {
                                //Upload file
                                if(isset($_FILES[$key]['tmp_name']) && isset($_FILES[$key]['name']) && $_FILES[$key]['name'])
                                {
                                    $salt = sha1(microtime());
                                    $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'], '.'), 1));
                                    $imageName = $salt.'.'.$type;
                                    $fileName = dirname(__FILE__).'/images/config/'.$imageName;                
                                    if(file_exists($fileName))
                                    {
                                        $errors[] = $config['label'].$this->l(' already exists. Try to rename the file then reupload');
                                    }
                                    else
                                    {
                                        
                            			$imagesize = @getimagesize($_FILES[$key]['tmp_name']);
                                        
                                        if (!$errors && isset($_FILES[$key]) &&				
                            				!empty($_FILES[$key]['tmp_name']) &&
                            				!empty($imagesize) &&
                            				in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                            			)
                            			{
                            				$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');    				
                            				if ($error = ImageManager::validateUpload($_FILES[$key]))
                            					$errors[] = $error;
                            				elseif (!$temp_name || !move_uploaded_file($_FILES[$key]['tmp_name'], $temp_name))
                            					$errors[] = $this->l('Can not upload the file');
                            				elseif (!ImageManager::resize($temp_name, $fileName, null, null, $type))
                            					$errors[] = $this->displayError($this->l('An error occurred during the image upload process.'));
                                            @unlink($temp_name);
                                            if(!$errors)
                                            {
                                                if(Configuration::get($key)!='')
                                                {
                                                    $oldImage = dirname(__FILE__).'/images/config/'.Configuration::get($key);
                                                    if(file_exists($oldImage))
                                                        @unlink($oldImage);
                                                }                                                
                                                Configuration::updateValue($key, $imageName,true);                                                                                               
                                            }
                                        }
                                    }
                                }
                                //End upload file
                            }
                            else
                                Configuration::updateValue($key,trim(Tools::getValue($key)),true);   
                        }                        
                    }
                }
            }
            Tools::clearSmartyCache();
            if (count($errors))
            {
               $this->errorMessage = $this->displayError(implode('<br />', $errors));  
            }
            else
               Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);            
        }
     }
     
    public function hookDisplayHeader($params)
    {
        if(($page = Tools::strtolower(trim(Tools::getValue('controller')))) && $page && !in_array($page, array('index')))
            return;
        $this->context->controller->addJS($this->_path.'views/js/jquery.countdown.min.js');
        $this->context->controller->addJS($this->_path.'views/js/specific_prices.js');
        $this->context->controller->addCss($this->_path.'views/css/slick.css');
        $this->context->controller->addJS($this->_path.'views/js/slick.js');
        $this->context->controller->addCss($this->_path.'views/css/specific_prices.css');
        if ($this->is17){
            $this->context->controller->addCss($this->_path.'views/css/res_v17.css');
        } else {
            $this->context->controller->addCss($this->_path.'views/css/res_v16.css');
        }
        return $this->display(__FILE__, 'renderJs.tpl');
    }
    
    public function hookDisplayFooter($params)
    {
        //
    }
    
    public function hookDisplayCustomDiscount($params)
    {
        if(Configuration::get('ETS_HOTDEALS_HOOK_TO')=='custom_hook')
            return $this->getProductDiscount(); 
    }
    public function hookDisplayHome()
    {
        if(Configuration::get('ETS_HOTDEALS_HOOK_TO')=='display_home')
            return $this->getProductDiscount();
    }
    public function hookHotdeal()
    {
        if(Configuration::get('ETS_HOTDEALS_HOOK_TO')=='hotdeal')
            return $this->getProductDiscount();
    }
    public function getProductDiscount(){
        $id_address = $this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
        $ids = Address::getCountryAndState($id_address);
        $id_country = ($ids && $ids['id_country']) ? (int)$ids['id_country'] : (int)Configuration::get('PS_COUNTRY_DEFAULT');
        $current_date = date('Y-m-d 00:00:00');
        $id_customer= $this->context->customer->id;
        $query_extra = self::computeExtraConditions(null, null, $id_customer, null, $current_date, $current_date);
        $products_list = array();
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $id_lang = $this->context->language->id;
        if((int)Configuration::get('ETS_HOTDEALS_SELECT_DISCOUNTED_PRODUCTS'))
        {
            if(Configuration::get('ETS_HOTDEALS_SELECT_DISCOUNTED_PRODUCTS'))
                $limit =' LIMIT 0,'.(int)Configuration::get('ETS_HOTDEALS_PRODUCT_COUNT');
            else
                $limit =' LIMIT 0,10';
            $hotdeals_order_by =Configuration::get('ETS_HOTDEALS_ORDER_PRODUCTS_BY');
            $sql = 'SELECT  product_shop.*,p.ean13,sp.to,sp.id_specific_price, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
				pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`, pl.`name`,
                image_shop.`id_image` id_image, il.`legend`, m.`name` manufacturer_name,
				DATEDIFF(
					p.`date_add`,
					DATE_SUB(
						"'.date('Y-m-d').' 00:00:00",
						INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
					)
				) > 0 new'.(Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(sp.`id_product_attribute`,0) id_product_attribute' : '').',sp.id_product_attribute as id_product_attribute2,( IF (sp.`id_group` = 3, 2, 0) + IF (sp.`id_country` = 21, 4, 0) + IF (sp.`id_currency` = 2, 8, 0) + IF (sp.`id_shop` = 1, 16, 0) + IF (sp.`id_customer` = 2, 32, 0)) AS `score`
				FROM '._DB_PREFIX_.'product p
				'.Shop::addSqlAssociation('product', 'p').'
				INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				)
                INNER JOIN `'._DB_PREFIX_.'specific_price` sp ON(sp.id_product =p.id_product)
				'.(Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
				ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$this->context->shop->id.')':'').'
				'.Product::sqlStock('p', 0).'
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$this->context->shop->id.')
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				WHERE p.`active`=1 AND sp.`id_shop` '.self::formatIntInQuery(0, $this->context->shop->id).' AND 
                    sp.`id_currency` '.self::formatIntInQuery(0, $this->context->currency->id).' AND
                    sp.`id_country` '.self::formatIntInQuery(0, $id_country).' AND
                    sp.`id_group` '.self::formatIntInQuery(0, $this->context->customer->id_default_group).' AND
                    sp.`from_quantity` = 1 AND
                    sp.`reduction` > 0 '.$query_extra.'  
			     ORDER BY `sp`.`id_product_attribute` DESC, sp.`id_cart` DESC, sp.`from_quantity` DESC, sp.`id_specific_price_rule` ASC,`score` DESC, sp.`to` DESC, sp.`from` DESC '.pSQL($limit);
                $db->execute('create or replace view '._DB_PREFIX_.'ets_hotdeals as '.$sql);
                $result = $db->executeS('SELECT * FROM '._DB_PREFIX_.'ets_hotdeals GROUP BY id_product', true, false);
        }
        else
        {
            $Ids= explode(',',Configuration::get('ETS_HOTDEALS_PRODUCT_IDS'));
            $productAttributeIds=array();
            $productIds=array();
            if($Ids)
            {
                foreach($Ids as $Id)
                {
                    $id = explode('-',$Id);
                    $productIds[]= $id[0];
                    if(isset($id[1]))
                        $productAttributeIds[]=$id[1];
                    else
                        $productAttributeIds[]=0;
                }
                $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
    				pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`, pl.`name`,
                    image_shop.`id_image` id_image, il.`legend`, m.`name` manufacturer_name,
    				DATEDIFF(
    					p.`date_add`,
    					DATE_SUB(
    						"'.date('Y-m-d').' 00:00:00",
    						INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
    					)
    				) > 0 new'.(Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.`id_product_attribute`,0) id_product_attribute2' : '').'
    				FROM '._DB_PREFIX_.'product p
    				'.Shop::addSqlAssociation('product', 'p').'
    				INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
    					p.`id_product` = pl.`id_product`
    					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
    				)
    				'.(Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
    				ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.id_shop='.(int)$this->context->shop->id.' AND product_attribute_shop.id_product_attribute IN ('.implode(',',array_map('intval',$productAttributeIds)).'))':'').'
    				'.Product::sqlStock('p', 0).'
    				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
    				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
    					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$this->context->shop->id.')
    				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
    				WHERE p.`id_product` IN ('.implode(',',array_map('intval',$productIds)).') AND p.`active`=1
    				GROUP BY product_shop.id_product'.(Combination::isFeatureActive()? ',product_attribute_shop.id_product_attribute':'');
                    $result = $db->executeS($sql, true, false);
            }
            else
                $result=array();
        }
        $products_list = Product::getProductsProperties((int)$id_lang, $result);
	    $products_for_template = array();
        if($this->is17)
        {
            $assembler = new ProductAssembler($this->context);
            $presenterFactory = new ProductPresenterFactory($this->context);
            $presentationSettings = $presenterFactory->getPresentationSettings();
            $presenter = new PrestaShop\PrestaShop\Core\Product\ProductListingPresenter(
                new PrestaShop\PrestaShop\Adapter\Image\ImageRetriever(
                    $this->context->link
                ),
                $this->context->link,
                new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter(),
                new PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever(),
                $this->context->getTranslator()
            );

            if($products_list)
            {
                foreach ($products_list as $rawProduct) {
                    $products_for_template[] = $presenter->present(
                        $presentationSettings,
                        $assembler->assembleProduct($rawProduct),
                        $this->context->language
                    );
                }
            }
            if($products_for_template)
            {
                foreach($products_for_template as &$item)
                {
                    if($item['id_product_attribute2'])
                    {
                        $item['link'] = $this->context->link->getProductLink($item['id_product'],$item['link_rewrite'],null,null,null,null,$item['id_product_attribute2']);
                        $image=Product::getCombinationImageById($item['id_product_attribute2'],$this->context->language->id);
                        if($image)
                            $item['id_image']=$item['id_product'].'-'.$image['id_image'];
                    }
                    $type_image= ImageType::getFormattedName('home');
                    $item['link_image'] = $this->context->link->getImageLink($item['link_rewrite'],$item['id_image'],$type_image);
                }
            }
        }
        else
        {
            if($products_list)
            {
                foreach($products_list as &$item)
                {
                    if($item['id_product_attribute2'])
                    {
                        $item['link'] = $this->context->link->getProductLink($item['id_product'],$item['link_rewrite'],null,null,null,null,$item['id_product_attribute2']);
                        $image=Product::getCombinationImageById($item['id_product_attribute2'],$this->context->language->id);
                        if($image)
                            $item['id_image']=$item['id_product'].'-'.$image['id_image'];
                    }
                
                    $type_image= ImageType::getFormattedName('home');
                    $item['link_image'] = $this->context->link->getImageLink($item['link_rewrite'],$item['id_image'],$type_image);
                }
            }
            
        }
        if(Configuration::get('ETS_HOTDEALS_DISPLAY_PRODUCT_ATTRIBUTE'))
        {
            if($this->is17)
            {
                if($products_for_template)
                    foreach($products_for_template as &$product)
                    {
                        if($product['id_product_attribute2'])
                            $product['product_attribute'] = EtsHdProduct::getAllAttributes($product['id_product_attribute2']);
                        else
                        {
                            $product_class= new Product($product['id_product']);
                            if($product_class->hasAttributes())
                            {
                                $id_product_attribute= Product::getDefaultAttribute($product['id_product']);
                                $product['product_attribute'] = EtsHdProduct::getAllAttributes($id_product_attribute);
                            }
                        }
                    }
            }
            else
            {
                if($products_list)
                    foreach($products_list as &$product)
                    {
                        if($product['id_product_attribute2'])
                            $product['product_attribute'] = EtsHdProduct::getAllAttributes($product['id_product_attribute2']);
                        else
                        {
                            $product_class= new Product($product['id_product']);
                            if($product_class->hasAttributes())
                            {
                                $id_product_attribute= Product::getDefaultAttribute($product['id_product']);
                                $product['product_attribute'] = EtsHdProduct::getAllAttributes($id_product_attribute);
                            }
                        }
                    }
            }
            
        }
        if(isset($hotdeals_order_by)&& $hotdeals_order_by=='most_discounted')
        {
            if($this->is17)
            {
                $count= count($products_for_template);
                for($i=0;$i<=$count-2;$i++)
                    for($j=$i+1;$j<=$count-1;$j++)
                    {
                       $specific_price_i= $this->getSpecificPrice($products_for_template[$i]);
                       $specific_price_j= $this->getSpecificPrice($products_for_template[$j]);
                       if($specific_price_i < $specific_price_j)
                       {
                            $tam = $products_for_template[$i];
                            $products_for_template[$i]=$products_for_template[$j];
                            $products_for_template[$j] = $tam;
                       }
                    }
            }
            else
            {
                $count= count($products_list);
                for($i=0;$i<=$count-2;$i++)
                for($j=$i+1;$j<=$count-1;$j++)
                {
                   $specific_price_i= $this->getSpecificPrice($products_list[$i]); 
                   $specific_price_j= $this->getSpecificPrice($products_list[$j]);
                   if($specific_price_i < $specific_price_j)
                   {
                        $tam = $products_list[$i];
                        $products_for_template[$i]=$products_list[$j];
                        $products_list[$j] = $tam;
                   }
                }
            }
               
        }
        if(isset($hotdeals_order_by)&& $hotdeals_order_by=='new_products_discounted')
        {
            if($this->is17)
            {
                $count= count($products_for_template);
                for($i=0;$i<=$count-2;$i++)
                    for($j=$i+1;$j<=$count-1;$j++)
                    {
                       if($products_for_template[$i]['id_specific_price'] < $products_for_template[$j]['id_specific_price'])
                       {
                            $tam = $products_for_template[$i];
                            $products_for_template[$i]=$products_for_template[$j];
                            $products_for_template[$j] = $tam;
                       }
                    }
            }
            else
            {
                $count= count($products_list);
                for($i=0;$i<=$count-2;$i++)
                for($j=$i+1;$j<=$count-1;$j++)
                {
                   if($products_list[$i]['id_specific_price']<$products_list[$j]['id_specific_price'])
                   {
                        $tam = $products_list[$i];
                        $products_for_template[$i]=$products_list[$j];
                        $products_list[$j] = $tam;
                   }
                }
            }
               
        }
        if(isset($hotdeals_order_by)&& $hotdeals_order_by=='nearly_expired')
        {
            if($this->is17)
            {
                $count= count($products_for_template);
                for($i=0;$i<=$count-2;$i++)
                    for($j=$i+1;$j<=$count-1;$j++)
                    {
                       if((strtotime($products_for_template[$i]['to']) > strtotime($products_for_template[$j]['to']) && strtotime($products_for_template[$j]['to'])>0) || strtotime($products_for_template[$i]['to'])<0)
                       {
                            $tam = $products_for_template[$i];
                            $products_for_template[$i]=$products_for_template[$j];
                            $products_for_template[$j] = $tam;
                       }
                    }
            }
            else
            {
                $count= count($products_list);
                for($i=0;$i<=$count-2;$i++)
                    for($j=$i+1;$j<=$count-1;$j++)
                    {
                       if((strtotime($products_list[$i]['to']) > strtotime($products_list[$j]['to']) && strtotime($products_list[$j]['to'])>0) || strtotime($products_list[$i]['to'])<0)
                       {
                            $tam = $products_list[$i];
                            $products_for_template[$i]=$products_list[$j];
                            $products_list[$j] = $tam;
                       }
                    }
            }
               
        }
        $this->smarty->assign(array(
            'products_list'=>$this->is17?$products_for_template:$products_list,
            'ets_hotdeals_display_product_attribute'=>Configuration::get('ETS_HOTDEALS_DISPLAY_PRODUCT_ATTRIBUTE'),
            'ets_hotdeals_display_rating'=> Configuration::get('ETS_HOTDEALS_DISPLAY_RATING'),
            'ets_productcomment_enable'=> Module::isEnabled('ets_productcomments'),
            'ets_hotdeals_display_description'=>Configuration::get('ETS_HOTDEALS_DISPLAY_DESCRIPTION'),
            'ets_hotdeals_display_discounted_amount'=>Configuration::get('ETS_HOTDEALS_DISPLAY_DISCOUNTED_AMOUNT'),
            'ets_hotdeals_display_countdown_clock'=>Configuration::get('ETS_HOTDEALS_DISPLAY_COUNTDOWN_CLOCK'),
            'ets_hotdeals_display_type'=>Configuration::get('ETS_HOTDEALS_DISPLAY_TYPE'),
            'ets_hotdeals_auto_play_slider'=>Configuration::get('ETS_HOTDEALS_AUTO_PLAY_SLIDER'),
            'ets_hotdeals_stop_hover'=>Configuration::get('ETS_HOTDEALS_STOP_WHEN_HOVER'),
            'ets_hotdeals_speed'=>Configuration::get('ETS_HOTDEALS_SPEED'),
            'ets_hotdeals_product_per_row_mobile'=>Configuration::get('ETS_HOTDEALS_PRODUCT_PER_ROW_MOBILE'),
            'ets_hotdeals_product_per_row_tabletlarge'=>Configuration::get('ETS_HOTDEALS_PRODUCT_PER_ROW_TABLETLARGE'),
            'ets_hotdeals_product_per_row_tablet'=>Configuration::get('ETS_HOTDEALS_PRODUCT_PER_ROW_TABLET'),
            'ets_hotdeals_product_per_row_desktop'=>Configuration::get('ETS_HOTDEALS_PRODUCT_PER_ROW_DESKTOP'),
            'specific_title' => Configuration::get('ETS_HOTDEALS_TITLE',$this->context->language->id),
            'pres17'=>$this->is17,
            
        ));
        if($this->is17)
            return $this->display(__FILE__, 'display_product.tpl');
        else
            return $this->display(__FILE__, 'display_product_16.tpl');
    }
    private static function formatIntInQuery($first_value, $second_value) {
        $first_value = (int)$first_value;
        $second_value = (int)$second_value;
        if ($first_value != $second_value) {
            return 'IN ('.$first_value.', '.$second_value.')';
        } else {
            return ' = '.$first_value;
        } 
    }
    protected static function computeExtraConditions($id_product, $id_product_attribute, $id_customer, $id_cart, $beginning = null, $ending = null)
    {
        $first_date = date('Y-m-d 00:00:00');
        $last_date = date('Y-m-d 23:59:59');
        $now = date('Y-m-d 00:00:00');
        if ($beginning === null) {
            $beginning = $now;
        }
        if ($ending === null) {
            $ending = $now;
        }
        $id_customer = (int)$id_customer;

        $query_extra = '';

        if ($id_product !== null) {
            $query_extra .= self::filterOutField('id_product', $id_product);
        }
        if ($id_customer !== null) {
            $query_extra .= self::filterOutField('id_customer', $id_customer);
        }

        if ($id_product_attribute !== null) {
            $query_extra .= self::filterOutField('id_product_attribute', $id_product_attribute);
        }

        if ($id_cart !== null) {
            $query_extra .= self::filterOutField('id_cart', $id_cart);
        }

        if ($ending == $now && $beginning == $now) {
            $key = __FUNCTION__.'-'.$first_date.'-'.$last_date;
            if (!array_key_exists($key, self::$_filterOutCache)) {
                $query_from_count    = 'SELECT 1 FROM `'._DB_PREFIX_.'specific_price` WHERE `from` BETWEEN \''.pSQL($first_date).'\' AND \''.pSQL($last_date).'\'';
                $from_specific_count = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_from_count);

                $query_to_count = 'SELECT 1 FROM `'._DB_PREFIX_.'specific_price` WHERE `to` BETWEEN \''.pSQL($first_date).'\' AND \''.pSQL($last_date).'\'';

                $to_specific_count= Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_to_count);
                self::$_filterOutCache[$key] = array($from_specific_count, $to_specific_count);
            } else {
                list($from_specific_count, $to_specific_count) = self::$_filterOutCache[$key];
            }
        } else {
            $from_specific_count = $to_specific_count = 1;
        }

        // if the from and to is not reached during the current day, just change $ending & $beginning to any date of the day to improve the cache
        if (!$from_specific_count && !$to_specific_count) {
            $ending = $beginning = $first_date;
        }
        
        $query_extra .= ' AND (`from` = \'0000-00-00 00:00:00\' OR \''.pSQL($beginning).'\' >= `from`)'
                       .' AND (`to` = \'0000-00-00 00:00:00\' OR \''.pSQL($ending).'\' <= `to`)';

        return $query_extra;
    }
    protected static function filterOutField($field_name, $field_value, $threshold = 1000)
    {
	    $field_name_sql = pSQL($field_name);
        $query_extra = 'AND `'.$field_name_sql.'` = 0 ';
        if ($field_value == 0 || array_key_exists($field_name, self::$_no_specific_values)) {
            return $query_extra;
        }
        $key_cache     = __FUNCTION__.'-'.$field_name.'-'.$threshold;
        $specific_list = array();
        if (!array_key_exists($key_cache, self::$_filterOutCache)) {
            $query_count    = 'SELECT COUNT(DISTINCT `'.$field_name_sql.'`) FROM `'._DB_PREFIX_.'specific_price` WHERE `'.$field_name_sql.'` != 0';
            $specific_count = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_count);
            if ($specific_count == 0) {
                self::$_no_specific_values[$field_name] = true;

                return $query_extra;
            }
            if ($specific_count < $threshold) {
                $query = 'SELECT DISTINCT `'.$field_name_sql.'` FROM `'._DB_PREFIX_.'specific_price` WHERE `'.$field_name_sql.'` != 0';
                $tmp_specific_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                foreach ($tmp_specific_list as $value) {
                    $specific_list[] = $value[$field_name];
                }
            }
            self::$_filterOutCache[$key_cache] = $specific_list;
        } else {
            $specific_list = self::$_filterOutCache[$key_cache];
        }

        // $specific_list is empty if the threshold is reached
        if (empty($specific_list) || in_array($field_value, $specific_list)) {
            $query_extra = 'AND `'.$field_name_sql.'` '.self::formatIntInQuery(0, $field_value).' ';
        }

        return $query_extra;
    }
    public function getAllAttributes($id_product_attribute)
    {
        return EtsHdProduct::getAllAttributes($id_product_attribute);
    }
    public function getSpecificPrice($product)
    {
        if(isset($product['specific_prices']['reduction_type'])&&$product['specific_prices']['reduction_type']=='percentage')
            return $product['specific_prices']['reduction'];
        if(isset($product['specific_prices']['reduction_type']) && $product['specific_prices']['reduction_type']=='amount')
        {
            $id_customer = ($this->context->customer->id) ? (int)($this->context->customer->id) : 0;
            $id_group = null;
            if ($id_customer) {
                $id_group = Customer::getDefaultGroupId((int)$id_customer);
            }
            if (!$id_group) {
                $id_group = (int)Group::getCurrent()->id;
            }
            $group= new Group($id_group);
            if($group->price_display_method)
                $tax=false;
            else
                $tax=true;
            $product_class = new Product($product['id_product'], true, $this->context->language->id, $this->context->shop->id);
            $price= $product_class->getPriceWithoutReduct(!$tax,false);
            return $price > 0 ? (float)$product['specific_prices']['reduction']/$price : 0;
        }
        return 0;
    }

    public function displayIframe()
    {
        switch($this->context->language->iso_code) {
            case 'en':
                $url = 'https://cdn.prestahero.com/prestahero-product-feed?utm_source=feed_'.$this->name.'&utm_medium=iframe';
                break;
            case 'it':
                $url = 'https://cdn.prestahero.com/it/prestahero-product-feed?utm_source=feed_'.$this->name.'&utm_medium=iframe';
                break;
            case 'fr':
                $url = 'https://cdn.prestahero.com/fr/prestahero-product-feed?utm_source=feed_'.$this->name.'&utm_medium=iframe';
                break;
            case 'es':
                $url = 'https://cdn.prestahero.com/es/prestahero-product-feed?utm_source=feed_'.$this->name.'&utm_medium=iframe';
                break;
            default:
                $url = 'https://cdn.prestahero.com/prestahero-product-feed?utm_source=feed_'.$this->name.'&utm_medium=iframe';
        }
        $this->smarty->assign(
            array(
                'url_iframe' => $url
            )
        );
        return $this->display(__FILE__,'iframe.tpl');
    }

}
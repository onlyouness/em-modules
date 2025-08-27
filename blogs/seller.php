<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    JA Modules <info@jamodules.com>
 * @copyright Since 2007 JA Modules
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class JmarketplaceSellerModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function setMedia()
    {
        parent::setMedia();

        $this->context->controller->registerStylesheet(
            'module-jmarketplace-select2',
            'modules/' . $this->module->name . '/views/css/select2.min.css',
            ['media' => 'all', 'priority' => 150]
        );

        $this->context->controller->addJqueryPlugin('fancybox');

        $this->context->controller->registerJavascript(
            'module-jmarketplace-tinymce',
            $this->module->getPathUri() . 'views/js/tinymce/tinymce.min.js',
            ['server' => 'remote', 'position' => 'head', 'priority' => 150]
        );

        $iso = Language::getIsoById($this->context->language->id);

        switch ($iso) {
            case 'de':
                $this->context->controller->registerJavascript(
                    'module-jmarketplace-tinymce-de',
                    'modules/' . $this->module->name . '/views/js/tinymce/langs/de.js',
                    ['position' => 'bottom', 'priority' => 150]
                );
                break;
            case 'es':
                $this->context->controller->registerJavascript(
                    'module-jmarketplace-tinymce-es',
                    'modules/' . $this->module->name . '/views/js/tinymce/langs/es.js',
                    ['position' => 'bottom', 'priority' => 150]
                );
                break;
            case 'ar':
                $this->context->controller->registerJavascript(
                    'module-jmarketplace-tinymce-es_AR',
                    'modules/' . $this->module->name . '/views/js/tinymce/langs/es_AR.js',
                    ['position' => 'bottom', 'priority' => 150]
                );
                break;
            case 'mx':
                $this->context->controller->registerJavascript(
                    'module-jmarketplace-tinymce-es_MX',
                    'modules/' . $this->module->name . '/views/js/tinymce/langs/es_MX.js',
                    ['position' => 'bottom', 'priority' => 150]
                );
                break;
            case 'fr':
                $this->context->controller->registerJavascript(
                    'module-jmarketplace-tinymce-fr_FR',
                    'modules/' . $this->module->name . '/views/js/tinymce/langs/fr_FR.js',
                    ['position' => 'bottom', 'priority' => 150]
                );
                break;
            case 'it':
                $this->context->controller->registerJavascript(
                    'module-jmarketplace-tinymce-it',
                    'modules/' . $this->module->name . '/views/js/tinymce/langs/it.js',
                    ['position' => 'bottom', 'priority' => 150]
                );
                break;
            case 'nl':
                $this->context->controller->registerJavascript(
                    'module-jmarketplace-tinymce-nl',
                    'modules/' . $this->module->name . '/views/js/tinymce/langs/nl.js',
                    ['position' => 'bottom', 'priority' => 150]
                );
                break;
            case 'pl':
                $this->context->controller->registerJavascript(
                    'module-jmarketplace-tinymce-pl',
                    'modules/' . $this->module->name . '/views/js/tinymce/langs/pl.js',
                    ['position' => 'bottom', 'priority' => 150]
                );
                break;
            case 'br':
                $this->context->controller->registerJavascript(
                    'module-jmarketplace-tinymce-pt_BR',
                    'modules/' . $this->module->name . '/views/js/tinymce/langs/pt_BR.js',
                    ['position' => 'bottom', 'priority' => 150]
                );
                break;
            case 'pt':
                $this->context->controller->registerJavascript(
                    'module-jmarketplace-tinymce-pt_PT',
                    'modules/' . $this->module->name . '/views/js/tinymce/langs/pt_PT.js',
                    ['position' => 'bottom', 'priority' => 150]
                );
                break;
            case 'ro':
                $this->context->controller->registerJavascript(
                    'module-jmarketplace-tinymce-ro',
                    'modules/' . $this->module->name . '/views/js/tinymce/langs/ro.js',
                    ['position' => 'bottom', 'priority' => 150]
                );
                break;
            case 'ru':
                $this->context->controller->registerJavascript(
                    'module-jmarketplace-tinymce-ru',
                    'modules/' . $this->module->name . '/views/js/tinymce/langs/ru.js',
                    ['position' => 'bottom', 'priority' => 150]
                );
                break;
            case 'fa':
                $this->context->controller->registerJavascript(
                    'module-jmarketplace-tinymce-fa_IR',
                    'modules/' . $this->module->name . '/views/js/tinymce/langs/fa_IR.js',
                    ['position' => 'bottom', 'priority' => 150]
                );
                break;
            default:
                $this->context->controller->registerJavascript(
                    'module-jmarketplace-tinymce-en_GB',
                    'modules/' . $this->module->name . '/views/js/tinymce/langs/en_GB.js',
                    ['position' => 'bottom', 'priority' => 150]
                );
                break;
        }

        $this->context->controller->registerJavascript(
            'module-jmarketplace-calltinymce',
            'modules/' . $this->module->name . '/views/js/calltinymce.js',
            ['position' => 'bottom', 'priority' => 150]
        );

        $this->context->controller->registerJavascript(
            'module-jmarketplace-select2',
            'modules/' . $this->module->name . '/views/js/select2.min.js',
            ['position' => 'bottom', 'priority' => 150]
        );

        $this->context->controller->registerJavascript(
            'module-jmarketplace-select2call',
            'modules/' . $this->module->name . '/views/js/select2call.js',
            ['position' => 'bottom', 'priority' => 150]
        );

        $this->context->controller->registerJavascript(
            'module-jmarketplace-seller',
            'modules/' . $this->module->name . '/views/js/seller.js',
            ['position' => 'bottom', 'priority' => 150]
        );
    }

    public function init()
    {
        $action = Tools::getValue('action');

        if (!$this->context->cookie->id_customer) {
            Tools::redirect($this->context->link->getPageLink('my-account', true));
        }

        if ($action == 'update') {
            if (!$this->context->cookie->id_customer) {
                Tools::redirect($this->context->link->getPageLink('my-account', true));
            }

            $is_seller = Seller::isSeller($this->context->cookie->id_customer, $this->context->shop->id);
            if (!$is_seller) {
                Tools::redirect($this->context->link->getPageLink('my-account', true));
            }

            if (Configuration::get('JMARKETPLACE_SELLER_EDIT') == 0) {
                Tools::redirect($this->context->link->getModuleLink('jmarketplace', 'selleraccount', [], true));
            }

            $id_seller = Seller::getSellerByCustomer($this->context->cookie->id_customer);
            $seller = new Seller($id_seller);

            $params = [
                'token' => $seller->secure_key,
            ];

            if ($seller->secure_key != Tools::getValue('token')) {
                Tools::redirect($this->context->link->getModuleLink('jmarketplace', 'selleraccount', $params, true));
            }

            if ($seller->active == 0) {
                Tools::redirect($this->context->link->getPageLink('my-account', true));
            }
        }

        if ($action && $action !== 'update' && $action !== 'add') {
            Tools::redirect($this->context->link->getPageLink('my-account', true));
        }

        parent::init();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitSeller')) {
            $action = Tools::getValue('action');
            if ($action == 'add') {
                $old_link_rewrite = '';
                Hook::exec('actionMarketplaceBeforeAddSeller');
            } else {
                $id_seller = Seller::getSellerByCustomer((int) $this->context->cookie->id_customer);
                $seller = new Seller($id_seller);
                $old_link_rewrite = $seller->link_rewrite;
                $params = ['id_seller' => $seller->id];
                Hook::exec('actionMarketplaceBeforeUpdateSeller');
            }

            $seller_name = (string) Tools::getValue('name');
            $seller_shop = (string) Tools::getValue('shop');
            $seller_email = (string) Tools::getValue('email');
            $seller_link_rewrite = $old_link_rewrite;
            if (Tools::getValue('id_lang')) {
                $id_lang = (int) Tools::getValue('id_lang');
            } else {
                $id_lang = $this->context->language->id;
            }

            if (!Tools::getValue('conditions') && Configuration::get('JMARKETPLACE_SELLER_ACCEPT_TERMS') == 1) {
                $this->errors[] = $this->module->l('You must agree to the terms of service before continuing.', 'seller');
            }

            if ($seller_name && Configuration::get('JMARKETPLACE_SELLER_NAME') == 1) {
                if (!Validate::isCatalogName($seller_name)) {
                    $this->errors[] = $this->module->l('Invalid seller name.', 'seller');
                }

                if (!isset($seller_name) || $seller_name == '') {
                    $this->errors[] = $this->module->l('Invalid seller name.', 'seller');
                }

                if ($action == 'add') {
                    if (Seller::existName($seller_name) > 0) {
                        $this->errors[] = $this->module->l('The name of seller already exists in our database.', 'seller');
                    }
                } else {
                    if (Seller::existName($seller_name) > 0 && $seller->name != $seller_name) {
                        $this->errors[] = $this->module->l('The name of seller already exists in our database.', 'seller');
                    } else {
                        $seller->name = $seller_name;
                    }
                }
            }

            if ($seller_shop && Configuration::get('JMARKETPLACE_SELLER_SHOP') == 1) {
                if (!Validate::isCatalogName($seller_shop)) {
                    $this->errors[] = $this->module->l('Invalid seller shop.', 'seller');
                }

                if (!isset($seller_shop) || $seller_shop == '') {
                    $this->errors[] = $this->module->l('Invalid seller shop.', 'seller');
                }

                if ($action == 'add') {
                    if (Seller::existShopName($seller_shop) > 0) {
                        $this->errors[] = $this->module->l('The shop name already exists in our database.', 'seller');
                    }
                } else {
                    if (Seller::existShopName($seller_shop) > 0 && $seller->shop != $seller_shop) {
                        $this->errors[] = $this->module->l('The shop name already exists in our database.', 'seller');
                    } else {
                        $seller->shop = $seller_shop;
                    }
                }

                if (Configuration::get('JMARKETPLACE_SELLER_NAME') == 0) {
                    $seller_name = $seller_shop;
                }
            }

            if ($action == 'add') {
                if (Seller::existEmail($seller_email) > 0) {
                    $this->errors[] = $this->module->l('The email of seller already exists in our database.', 'seller');
                }
            } else {
                if (Seller::existEmail($seller_email) > 0 && $seller->email != $seller_email) {
                    $this->errors[] = $this->module->l('The email of seller already exists in our database.', 'seller');
                }
            }

            if (!isset($seller_email) || $seller_email == '' || !Validate::isEmail($seller_email)) {
                $this->errors[] = $this->module->l('Invalid seller email.', 'seller');
            }

            if (isset($_FILES['sellerImage']) && $_FILES['sellerImage']['name'] != '') {
                if (!Seller::saveSellerImage($_FILES['sellerImage'], $seller_link_rewrite)) {
                    $this->errors[] = $this->module->l('The image seller format is incorrect.', 'seller');
                }
            }

            if (isset($_FILES['sellerBanner']) && $_FILES['sellerBanner']['name'] != '') {
                if (!Seller::saveSellerBanner($_FILES['sellerBanner'], $seller_link_rewrite)) {
                    $this->errors[] = $this->module->l('The image seller format is incorrect.', 'seller');
                }
            }

            $allow_iframe = (int) Configuration::get('PS_ALLOW_HTML_IFRAME');

            if (Tools::getValue('description') != '' && !Validate::isCleanHtml(Tools::getValue('description'), $allow_iframe)) {
                $this->errors[] = $this->module->l('Seller description is incorrect.', 'seller');
            }

            if (!count($this->errors)) {
                if ($action == 'add') {
                    $seller = new Seller();
                }

                $seller->id_customer = (int) $this->context->cookie->id_customer;
                $seller->id_shop = (int) $this->context->shop->id;
                $seller->id_lang = $id_lang;
                $seller->name = Tools::stripslashes(trim($seller_name));
                $seller->email = $seller_email;
                $seller->shop = Tools::stripslashes(trim($seller_shop));

                if (Configuration::get('JMARKETPLACE_SELLER_AUTO_LINK_REWRITE') == 'name') {
                    $seller->link_rewrite = Seller::generateLinkRewrite($seller->name);
                } else {
                    $seller->link_rewrite = Seller::generateLinkRewrite($seller->shop);
                }

                if ($old_link_rewrite != $seller->link_rewrite) {
                    rename(
                        _PS_IMG_DIR_.'sellers/'.$old_link_rewrite.'.jpg',
                        _PS_IMG_DIR_.'sellers/'.$seller->link_rewrite.'.jpg'
                    );
                }

                $seller->cif = Tools::getValue('cif');
                $seller->phone = Tools::getValue('phone');
                $seller->fax = Tools::getValue('fax');
                $seller->address = Tools::stripslashes(Tools::getValue('address'));
                $seller->country = Tools::stripslashes(Tools::getValue('country'));
                $seller->state = Tools::stripslashes(Tools::getValue('state'));
                $seller->city = Tools::stripslashes(Tools::getValue('city'));
                $seller->postcode = Tools::getValue('postcode');
                $seller->description = (string) Tools::getValue('description'); // this is content html
                $seller->short_description = (string) Tools::getValue('short_description'); // this is content html
                $seller->meta_title = Tools::getValue('meta_title');
                $seller->meta_description = Tools::getValue('meta_description');
                $seller->meta_keywords = Tools::getValue('meta_keywords');
                $seller->active = 1;

                if (Configuration::get('JMARKETPLACE_SELLER_MODERATE')) {
                    $seller->validate = 0;
                } else {
                    $seller->validate = 1;
                }

                if ($action == 'add') {
                    $seller->secure_key = md5($seller->id_customer);
                    $seller->add();
                    $params = ['id_seller' => $seller->id];
                    Hook::exec('actionMarketplaceAfterAddSeller', $params);
                } else {
                    $seller->update();
                    $params = ['id_seller' => $seller->id];
                    Hook::exec('actionMarketplaceAfterUpdateSeller', $params);
                }

                if (Configuration::get('JMARKETPLACE_SELLER_MODERATE') || Configuration::get('JMARKETPLACE_SEND_ADMIN_REGISTER')) {
                    $id_seller_email = false;
                    $to = Configuration::get('JMARKETPLACE_SEND_ADMIN');
                    $to_name = Configuration::get('PS_SHOP_NAME');
                    $from = Configuration::get('PS_SHOP_EMAIL');
                    $from_name = Configuration::get('PS_SHOP_NAME');
                    $template = 'base';

                    if ($action == 'add') {
                        $reference = 'new-seller';
                    } else {
                        $reference = 'edit-seller';
                    }

                    $id_seller_email = SellerEmail::getIdByReference($reference);

                    if ($id_seller_email) {
                        $seller_email = new SellerEmail($id_seller_email, Configuration::get('PS_LANG_DEFAULT'));
                        $vars = ['{shop_name}', '{seller_name}', '{seller_shop}'];
                        $seller_name = $seller->getSellerName();
                        $values = [Configuration::get('PS_SHOP_NAME'), $seller_name, $seller->shop];
                        $subject_var = $seller_email->subject;
                        $subject_value = str_replace($vars, $values, $subject_var);
                        $content_var = $seller_email->content;
                        $content_value = str_replace($vars, $values, $content_var);

                        $template_vars = [
                            '{content}' => $content_value,
                            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                        ];

                        $iso = Language::getIsoById(Configuration::get('PS_LANG_DEFAULT'));

                        if (file_exists(dirname(__FILE__) . '/../../mails/' . $iso . '/' . $template . '.txt') && file_exists(dirname(__FILE__) . '/../../mails/' . $iso . '/' . $template . '.html')) {
                            Mail::Send(
                                Configuration::get('PS_LANG_DEFAULT'),
                                $template,
                                $subject_value,
                                $template_vars,
                                $to,
                                $to_name,
                                $from,
                                $from_name,
                                null,
                                null,
                                dirname(__FILE__) . '/../../mails/'
                            );
                        }
                    }
                }

                if (Configuration::get('JMARKETPLACE_SEND_SELLER_WELCOME') && $action == 'add') {
                    $id_seller_email = false;
                    $to = $seller->email;
                    $to_name = $seller->name;
                    $from = Configuration::get('JMARKETPLACE_SEND_ADMIN');
                    $from_name = Configuration::get('PS_SHOP_NAME');
                    $template = 'base';
                    $reference = 'welcome-seller';
                    $id_seller_email = SellerEmail::getIdByReference($reference);

                    if ($id_seller_email) {
                        $seller_email = new SellerEmail($id_seller_email, $id_lang);
                        $vars = ['{shop_name}', '{seller_name}'];
                        $seller_name = $seller->getSellerName();
                        $values = [Configuration::get('PS_SHOP_NAME'), $seller_name];
                        $subject_var = $seller_email->subject;
                        $subject_value = str_replace($vars, $values, $subject_var);
                        $content_var = $seller_email->content;
                        $content_value = str_replace($vars, $values, $content_var);

                        $template_vars = [
                            '{content}' => $content_value,
                            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                        ];

                        $iso = Language::getIsoById($id_lang);

                        if (file_exists(dirname(__FILE__) . '/../../mails/' . $iso . '/' . $template . '.txt') && file_exists(dirname(__FILE__) . '/../../mails/' . $iso . '/' . $template . '.html')) {
                            Mail::Send(
                                $id_lang,
                                $template,
                                $subject_value,
                                $template_vars,
                                $to,
                                $to_name,
                                $from,
                                $from_name,
                                null,
                                null,
                                dirname(__FILE__) . '/../../mails/'
                            );
                        }
                    }
                }

                if ($action == 'add') {
                    Tools::redirect($this->context->link->getPageLink('my-account', true));
                } else {
                    $this->context->smarty->assign(['confirmation' => 1]);
                }
            } else {
                $this->context->smarty->assign([
                    'errors' => $this->errors,
                    'customer_name' => Tools::getValue('name'),
                    'seller_shop' => Tools::getValue('shop'),
                    'cif' => Tools::getValue('cif'),
                    'customer_email' => Tools::getValue('email'),
                    'id_lang' => Tools::getValue('id_lang'),
                    'phone' => Tools::getValue('phone'),
                    'fax' => Tools::getValue('fax'),
                    'address' => Tools::getValue('address'),
                    'country_name' => Tools::getValue('country'),
                    'state' => Tools::getValue('state'),
                    'postcode' => Tools::getValue('postcode'),
                    'city' => Tools::getValue('city'),
                    'description' => Tools::getValue('description'),
                    'short_description' => Tools::getValue('short_description'),
                ]);
            }
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        if (Tools::getValue('action') == 'add') {
            $breadcrumb['links'][] = [
                'title' => $this->module->l('Your account', 'seller'),
                'url' => $this->context->link->getPageLink('my-account', true),
            ];
        } else {
            $id_seller = Seller::getSellerByCustomer($this->context->cookie->id_customer);
            $seller = new Seller($id_seller);

            $params = [
                'token' => $seller->secure_key,
            ];

            $breadcrumb['links'][] = [
                'title' => $this->module->l('Your account', 'seller'),
                'url' => $this->context->link->getPageLink('my-account', true),
            ];

            $breadcrumb['links'][] = [
                'title' => $this->module->l('Seller account', 'seller'),
                'url' => $this->context->link->getModuleLink('jmarketplace', 'selleraccount', $params, true),
            ];
        }

        return $breadcrumb;
    }

    protected function ajaxProcessAddImageDescription()
    {
        $image_location = ImageUploader::processImage($_FILES['file']);
        echo json_encode(['location' => $image_location]);
        exit;
    }

    public function initContent()
    {
        parent::initContent();

        if (Tools::isSubmit('subaction') && Tools::getValue('subaction') == 'add_image_description') {
            $this->ajaxProcessAddImageDescription();
        }

        if (Configuration::get('PS_SSL_ENABLED') == 1) {
            $url_shop = Tools::getShopDomainSsl(true) . __PS_BASE_URI__;
        } else {
            $url_shop = Tools::getShopDomain(true) . __PS_BASE_URI__;
        }

        if (Tools::getValue('action') == 'update') {
            $id_seller = Seller::getSellerByCustomer($this->context->cookie->id_customer);
            $seller = new Seller($id_seller);

            $params = [
                'id_seller' => $seller->id,
                'link_rewrite' => $seller->link_rewrite,
            ];

            $url_seller_profile = $this->context->link->getModuleLink('jmarketplace', 'sellerprofile', $params);

            $params = [
                'action' => 'update',
                'token' => $seller->secure_key,
            ];

            $seller_form_action = $this->context->link->getModuleLink('jmarketplace', 'seller', $params, true);

            $this->context->smarty->assign([
                'seller' => $seller,
                'seller_form_action' => $seller_form_action,
                'seller_link' => $url_seller_profile,
                'mesages_not_readed' => SellerIncidenceMessage::getNumMessagesNotReadedBySeller($id_seller),
                'seller_validate' => 1,
            ]);

            if (Configuration::get('JMARKETPLACE_SELLER_MODERATE') == 1 && $seller->validate == 0) {
                $this->context->smarty->assign([
                    'seller_validate' => 0,
                ]);
            }

            if (file_exists(_PS_IMG_DIR_ . 'sellers/' . $seller->link_rewrite . '.jpg')) {
                $this->context->smarty->assign(['seller_logo' => $url_shop . 'img/sellers/' . $seller->link_rewrite . '.jpg']);
            }

            if (file_exists(_PS_IMG_DIR_ . 'sellerbanners/' . $seller->link_rewrite . '.jpg')) {
                $this->context->smarty->assign(['seller_banner' => $url_shop . 'img/sellerbanners/' . $seller->link_rewrite . '.jpg']);
            }
        } else {
            $params = [
                'action' => 'add',
            ];

            $seller_form_action = $this->context->link->getModuleLink('jmarketplace', 'seller', $params, true);

            $this->context->smarty->assign([
                'seller' => false,
                'seller_validate' => -1,
                'seller_form_action' => $seller_form_action,
            ]);
        }

        $customer = new Customer($this->context->cookie->id_customer);

        if (Configuration::get('JMARKETPLACE_SELLER_COUNTRY')) {
            $countries = Country::getCountries($this->context->language->id, true);
            $this->context->smarty->assign('countries', $countries);
        }

        if (isset($this->context->cookie->menuOptionsStatus)) {
            $menu_options_status = $this->context->cookie->menuOptionsStatus;
        } else {
            $menu_options_status = 1;
        }

        $this->context->smarty->assign([
            'moderate' => Configuration::get('JMARKETPLACE_SELLER_MODERATE'),
            'show_name' => Configuration::get('JMARKETPLACE_SELLER_NAME'),
            'show_shop' => Configuration::get('JMARKETPLACE_SELLER_SHOP'),
            'show_cif' => Configuration::get('JMARKETPLACE_SELLER_CIF'),
            'show_email' => Configuration::get('JMARKETPLACE_SELLER_EMAIL'),
            'show_phone' => Configuration::get('JMARKETPLACE_SELLER_PHONE'),
            'show_fax' => Configuration::get('JMARKETPLACE_SELLER_FAX'),
            'show_country' => Configuration::get('JMARKETPLACE_SELLER_COUNTRY'),
            'show_state' => Configuration::get('JMARKETPLACE_SELLER_STATE'),
            'show_postcode' => Configuration::get('JMARKETPLACE_SELLER_POSTAL_CODE'),
            'show_city' => Configuration::get('JMARKETPLACE_SELLER_CITY'),
            'show_address' => Configuration::get('JMARKETPLACE_SELLER_ADDRESS'),
            'show_language' => Configuration::get('JMARKETPLACE_SELLER_LANGUAGE'),
            'show_short_description' => Configuration::get('JMARKETPLACE_SELLER_SHORT_DESCRIPTION'),
            'show_description' => Configuration::get('JMARKETPLACE_SELLER_DESCRIPTION'),
            'show_meta_title' => Configuration::get('JMARKETPLACE_SELLER_META_TITLE'),
            'show_meta_description' => Configuration::get('JMARKETPLACE_SELLER_META_DESCRIPTION'),
            'show_meta_keywords' => Configuration::get('JMARKETPLACE_SELLER_META_KEYWORDS'),
            'show_logo' => Configuration::get('JMARKETPLACE_SELLER_LOGO'),
            'show_banner' => Configuration::get('JMARKETPLACE_SELLER_BANNER'),
            'show_terms' => Configuration::get('JMARKETPLACE_SELLER_ACCEPT_TERMS'),
            // required fields
            'name_required' => Configuration::get('JMARKETPLACE_SELLER_REQUIRED_NAME'),
            'shop_required' => Configuration::get('JMARKETPLACE_SELLER_REQUIRED_SHOP'),
            'cif_required' => Configuration::get('JMARKETPLACE_SELLER_REQUIRED_CIF'),
            'email_required' => Configuration::get('JMARKETPLACE_SELLER_REQUIRED_EMAIL'),
            'lang_required' => Configuration::get('JMARKETPLACE_SELLER_REQUIRED_LANG'),
            'phone_required' => Configuration::get('JMARKETPLACE_SELLER_REQUIRED_PHONE'),
            'fax_required' => Configuration::get('JMARKETPLACE_SELLER_REQUIRED_FAX'),
            'address_required' => Configuration::get('JMARKETPLACE_SELLER_REQUIRED_ADDRESS'),
            'country_required' => Configuration::get('JMARKETPLACE_SELLER_REQUIRED_COUNTRY'),
            'state_required' => Configuration::get('JMARKETPLACE_SELLER_REQUIRED_STATE'),
            'postcode_required' => Configuration::get('JMARKETPLACE_SELLER_REQUIRED_POSTCODE'),
            'city_required' => Configuration::get('JMARKETPLACE_SELLER_REQUIRED_CITY'),
            'short_description_required' => Configuration::get('JMARKETPLACE_SELLER_REQUIRED_SHORT_DESCRIPTION'),
            'description_required' => Configuration::get('JMARKETPLACE_SELLER_REQUIRED_DESCRIPTION'),
            'image_required' => Configuration::get('JMARKETPLACE_SELLER_REQUIRED_IMAGE'),
            'banner_required' => Configuration::get('JMARKETPLACE_SELLER_REQUIRED_BANNER'),
            // pendiente ver que hacer
            'show_import_product' => Configuration::get('JMARKETPLACE_SELLER_IMPORT_PROD'),
            'show_orders' => Configuration::get('JMARKETPLACE_SHOW_ORDERS'),
            'show_edit_seller_account' => Configuration::get('JMARKETPLACE_SELLER_EDIT'),
            'show_contact' => Configuration::get('JMARKETPLACE_SHOW_CONTACT'),
            'show_manage_orders' => Configuration::get('JMARKETPLACE_SHOW_MANAGE_ORDERS'),
            'show_manage_carriers' => Configuration::get('JMARKETPLACE_SHOW_MANAGE_CARRIER'),
            'show_dashboard' => Configuration::get('JMARKETPLACE_SHOW_DASHBOARD'),
            'show_seller_invoice' => Configuration::get('JMARKETPLACE_SHOW_SELLER_INVOICE'),
            // /
            'show_menu_top' => Configuration::get('JMARKETPLACE_MENU_TOP'),
            'show_menu_options' => Configuration::get('JMARKETPLACE_MENU_OPTIONS'),
            'menu_options_status' => $menu_options_status,
            'use_icons' => Configuration::get('JMARKETPLACE_ICONS'),
            'languages' => Language::getLanguages(),
            'show_contact' => Configuration::get('JMARKETPLACE_SHOW_CONTACT'),
            'customer_name' => $customer->firstname . ' ' . $customer->lastname,
            'customer_email' => $customer->email,
            'id_lang' => $this->context->language->id,
            'languages' => Language::getLanguages(),
            'action' => Tools::getValue('action'),
            'id_module' => Module::getModuleIdByName('jmarketplace'),
            'seller_validate' => 1,
        ]);

        if (Configuration::get('JMARKETPLACE_SELLER_ACCEPT_TERMS') == 1) {
            $cms = new CMS(Configuration::get('JMARKETPLACE_SELLER_CMS_TERMS'), $this->context->language->id);
            $cms_link = $this->context->link->getCMSLink($cms, $cms->link_rewrite, Configuration::get('PS_SSL_ENABLED'));

            if (!strpos($cms_link, '?')) {
                $cms_link .= '?content_only=1';
            } else {
                $cms_link .= '&content_only=1';
            }

            $this->context->smarty->assign([
                'cms_name' => $cms->meta_title,
                'cms_link' => $cms_link,
            ]);
        }

        $this->setTemplate('module:jmarketplace/views/templates/front/seller.tpl');
    }
}

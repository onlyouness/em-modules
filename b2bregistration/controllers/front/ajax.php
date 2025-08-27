<?php
/**
 * NOTICE OF LICENSE
 *
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FME Modules
 *  @copyright 2020 fmemodules All right reserved
 *  @license   FMM Modules
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class B2bregistrationAjaxModuleFrontController extends ModuleFrontController
{
    public $ajax = false;

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->ajax = Tools::getValue('ajax');
    }

    public function init()
    {
        parent::init();
        if (!$this->context->customer->logged && !$this->ajax) {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function initContent()
    {
        parent::initContent();
        if (Tools::getValue('action') && Tools::getValue('action') == 'download') {
            $id_file = base64_decode(Tools::getValue('field'));
            $id_customer = base64_decode(Tools::getValue('me'));
            if ($id_file && $id_customer) {
                b2bCustomFields::downloadAttachment($id_file, $id_customer);
            } else {
                return false;
            }
        }
    }
}

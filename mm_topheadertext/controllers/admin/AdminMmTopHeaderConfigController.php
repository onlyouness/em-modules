<?php

class AdminMmTopHeaderConfigController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminModules').'&configure=mm_topheadertext');
        $this->bootstrap = true;
    }

}

<?php

namespace Hp\BulkPriceChange\Controller;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class AdminBulkPriceController extends FrameworkBundleAdminController
{
    public function indexAction()
    {
        return $this->render('@Modules/bulkpricechange/views/templates/admin/index.html.twig', [
            'enableSidebar' => true,
            'layoutTitle'   => 'Bulk Price Change',
        ]);
    }
}
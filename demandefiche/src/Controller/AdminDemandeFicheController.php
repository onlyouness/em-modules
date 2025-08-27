<?php

declare (strict_types = 1);

namespace Hp\Demandefiche\Controller;

use DbQuery;
use Db;
use Tools;
use Context;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class AdminDemandeFicheController extends FrameworkBundleAdminController
{
    public function indexAction()
    {
        $sql = new DbQuery();
        $sql->select('*')
            ->from('demande_fiche', 'd')
            ->orderBy('b.file_id DESC');
        $demandes = Db::getInstance()->executeS($sql);

        return $this->render('@Modules/demandefiche/views/templates/admin/index.html.twig', [
            'enableSidebar' => true,
            'layoutTitle'   => $this->trans('Demande', 'Modules.DemandeFiche.Admin'),
            'demandes'         => $demandes,
        ]);
    }
}

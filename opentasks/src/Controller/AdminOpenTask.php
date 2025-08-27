<?php

namespace Hp\Opentasks\Controller;

use Hp\Opentasks\Grid\Filters\TasksFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class AdminOpenTask extends FrameworkBundleAdminController
{

    public function indexAction( TasksFilters $tasksFilters)
    {
        $gridFactory = $this->get('opentasks.grid.grid_factory');

        // Get the grid using the filters
        $grid = $gridFactory->getGrid($tasksFilters);

        // Render the template with the grid data
        return $this->render('@Modules/opentasks/views/templates/admin/task.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Liste Des Articles', 'Modules.OpenArticle.Admin'),
            'articleGrid' => $this->presentGrid($grid),
        ]);
    }
}
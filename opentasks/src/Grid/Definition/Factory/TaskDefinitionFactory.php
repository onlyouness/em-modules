<?php
declare(strict_types=1);
namespace Hp\Opentasks\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\IdentifierColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;

class TaskDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'open_task';
    /**
     * @return string
     */
    protected function getId()
    {
        return self::GRID_ID;
    }
    /**
     * @return string
     */
    protected function getName()
    {
        return $this->trans('Mes Tasks', [], 'Modules.OpenTasks.Admin');
    }
    /**
     * @return ColumnCollectionInterface
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new IdentifierColumn('id'))
                ->setName($this->trans('id', [], 'Admin.Global'))
                ->setOptions([
                    'identifier_field' => 'id',
                    'bulk_field' => 'id',
                    'with_bulk_field' => true,
                    'clickable' => false,
                ])
            )
            ->add((new DataColumn('title'))
                ->setName($this->trans('title', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'title',
                ])
            )
            ->add((new DataColumn('description'))
                ->setName($this->trans('description', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'description',
                ])
            )

            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add(
                            (new LinkRowAction('edit'))
                                ->setName($this->trans('Edit', [], 'Admin.Global'))
                                ->setIcon('edit')
                                ->setOptions([
                                    'route' => 'oil_opentasks_edit',
                                    'route_param_name' => 'taskId',
                                    'route_param_field' => 'task_id',
                                    'clickable_row' => true,
                                ])
                        )

                        ->add(
                            (new SubmitRowAction('delete'))
                                ->setName($this->trans('Delete', [], 'Admin.Global'))
                                ->setIcon('delete')
                                ->setOptions([
                                    'route' => 'oil_opentasks_delete',
                                    'route_param_name' => 'taskId',
                                    'route_param_field' => 'task_id',
                                    'confirm_message' => $this->trans('Delete selected item', [], 'Admin.Notifications.Warning'),
                                ])
                        )
                ])
            );
    }
}
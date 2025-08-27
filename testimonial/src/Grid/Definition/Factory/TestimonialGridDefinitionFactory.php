<?php
namespace Hp\Testimonial\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;

class TestimonialGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'testimonial';

    public function getId()
    {
        return self::GRID_ID;
    }
    public function getName()
    {
        return $this->trans('Testimonial', [], 'Modules.Testimonial.Admin');
    }
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                    ->setOptions([
                        'bulk_field' => 'id',
                    ])
            )
            ->add((new DataColumn('id'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id',
                    ])
            )
            ->add((new DataColumn('name'))
                    ->setName($this->trans('Name', [], 'Modules.Demodoctrine.Admin'))
                    ->setOptions([
                        'field' => 'name',
                    ])
            )
            ->add((new DataColumn('message'))
                    ->setName($this->trans('Message', [], 'Modules.Demodoctrine.Admin'))
                    ->setOptions([
                        'field' => 'message',
                    ])
            )
            ->add((new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add((new LinkRowAction('edit'))
                                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                    ->setIcon('edit')
                                    ->setOptions([
                                        'route'             => 'mm_testimonial_edit',
                                        'route_param_name'  => 'id',
                                        'route_param_field' => 'id',
                                        'clickable_row'     => true,
                                    ])
                            )
                            ->add((new SubmitRowAction('delete'))
                                    ->setName($this->trans('Delete', [], 'Admin.Actions'))
                                    ->setIcon('delete')
                                    ->setOptions([
                                        'method'            => 'DELETE',
                                        'route'             => 'mm_testimonial_delete',
                                        'route_param_name'  => 'id',
                                        'route_param_field' => 'id',
                                        'confirm_message'   => $this->trans(
                                            'Delete selected item?',
                                            [],
                                            'Admin.Notifications.Warning'
                                        ),
                                    ])
                            ),
                    ])
            )
        ;
    }
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('id', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr'     => [
                            'placeholder' => $this->trans('ID', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('id')
            )
            ->add((new Filter('name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr'     => [
                            'placeholder' => $this->trans('name', [], 'Modules.Demodoctrine.Admin'),
                        ],
                    ])
                    ->setAssociatedColumn('name')
            )
            ->add((new Filter('message', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr'     => [
                            'placeholder' => $this->trans('Message', [], 'Modules.Demodoctrine.Admin'),
                        ],
                    ])
                    ->setAssociatedColumn('message')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route'        => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route'     => 'mm_testimonial_index',
                    ])
                    ->setAssociatedColumn('actions')
            )
        ;
    }

}

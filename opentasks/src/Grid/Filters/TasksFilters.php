<?php
declare(strict_types=1);
namespace Hp\Opentasks\Grid\Filters;
use Hp\Opentasks\Grid\Definition\Factory\TaskDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

class TasksFilters extends Filters
{
    /** @var string */
    protected $filterId = TaskDefinitionFactory::GRID_ID;
    /**
     * {@inheritdoc}
     */

    public static function getDefaults(): array
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'sortBy' => 'id',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
<?php

declare(strict_types = 1);
namespace Hp\Testimonial\Filters;

use Hp\Testimonial\Grid\Definition\Factory\TestimonialGridDefinitionFactory as FactoryTestimonialGridDefinitionFactory;

use PrestaShop\PrestaShop\Core\Search\Filters;

class TestimonialFilters extends Filters
{
    protected $filterId = FactoryTestimonialGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 11,
            'offset' => 0,
            'orderBy' => 'id',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
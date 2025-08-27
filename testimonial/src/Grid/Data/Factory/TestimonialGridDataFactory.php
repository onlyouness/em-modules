<?php
namespace Hp\Testimonial\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

class TestimonialGridDataFactory implements GridDataFactoryInterface{

  /**
     * @var GridDataFactoryInterface 
     */
    private $gridDataFactory;

    /**
     * @param GridDataFactoryInterface $gridDataFactory 
    */

    public function __construct(GridDataFactoryInterface $gridDataFactory)
    {
        $this->gridDataFactory = $gridDataFactory;
    }

    /**
     * @var SearchCriteriaInterface $searchCriteria
     * @return GridData
     */
    public function getData(SearchCriteriaInterface $searchCriteria) {
        $testimonialData = $this->gridDataFactory->getData($searchCriteria);
        $modificatiedRecords = $this->applyModification($testimonialData->getRecords()->all());

        return new GridData(
            new RecordCollection($modificatiedRecords),
            $testimonialData->getRecordsTotal(),
            $testimonialData->getQuery()
        );
    }
    public function  applyModification(array $rows){
        return $rows;

    }

}
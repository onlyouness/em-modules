<?php
namespace Hp\Opentasks\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

class TaskGridDataFactory implements GridDataFactoryInterface
{

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

    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $taskData = $this->gridDataFactory->getData($searchCriteria);
        $modificatiedRecords = $this->applyModification($taskData->getRecords()->all());
        return new GridData(
            new RecordCollection($modificatiedRecords),
            $taskData->getRecordsTotal(),
            $taskData->getQuery()
        );
    }
    public function  applyModification(array $rows){
        return $rows;

    }
}
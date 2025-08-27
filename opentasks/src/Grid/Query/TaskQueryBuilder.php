<?php

namespace Hp\Opentasks\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\DoctrineFilterApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\SqlFilters;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
class TaskQueryBuilder extends AbstractDoctrineQueryBuilder {
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var int
     */

    private $contextLanguageId;

    /**
     * @var DoctrineFilterApplicatorInterface
     */

    private $filterApplicator;


    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        int $contextLanguageId,
        DoctrineFilterApplicatorInterface $filterApplicator
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextLanguageId = $contextLanguageId;
        $this->filterApplicator = $filterApplicator;
    }
    /**
     * @var SearchCriteriaInterface $searchCriteria
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('oa.`id` , oa.`position`, oa.`active` ')
            ->addSelect('oal.`lang_id` , oal.`title`,oal.`task_id`,oal.`description` ');

        $this->searchCriteriaApplicator->applyPagination($searchCriteria,$qb)->applySorting($searchCriteria,$qb);

        return $qb;
    }

    /**
     * @var SearchCriteriaInterface $searchCriteria
     * @return QueryBuilder
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(oa.`id`)');

        return $qb;
    }

    /**
     * Get query builder.
     *
     * @param array $filterValues
     *
     * @return QueryBuilder
     */

    public function getQueryBuilder(array $filterValues): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'open_tasks', 'oa')
            ->leftJoin(
                'oa',
                $this->dbPrefix . 'open_tasks_lang',
                'oal',
                'oal.`task_id` = oa.`id` AND oal.`lang_id` = :lang_id '
            );

        $sqlFilters = new SqlFilters();
        $sqlFilters->addFilter(
            'id',
            'oa.`id`',
            SqlFilters::WHERE_STRICT
        );
        $this->filterApplicator->apply($qb, $sqlFilters, $filterValues);
        $qb->setParameter('lang_id', $this->contextLanguageId);
        foreach ($filterValues as $filterName => $filter) {
            if ('active' === $filterName) {
                $qb->andWhere('oa.`active` = :active');
                $qb->setParameter('active', $filter);
                continue;
            }
            if ('title' === $filterName) {
                $qb->andWhere('oal.`title` LIKE :title');
                $qb->setParameter('title', '%' . $filter . '%');
                continue;
            }
            if ('description' === $filterName) {
                $qb->andWhere('oal.`description` LIKE :description');
                $qb->setParameter('description', '%' . $filter . '%');
                continue;
            }
            if ('position' === $filterName) {
                $qb->andWhere('oa.`position` LIKE :position');
                $qb->setParameter('position', '%' . $filter . '%');
                continue;
            }
        }
        return $qb;
    }
}

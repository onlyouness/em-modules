<?php
declare(strict_types =1);
namespace HP\Services\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ServiceLangRepository extends EntityRepository
{

    public function findByLang($langId)
    {
        return $this->createQueryBuilder('sl')
            ->andWhere('sl.idLang = :langId')
            ->setParameter('langId', $langId)
            ->getQuery()
            ->getOneOrNullResult()->getResult();
    }
}

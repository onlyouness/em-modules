<?php
namespace Hp\Mmreassurances\Repository;

use Doctrine\ORM\EntityRepository;

class ReassuranceRepository extends EntityRepository
{
    public function findReassuranceByLang(int $langId)
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f.id', 'f.image', 'f.active', 'fl.title', 'fl.description')
            ->leftJoin('f.bannerLangs', 'fl')
            ->where('fl.lang = :langId')
            ->setParameter('langId', $langId)
            ->getQuery();
        return $qb->getResult();
    }
    public function findReassuranceByLangAndActive(int $langId)
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f.id', 'f.image', 'f.active', 'fl.title', 'fl.description')
            ->leftJoin('f.bannerLangs', 'fl')
            ->where('fl.lang = :langId')
            ->andWhere('f.active = 1')
            ->setParameter('langId', $langId)
            ->getQuery();
        return $qb->getResult();
    }
    public function findReassuranceLangById(int $id)
    {
        $qb = $this->createQueryBuilder('f')
            ->select('fl.title', 'fl.description')
            ->leftJoin('f', 'fl')
            ->where('f.id = :id')
            ->setParameter('id', $id)
            ->getQuery();
        return $qb->getResult();
    }
}

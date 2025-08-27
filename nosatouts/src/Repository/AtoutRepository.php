<?php
namespace Hp\Nosatouts\Repository;

use Doctrine\ORM\EntityRepository;

class AtoutRepository extends EntityRepository
{
    public function findAtoutByLang(int $langId)
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f.id', 'f.image', 'f.active', 'fl.title', 'fl.description')
            ->leftJoin('f.bannerLangs', 'fl')
            ->where('fl.lang = :langId')
            ->setParameter('langId', $langId)
            ->getQuery();
        return $qb->getResult();
    }

}

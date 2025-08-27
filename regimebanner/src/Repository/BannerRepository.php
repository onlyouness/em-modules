<?php
namespace Hp\Regimebanner\Repository;

use Doctrine\ORM\EntityRepository;

class BannerRepository extends EntityRepository
{

    public function findActive(int $section,int $group)
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f.active') 
            ->where('f.section = :section') 
            ->andWhere('f.group = :group') 
            ->setParameter('section', $section)
            ->setParameter('group', $group)
            ->getQuery();

        return $qb->getResult();
        }
    public function findBannerByLangAndActiveAndGroup(int $langId,int $groupId)
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f.id,f.group', 'f.section','f.link', 'f.image','f.active', 'fl.title', 'fl.description') 
            ->leftJoin('f.bannerLangs', 'fl') 
            ->where('fl.lang = :langId')
            ->andWhere('f.active = 1')
            ->andWhere('f.group = :groupId')
            ->setParameter('langId', $langId)
            ->setParameter('groupId', $groupId)
            ->getQuery();
        return $qb->getResult();
    }

}

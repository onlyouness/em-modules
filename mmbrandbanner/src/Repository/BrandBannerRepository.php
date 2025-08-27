<?php

namespace Hp\Mmbrandbanner\Repository;

use Doctrine\ORM\EntityRepository;

class BrandBannerRepository extends EntityRepository
{
    public function findBanners()
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b.active', 'b.id','b.title','b.description','b.image') 
            // ->leftJoin('PrestaShopBundle\Entity\Manufacturer', 'm', 'WITH', 'b.manufacturer = m.id_manufacturer')
            // ->addSelect('m.name as manufacturerName') 
            ->getQuery();
        return $qb->getResult();
    }

}
<?php
namespace Hp\Brandproducts\Repository;

use Doctrine\ORM\EntityRepository;

class BrandProductRepository extends EntityRepository
{
    public function findFaqsByLang(int $langId)
    {
        // Create the query builder to fetch FAQs and the related FaqLang
        $qb = $this->createQueryBuilder('f')
            ->select('f.id,f.group', 'f.section', 'f.active', 'fl.question', 'fl.response') // Select necessary fields
            ->leftJoin('f.faqLangs', 'fl') // Join with the FaqLang entity
            ->where('fl.lang = :langId') // Filter by the language ID
            ->setParameter('langId', $langId)
            ->getQuery();

        return $qb->getResult();
        }
}
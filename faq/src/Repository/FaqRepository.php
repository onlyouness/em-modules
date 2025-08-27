<?php
namespace Hp\Faq\Repository;

use Doctrine\ORM\EntityRepository;

class FaqRepository extends EntityRepository
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
    public function findFaqsByLangAndActiveAndGroup(int $langId,int $groupId)
    {
        // Create the query builder to fetch FAQs and the related FaqLang
        $qb = $this->createQueryBuilder('f')
            ->select('f.id,f.group', 'f.section', 'f.active', 'fl.question', 'fl.response') // Select necessary fields
            ->leftJoin('f.faqLangs', 'fl') // Join with the FaqLang entity
            ->where('fl.lang = :langId') // Filter by the language ID
            ->andWhere('f.active = 1')
            ->andWhere('f.group = :groupId')
            ->setParameter('langId', $langId)
            ->setParameter('groupId', $groupId)
            ->getQuery();

        return $qb->getResult();
    }

}

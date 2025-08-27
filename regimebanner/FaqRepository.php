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

    public function findFaqsByLangAndActiveAndGroup(int $langId, int $groupId)

    {

        // Create the query builder to fetch FAQs and the related FaqLang

        $qb = $this->createQueryBuilder('f')

            ->select('f.id', 'f.group', 'f.section', 'f.active', 'fl.question', 'fl.response')

            ->leftJoin('f.faqLangs', 'fl')

            ->where('fl.lang = :langId')

            ->andWhere('f.active = 1');

    

        

            $qb->andWhere('f.group = :groupId')

            ->setParameter('groupId', $groupId);

        

        $qb->setParameter('langId', $langId)

            ->orderBy('f.id', 'DESC')

            ->setMaxResults(7);

    

        return $qb->getQuery()->getResult();

    }



    public function findFaqByGroupAndLang(int $langId, int $groupId)

    {

        $qb = $this->createQueryBuilder('f')

            ->select('f.id', 'f.group', 'f.section', 'f.active', 'fl.question', 'fl.response')

            ->leftJoin('f.faqLangs', 'fl')

            ->where('fl.lang = :langId')

            ->andWhere('f.active = 1');

        if ($groupId == 3) {

            $qb->andWhere('f.group = :groupId');

        } else {

            $qb->andWhere('f.group != 3');

        }

        $qb->setParameter('langId', $langId);

        if ($groupId == 3) {

            $qb->setParameter('groupId', $groupId);

        }

            $qb->orderBy('f.id', 'DESC');

        return $qb->getQuery()->getResult();

    }

    

}


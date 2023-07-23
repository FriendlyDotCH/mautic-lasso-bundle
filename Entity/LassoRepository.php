<?php

namespace MauticPlugin\MauticLassoBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

/**
 * LassoRepository
 */
class LassoRepository extends CommonRepository
{
    public function getByPublished($isPublished = true)
    {
        $q = $this->createQueryBuilder('f');
        $q->select('l')
        ->from(Lasso::class, 'l')
        ->where('l.isPublished = :isPublished')
        ->setParameters(['isPublished' => $isPublished])
        ;
        return $q->getQuery()->getResult();
    }

    public function getByCampaign($campaignId, $isPublished = true)
    {
        $q = $this->createQueryBuilder('f');
        $q->select('l')
        ->from(Lasso::class, 'l')
        ->where('l.campaignLasso = :campaignId')
        ->andWhere('l.isPublished = :isPublished')
        ->setParameters(['campaignId' => $campaignId, 'isPublished' => $isPublished])
        ;
        return $q->getQuery()->getResult();
    }
}
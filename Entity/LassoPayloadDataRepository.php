<?php

namespace MauticPlugin\MauticLassoBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

class LassoPayloadDataRepository extends CommonRepository
{
    public function getByCampaignId($campaignId)
    {
        $q = $this->createQueryBuilder('f');
        $q->select('ld')
        ->from(LassoPayloadData::class, 'ld')
        ->where('ld.campaignId = :campaign')
        ->setParameters(['campaign' => $campaignId])
        ;
        return $q->getQuery()->getOneOrNullResult();
    }
}
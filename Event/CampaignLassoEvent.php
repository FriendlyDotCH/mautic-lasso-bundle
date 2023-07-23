<?php

/*
 * @copyright   2016 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticLassoBundle\Event;

use Mautic\CoreBundle\Event\CommonEvent;
use MauticPlugin\MauticLassoBundle\Entity\CampaignLasso;

/**
 * Class CampaignLassoEvent.
 */
class CampaignLassoEvent extends CommonEvent
{
    /**
     * @param bool|false $isNew
     */
    public function __construct(CampaignLasso $lasso, $isNew = false)
    {
        $this->entity = $lasso;
        $this->isNew  = $isNew;
    }

    /**
     * Returns the CampaignLasso entity.
     *
     * @return CampaignLassoEvent
     */
    public function getCampaignLasso()
    {
        return $this->entity;
    }

    /**
     * Sets the CampaignLasso entity.
     */
    public function setCampaignLasso(CampaignLasso $lasso)
    {
        $this->entity = $lasso;
    }
}

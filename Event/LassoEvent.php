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
use MauticPlugin\MauticLassoBundle\Entity\Lasso;

/**
 * Class LassoEvent.
 */
class LassoEvent extends CommonEvent
{
    /**
     * @param bool|false $isNew
     */
    public function __construct(Lasso $lasso, $isNew = false)
    {
        $this->entity = $lasso;
        $this->isNew  = $isNew;
    }

    /**
     * Returns the Lasso entity.
     *
     * @return LassoEvent
     */
    public function getLasso()
    {
        return $this->entity;
    }

    /**
     * Sets the Lasso entity.
     */
    public function setLasso(Lasso $lasso)
    {
        $this->entity = $lasso;
    }
}

<?php

namespace MauticPlugin\MauticLassoBundle\Controller;

use Mautic\CoreBundle\Controller\AbstractStandardFormController;
use Mautic\CoreBundle\Controller\CommonController;
use Mautic\CoreBundle\Form\Type\DateRangeType;
use MauticPlugin\MauticSpintaxBundle\Model\EmailVariation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Mautic\FormBundle\Controller\FormController;
use Mautic\CoreBundle\Model\FormModel;


/**
 * Class LassoPayloadDataController.
 */
class LassoPayloadDataController extends AbstractStandardFormController
{
    /**
     * @return string
     */
    protected function getControllerBase()
    {
        return 'MauticLassoBundle:LassoPayloadData';
    }

    /**
     * @return string
     */
    protected function getModelName()
    {
        return 'lassopayloaddata';
    }

    /**
     * @return string
     */
    protected function getDefaultOrderColumn()
    {
        return 'campaignId';
    }

    /**
     * @param int $page
     *
     * @return JsonResponse|RedirectResponse|Response
     */
    public function indexAction($page = 1)
    {        
        return parent::indexStandard($page);
    }
}
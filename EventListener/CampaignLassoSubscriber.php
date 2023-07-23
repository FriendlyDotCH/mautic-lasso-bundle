<?php

/*
 * @copyright   2016 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticLassoBundle\EventListener;

use MauticPlugin\MauticLassoBundle\Helper\RequestHelper;
use Mautic\AssetBundle\Helper\TokenHelper as AssetTokenHelper;
use Mautic\CoreBundle\Event as MauticEvents;
use Mautic\CoreBundle\Helper\InputHelper;
use Mautic\CoreBundle\Helper\IpLookupHelper;
use Mautic\CoreBundle\Model\AuditLogModel;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Helper\TokenHelper;
use Mautic\PageBundle\Entity\Trackable;
use Mautic\PageBundle\Helper\TokenHelper as PageTokenHelper;
use Mautic\PageBundle\Model\TrackableModel;
use MauticPlugin\MauticLassoBundle\Event\LassoEvent;
use MauticPlugin\MauticLassoBundle\Model\LassoModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\Container;

class CampaignLassoSubscriber implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var IpLookupHelper
     */
    private $ipHelper;

    /**
     * @var AuditLogModel
     */
    private $auditLogModel;

    /**
     * @var TrackableModel
     */
    private $trackableModel;

    /**
     * @var PageTokenHelper
     */
    private $pageTokenHelper;

    /**
     * @var AssetTokenHelper
     */
    private $assetTokenHelper;

    /**
     * @var LassoModel
     */
    private $lassoModel;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var Container
     */
    private $container;

    public function __construct(
        RouterInterface $router,
        IpLookupHelper $ipLookupHelper,
        AuditLogModel $auditLogModel,
        TrackableModel $trackableModel,
        PageTokenHelper $pageTokenHelper,
        AssetTokenHelper $assetTokenHelper,
        LassoModel $lassoModel,
        RequestStack $requestStack,
        Container $container
    ) {
        $this->router           = $router;
        $this->ipHelper         = $ipLookupHelper;
        $this->auditLogModel    = $auditLogModel;
        $this->trackableModel   = $trackableModel;
        $this->pageTokenHelper  = $pageTokenHelper;
        $this->assetTokenHelper = $assetTokenHelper;
        $this->lassoModel       = $lassoModel;
        $this->requestStack     = $requestStack;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST          => ['onKernelRequest', 0],
            'mautic.campaignlasso_post_save'         => ['onLassoPostSave', 0],
            'mautic.campaignlasso_delete'       => ['onLassoDelete', 0],
        ];
    }

    /*
     * Check and hijack the form's generate link if the ID has mf- in it
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Ignore if not an API request
        if (!RequestHelper::isLasoRequest($request)) {
            return;
        }
        $this->container->get('mautic.lasso.process_webhook_request')->processRawData($request);
        
        return;
    }

    /**
     * Add an entry to the audit log.
     */
    public function onLassoPostSave(LassoEvent $event)
    {
        $entity = $event->getLasso();
        if ($details = $event->getChanges()) {
            $log = [
                'bundle'    => 'lasso',
                'object'    => 'lasso',
                'objectId'  => $entity->getId(),
                'action'    => ($event->isNew()) ? 'create' : 'update',
                'details'   => $details,
                'ipAddress' => $this->ipHelper->getIpAddressFromRequest(),
            ];
            
            $this->auditLogModel->writeToLog($log);
            
        }
    }

    /**
     * Add a delete entry to the audit log.
     */
    public function onLassoDelete(LassoEvent $event)
    {
        $entity = $event->getCampaignLasso();
        $log    = [
            'bundle'    => 'lasso',
            'object'    => 'lasso',
            'objectId'  => $entity->deletedId,
            'action'    => 'delete',
            'details'   => ['name' => $entity->getName()],
            'ipAddress' => $this->ipHelper->getIpAddressFromRequest(),
        ];
        $this->auditLogModel->writeToLog($log);
    }

}

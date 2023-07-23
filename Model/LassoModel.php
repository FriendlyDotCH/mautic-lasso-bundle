<?php

/*
 * @copyright   2016 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticLassoBundle\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Event\TokenReplacementEvent;
use Mautic\CoreBundle\Helper\Chart\ChartQuery;
use Mautic\CoreBundle\Helper\Chart\LineChart;
use Mautic\CoreBundle\Helper\TemplatingHelper;
use Mautic\CoreBundle\Model\FormModel;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\FieldModel;
use Mautic\LeadBundle\Tracker\ContactTracker;
use Mautic\PageBundle\Model\TrackableModel;
use MauticPlugin\MauticLassoBundle\Entity\Lasso;
use MauticPlugin\MauticLassoBundle\Event\LassoEvent;
use MauticPlugin\MauticLassoBundle\Form\Type\LassoType;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class LassoModel extends FormModel
{
    /**
     * @var ContainerAwareEventDispatcher
     */
    protected $dispatcher;

    /**
     * @var \Mautic\FormBundle\Model\FormModel
     */
    protected $formModel;

    /**
     * @var TrackableModel
     */
    protected $trackableModel;

    /**
     * @var TemplatingHelper
     */
    protected $templating;

    /**
     * @var FieldModel
     */
    protected $leadFieldModel;

    /**
     * @var ContactTracker
     */
    protected $contactTracker;

    /**
     * 
     * @var EntityManager $entityManager
     */
    private static $entityManager;
    /**
     * LassoModel constructor.
     */
    public function __construct(
        \Mautic\FormBundle\Model\FormModel $formModel,
        TrackableModel $trackableModel,
        TemplatingHelper $templating,
        EventDispatcherInterface $dispatcher,
        FieldModel $leadFieldModel,
        ContactTracker $contactTracker,
        EntityManager $entityManager
    ) {
        $this->formModel      = $formModel;
        $this->trackableModel = $trackableModel;
        $this->templating     = $templating;
        $this->dispatcher     = $dispatcher;
        $this->leadFieldModel = $leadFieldModel;
        $this->contactTracker = $contactTracker;
        static::$entityManager = $entityManager;
    }

    /**
     * @return string
     */
    public function getActionRouteBase()
    {
        return 'lasso';
    }

    /**
     * @return string
     */
    public function getPermissionBase()
    {
        return 'lasso:items';
    }

    /**
     * {@inheritdoc}
     *
     * @param object                              $entity
     * @param \Symfony\Component\Form\FormFactory $formFactory
     * @param null                                $action
     * @param array                               $options
     *
     * @throws NotFoundHttpException
     */
    public function createForm($entity, $formFactory, $action = null, $options = [])
    {
        if (!$entity instanceof Lasso) {
            throw new MethodNotAllowedHttpException(['Lasso']);
        }

        if (!empty($action)) {
            $options['action'] = $action;
        }

        return $formFactory->create(LassoType::class, $entity, $options);
    }

    /**
     * {@inheritdoc}
     *
     * @return \MauticPlugin\MauticLassoBundle\Entity\LassoRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository(Lasso::class);
    }

    /**
     * {@inheritdoc}
     *
     * @param null $id
     *
     * @return Lasso
     */
    public function getEntity($id = null)
    {
        if (null === $id) {
            return new Lasso();
        }

        return parent::getEntity($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param Lasso      $entity
     * @param bool|false $unlock
     */
    public function saveEntity($entity, $unlock = true)
    {
        parent::saveEntity($entity, $unlock);
        $this->getRepository()->saveEntity($entity);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool|LassoEvent|void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    protected function dispatchEvent($action, &$entity, $isNew = false, Event $event = null)
    {
        if (!$entity instanceof Lasso) {
            throw new MethodNotAllowedHttpException(['Lasso']);
        }

        switch ($action) {
            case 'pre_save':
                $name = 'mautic.lasso_pre_save';
                break;
            case 'post_save':
                $name = 'mautic.lasso_post_save';
                break;
            case 'pre_delete':
                $name = 'mautic.lasso_pre_delete';
                break;
            case 'post_delete':
                $name = 'mautic.lasso_post_delete';
                break;
            default:
                return null;
        }

        if ($this->dispatcher->hasListeners($name)) {
            if (empty($event)) {
                $event = new LassoEvent($entity, $isNew);
                $event->setEntityManager($this->em);
            }

            $this->dispatcher->dispatch($name, $event);

            return $event;
        } else {
            return null;
        }
    }

    // Get path of the config.php file.
    public function getConfiArray()
    {
        return include dirname(__DIR__).'/Config/config.php';
    }

}

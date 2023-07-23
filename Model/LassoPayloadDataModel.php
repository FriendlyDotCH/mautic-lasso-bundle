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
use MauticPlugin\MauticLassoBundle\Entity\LassoPayloadData;
use MauticPlugin\MauticLassoBundle\Event\LassoEvent;
use MauticPlugin\MauticLassoBundle\Form\Type\LassoType;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class LassoPayloadDataModel extends FormModel
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
    public function __construct(EntityManager $entityManager) {
        static::$entityManager = $entityManager;
    }

    /**
     * @return string
     */
    public function getActionRouteBase()
    {
        return 'lassopayloaddata';
    }

    /**
     * @return string
     */
    public function getPermissionBase()
    {
        return 'lassopayloaddata:items';
    }

    /**
     * {@inheritdoc}
     *
     * @return \MauticPlugin\MauticLassoBundle\Entity\LassoPayloadDataRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository(LassoPayloadData::class);
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

    // Get path of the config.php file.
    public function getConfiArray()
    {
        return include dirname(__DIR__).'/Config/config.php';
    }

}

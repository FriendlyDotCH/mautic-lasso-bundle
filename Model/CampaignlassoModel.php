<?php

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
use MauticPlugin\MauticLassoBundle\Entity\CampaignLasso;
use MauticPlugin\MauticLassoBundle\Event\CampaignLassoEvent;
use MauticPlugin\MauticLassoBundle\Form\Type\CampaignLassoType;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Container;
use Mautic\MauticApi;

class CampaignlassoModel extends FormModel
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
     * @var Container
     */
    private $container;
    /**
     * LassoModel constructor.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getActionRouteBase()
    {
        return 'campaignlasso';
    }

    /**
     * @return string
     */
    public function getPermissionBase()
    {
        return 'campaignlasso:items';
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
        if (!$entity instanceof CampaignLasso) {
            throw new MethodNotAllowedHttpException(['CampaignLasso']);
        }

        if (!empty($action)) {
            $options['action'] = $action;
        }

        $options['core_fields'] = $this->getCoreCustomFields();
        
        return $formFactory->create(CampaignLassoType::class, $entity, $options);
    }

    private function getCoreCustomFields()
    {
        $auth = $this->container->get('mautic.other.lasso.api_auth')->basicAuth();
        $api         = new MauticApi();
        $apiUrl      = 'http://'.$_SERVER['SERVER_NAME'].'/api';
        $contactApi = $api->newApi('contacts', $auth, $apiUrl);

        // Get all the core custom fields of a contact.
        $fields = $contactApi->getFieldList();
        $coreFields = [];
        foreach($fields as $field)
        {
            if($field['group'] === 'core'){
                $coreFields[$field['alias']] = $field['group'].' '.$field['label'];
            }
        }

        return $coreFields;
    }

    /**
     * {@inheritdoc}
     *
     * @return \MauticPlugin\MauticLassoBundle\Entity\CampaignLassoRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository(CampaignLasso::class);
    }

    /**
     * {@inheritdoc}
     *
     * @param null $id
     *
     * @return CampaignLasso
     */
    public function getEntity($id = null)
    {
        if (null === $id) {
            return new CampaignLasso();
        }

        return parent::getEntity($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param CampaignLasso      $entity
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
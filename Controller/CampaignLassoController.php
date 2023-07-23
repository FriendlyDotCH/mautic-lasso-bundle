<?php

namespace MauticPlugin\MauticLassoBundle\Controller;

use Doctrine\ORM\EntityManager;
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
use Symfony\Component\Form\Form;
use Mautic\MauticApi;

/**
 * Class CampaignLassoController.
 */
class CampaignLassoController extends AbstractStandardFormController
{
    /**
     * @return string
     */
    protected function getControllerBase()
    {
        return 'MauticLassoBundle:CampaignLasso';
    }

    /**
     * @return string
     */
    protected function getModelName()
    {
        return 'campaignlasso';
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

    /**
     * Generates new form and processes post data.
     *
     * @return JsonResponse|Response
     */
    public function newAction()
    {
        return parent::newStandard();
    }

    /**
     * Generates edit form and processes post data.
     *
     * @param int  $objectId
     * @param bool $ignorePost
     *
     * @return JsonResponse|Response
     */
    public function editAction(
        $objectId, 
        $ignorePost = false, 
        $forceTypeSelection = false
    )
    {
        //$request = $this->getRequest();
        $isClone = false;
        $model   = $this->getModel($this->getModelName());
        if (!$model instanceof FormModel) {
            throw new \Exception(get_class($model).' must extend '.FormModel::class);
        }

        $entity = $this->getFormEntity('edit', $objectId, $isClone);
        /*if($campaign = $entity->getCampaignId())
        {
            $entity->setName($campaign->getName());
        }*/
        $viewParameters = [];
        //set the return URL
        $returnUrl      = $this->generateUrl($this->getIndexRoute());
        $page           = $this->get('session')->get('mautic.'.$this->getSessionBase().'.page', 1);
        $viewParameters = ['page' => $page];

        $template = $this->getControllerBase().':'.$this->getPostActionControllerAction('edit');

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => $viewParameters,
            'contentTemplate' => $template,
            'passthroughVars' => [
                'mauticContent' => $this->getJsLoadMethodPrefix(),
            ],
            'entity' => $entity,
        ];
        
        $originalLasso = new ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($entity->getLassos() as $lasso) {
            $originalLasso->add($lasso);
        }
        

         //form not found
         if (null === $entity) {
            return $this->postActionRedirect(
                $this->getPostActionRedirectArguments(
                    array_merge(
                        $postActionVars,
                        [
                            'flashes' => [
                                [
                                    'type'    => 'error',
                                    'msg'     => $this->getTranslatedString('error.notfound'),
                                    'msgVars' => ['%id%' => $objectId],
                                ],
                            ],
                        ]
                    ),
                    'edit'
                )
            );
        } elseif ((!$isClone && !$this->checkActionPermission('edit', $entity)) || ($isClone && !$this->checkActionPermission('create'))) {
            //deny access if the entity is not a clone and don't have permission to edit or is a clone and don't have permission to create
            return $this->accessDenied();
        } elseif (!$isClone && $model->isLocked($entity)) {
            //deny access if the entity is locked
            return $this->isLocked($postActionVars, $entity, $this->getModelName());
        }

        $options = $this->getEntityFormOptions();
        $action  = $this->generateUrl($this->getActionRoute(), ['objectAction' => 'edit', 'objectId' => $objectId]);
        $form    = $model->createForm($entity, $this->get('form.factory'), $action, $options);

        $isPost = !$ignorePost && 'POST' == $this->request->getMethod();
        $this->beforeFormProcessed($entity, $form, 'edit', $isPost, $objectId, $isClone);

        ///Check for a submitted form and process it
        if ($isPost) {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    if ($valid = $this->beforeEntitySave($entity, $form, 'edit', $objectId, $isClone)) {
                     
                        $lassoModel = $this->getModel('lasso');    
                        // remove the relationship between the tag and the Task
                        foreach ($originalLasso as $lasso) {
                            if (false === $entity->getLassos()->contains($lasso)) {
                                
                                $entity->removeLasso($lasso);
                                // if you wanted to delete the lasso entirely, you can also do that
                                $this->getDoctrine()->getManager()->remove($lasso);
                            }else{
                                $lassoModel->saveEntity($lasso);
                            }
                        }
                       
                        
                        $model->saveEntity($entity, $form->get('buttons')->get('save')->isClicked());

                        $this->afterEntitySave($entity, $form, 'edit', $valid);

                        $this->addFlash(
                            'mautic.core.notice.updated',
                            [
                                '%name%'      => $entity->getName(),
                                '%menu_link%' => $this->getIndexRoute(),
                                '%url%'       => $this->generateUrl(
                                    $this->getActionRoute(),
                                    [
                                        'objectAction' => 'edit',
                                        'objectId'     => $entity->getId(),
                                    ]
                                ),
                            ]
                        );

                        if ($entity->getId() !== $objectId) {
                            // No longer a clone - this is important for Apply
                            $objectId = $entity->getId();
                        }

                        if (!$this->isFormApplied($form) && method_exists($this, 'viewAction')) {
                            $viewParameters                    = ['objectId' => $objectId, 'objectAction' => 'view'];
                            $returnUrl                         = $this->generateUrl($this->getActionRoute(), $viewParameters);
                            $postActionVars['contentTemplate'] = $this->getControllerBase().':view';
                        }
                    }

                    $this->afterFormProcessed($valid, $entity, $form, 'edit', $isClone);
                }
            } else {
                if (!$isClone) {
                    //unlock the entity
                    $model->unlockEntity($entity);
                }

                $returnUrl = $this->generateUrl($this->getIndexRoute(), $viewParameters);
            }

            if ($cancelled || ($valid && !$this->isFormApplied($form))) {
                return $this->postActionRedirect(
                    $this->getPostActionRedirectArguments(
                        array_merge(
                            $postActionVars,
                            [
                                'returnUrl'      => $returnUrl,
                                'viewParameters' => $viewParameters,
                            ]
                        ),
                        'edit'
                    )
                );
            } elseif ($valid) {
                // Rebuild the form with new action so that apply doesn't keep creating a clone
                $action = $this->generateUrl($this->getActionRoute(), ['objectAction' => 'edit', 'objectId' => $entity->getId()]);
                $form   = $model->createForm($entity, $this->get('form.factory'), $action);
                $this->beforeFormProcessed($entity, $form, 'edit', false, $isClone);
            }
        } elseif (!$isClone) {
            $model->lockEntity($entity);
        }

        $delegateArgs = [
            'viewParameters' => [
                'permissionBase'  => $this->getPermissionBase(),
                'mauticContent'   => $this->getJsLoadMethodPrefix(),
                'actionRoute'     => $this->getActionRoute(),
                'indexRoute'      => $this->getIndexRoute(),
                'tablePrefix'     => $model->getRepository()->getTableAlias(),
                'modelName'       => $this->getModelName(),
                'translationBase' => $this->getTranslationBase(),
                'tmpl'            => $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index',
                'entity'          => $entity,
                'form'            => $this->getFormView($form, 'edit'),
            ],
            'contentTemplate' => $this->getTemplateName('form.html.php'),
            'passthroughVars' => [
                'mauticContent' => $this->getJsLoadMethodPrefix(),
                'route'         => $this->generateUrl(
                    $this->getActionRoute(),
                    [
                        'objectAction' => 'edit',
                        'objectId'     => $entity->getId(),
                    ]
                ),
                'validationError' => $this->getFormErrorForBuilder($form),
            ],
            'objectId' => $objectId,
            'entity'   => $entity,
        ];

        return $this->delegateView(
            $this->getViewArguments($delegateArgs, 'edit')
        );
    }    

    /**
     * Displays details on a lasso.
     *
     * @param $objectId
     *
     * @return array|JsonResponse|RedirectResponse|Response
     */
    public function viewAction($objectId)
    {
        return parent::viewStandard($objectId, 'campaignlasso', 'plugin.campaignlasso');
    }

    /**
     * Clone an entity.
     *
     * @param int $objectId
     *
     * @return JsonResponse|RedirectResponse|Response
     */
    public function cloneAction($objectId)
    {
        return parent::cloneStandard($objectId);
    }

    /**
     * Deletes the entity.
     *
     * @param int $objectId
     *
     * @return JsonResponse|RedirectResponse
     */
    public function deleteAction($objectId)
    {
        return parent::deleteStandard($objectId);
    }

    /**
     * Deletes a group of entities.
     *
     * @return JsonResponse|RedirectResponse
     */
    public function batchDeleteAction()
    {
        return parent::batchDeleteStandard();
    }

    public function webhookAction(Request $request, $id)
    {
        
        return new JsonResponse(['id' => $id] );
    }

   /**
     * Called after the entity has been persisted allowing for custom preperation of $entity prior to viewAction.
     *
     * @param      $entity
     * @param      $action
     * @param null $pass
     */
    protected function afterEntitySave($entity, Form $form, $action = 'new', $pass = null)
    {
        /*$model   = $this->getModel($this->getModelName());
        if (!$model instanceof FormModel) {
            throw new \Exception(get_class($model).' must extend '.FormModel::class);
        }

        $name = $entity->getName();

        if($action == 'new'){
            // Get Default basic Auth to authorize the mautic API
            $auth = $this->container->get('mautic.other.lasso.api_auth')->basicAuth();
            $api         = new MauticApi();
            $apiUrl      = 'http://'.$_SERVER['SERVER_NAME'].'/api';
            $campaignApi  = $api->newApi('campaigns', $auth, $apiUrl);
            $contactApi = $api->newApi('contacts', $auth, $apiUrl);

        
            $campaignRes = $campaignApi->create([
                'name' => $name,
                'description' => '',
                'isPublished' => 1,
                'createdBy' => $this->getUser()->getId(),
                'events' => [
                    'name' => $name,
                    'eventType' => 'action',
                ]
            ]);
            

            if(!$campaignRes['errors']){
            $campaignId = isset($campaign['id']) ? $campaign['id'] : null;
            $entity->setcampaignId($campaignId);
            }
            
            $model->saveEntity($entity);
        }*/
    }
    
}
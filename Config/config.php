<?php

return [
    'name'        => 'Lasso',
    'description' => 'Lasso Payload settings plugin, which will store information on Mautic from foreign app',
    'author'      => 'Abdullah Kiser',
    'version'     => '1.0.0',
    'routes' => [
        'main' => [
            'mautic_campaignlasso_index' => [
                'path'       => '/lasso-settings/{page}',
                'controller' => 'MauticLassoBundle:CampaignLasso:index',
            ],            
            'mautic_campaignlasso_action' => [
                'path'       => '/lasso-settings/{objectAction}/{objectId}',
                'controller' => 'MauticLassoBundle:CampaignLasso:execute',
            ],
            'mautic_lassopayloaddata_index' => [
                'path'       => '/lasso-payloads/{page}',
                'controller' => 'MauticLassoBundle:LassoPayloadData:index',
            ],
            'mautic_lassopayloaddata_action' => [
                'path'       => '/lasso-payloads/{objectAction}/{objectId}',
                'controller' => 'MauticLassoBundle:LassoPayloadData:execute',
            ],            
        ],
        'public' =>[
            'mautic_campaignlasso_webhook' => [
                'path'       => '/lasso/{id}',
                'controller' => 'MauticLassoBundle:CampaignLasso:webhook',
            ],
        ],
    ],
    'menu' => [
        'main' => [
            'mautic.lasso.main' => [
                'id'        => 'mautic_lasso_root',
                'iconClass' => 'fa-globe',
                'priority'  => 40,
            ],
            'mautic.lasso.menu' => [
                'route'    => 'mautic_campaignlasso_index',
                'priority' => 10,
                'parent' => 'mautic.lasso.main',
            ],
            'mautic.lassopayloaddata.menu' => [
                'route'    => 'mautic_lassopayloaddata_index',
                'priority' => 10,
                'parent' => 'mautic.lasso.main',
            ],
        ],
    ],  
    'services' => [
        'forms' => [
            'mautic.form.type.lasso' => [
                'class' => \MauticPlugin\MauticLassoBundle\Form\Type\LassoType::class,
            ],
            'mautic.form.type.campaignlasso' => [
                'class' => \MauticPlugin\MauticLassoBundle\Form\Type\CampaignLassoType::class,
            ],
        ],
        'models' => [
            'mautic.lasso.model.lasso' => [
                'class'     => \MauticPlugin\MauticLassoBundle\Model\LassoModel::class,
                'arguments' => [
                    'mautic.form.model.form',
                    'mautic.page.model.trackable',
                    'mautic.helper.templating',
                    'event_dispatcher',
                    'mautic.lead.model.field',
                    'mautic.tracker.contact',
                    'doctrine.orm.entity_manager',
                    
                ],
                //'public' => true,
                'alias' => 'model.lasso.lasso'
            ],

            'mautic.campaignlasso.model.campaignlasso' => [
                'class'     => \MauticPlugin\MauticLassoBundle\Model\CampaignlassoModel::class,
                //'public' => true,
                'arguments' => [
                    '@service_container'
                ],
                'alias' => 'model.lasso.campaignlasso'
            ],
            'mautic.lassopayloaddata.model.lassopayloaddata' => [
                'class'     => \MauticPlugin\MauticLassoBundle\Model\LassoPayloadDataModel::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                ],
                //'public' => true,
                'alias' => 'model.lasso.lassopayloaddata'
            ],
        ],
        'events' => [
            'mautic.lasso.subscriber.lasso' => [
                'class'     => \MauticPlugin\MauticLassoBundle\EventListener\LassoSubscriber::class,
                'arguments' => [
                    'router',
                    'mautic.helper.ip_lookup',
                    'mautic.core.model.auditlog',
                    'mautic.page.model.trackable',
                    'mautic.page.helper.token',
                    'mautic.asset.helper.token',
                    'mautic.lasso.model.lasso',
                    'request_stack',
                    '@service_container',
                ],
            ],
        ],
        'other' =>[
            'mautic.lasso.process_webhook_request' => [
                'class' => \MauticPlugin\MauticLassoBundle\Service\LassoService::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'mautic.lead.model.lead',
                    '@service_container',
                ],
            ],
            'mautic.other.lasso.api_auth' => [
                'class' => 'MauticPlugin\MauticLassoBundle\Helper\AuthHelper',
                'arguments' => [
                    'mautic.helper.core_parameters',
                ]
            ],
        ],
    ], 
    
];
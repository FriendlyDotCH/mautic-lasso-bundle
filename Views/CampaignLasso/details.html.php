<?php

    $lassos = $item->getLassos();

    $view->extend('MauticCoreBundle:Default:content.html.php');
    $view['slots']->set('mauticContent', 'lasso');
    $view['slots']->set('headerTitle', 'Lasso Details');

    $view['slots']->set(
        'actions',
        $view->render(
            'MauticCoreBundle:Helper:page_actions.html.php',
            [
                'item'            => $item,
                'templateButtons' => [
                    'edit' => $view['security']->hasEntityAccess(
                        $permissions['campaignlasso:items:editown'],
                        $permissions['campaignlasso:items:editother'],
                        $item->getCreatedBy()
                    ),
                    'clone'  => $permissions['campaignlasso:items:create'],
                    'delete' => $view['security']->hasEntityAccess(
                        $permissions['campaignlasso:items:deleteown'],
                        $permissions['campaignlasso:items:deleteother'],
                        $item->getCreatedBy()
                    ),
                    'close' => $view['security']->isGranted('campaignlasso:items:view'),
                ],
                'routeBase' => 'campaignlasso',
                'langVar'   => 'campaignlasso',
            ]
        )
    );

?>

<!-- start: box layout -->
<div class="box-layout">
    
    <?php foreach($lassos as $lasso) :?>
    <!-- container -->
    <div class="col-md-9 bg-auto height-auto bdr-r pa-md">
        <div class="row">
            <div class="col-md-2">
                <b><?php echo $view['translator']->trans('plugin.lasso.payload'); ?>:</b>
            </div>
            <div class="col-md-6">
                <?php echo $lasso->getPayload();?>
            </div>
        </div> 

        <div class="row">
            <div class="col-md-2">
                <b><?php echo $view['translator']->trans('plugin.lasso.switch'); ?>:</b>
            </div>
            <div class="col-md-6">
                <p><?php echo $lasso->getSwitch();?></p>
            </div>
        </div>   
        <div class="row">
            <div class="col-md-2">
                <b><?php echo $view['translator']->trans('plugin.lasso.coreFields'); ?>:</b>
            </div>
            <div class="col-md-6">
                <p><?php echo $lasso->getCoreFields();?></p>
            </div>
        </div>        
    </div>
    <?php endforeach; ?>    


</div>
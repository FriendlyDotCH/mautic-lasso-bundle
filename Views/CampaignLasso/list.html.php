<?php

/*
 * @copyright   2016 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if ('index' == $tmpl) {
    $view->extend('MauticLassoBundle:CampaignLasso:index.html.php');
}
?>

<?php if (count($items)): ?>
    <div class="table-responsive page-list">
        <table class="table table-hover table-striped table-bordered lasso-list" id="lassoTable">
            <thead>
            <tr>
                <?php
                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'checkall'        => 'true',
                        'target'          => '#lassoTable',
                        'routeBase'       => 'campaignlasso',
                        'templateButtons' => [
                            'delete' => $permissions['campaignlasso:items:delete'],
                        ],
                    ]
                );

                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'campaignlasso',
                        'orderBy'    => 'f.payload',
                        'text'       => 'mautic.lasso.title',
                        'class'      => 'col-lasso-name',
                        'default'    => true,
                    ]
                );
                ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <?php
                        echo $view->render(
                            'MauticCoreBundle:Helper:list_actions.html.php',
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
                                ],
                                'routeBase' => 'campaignlasso',
                            ]
                        );
                        ?>
                    </td>
                    <td>
                        <div>
                            <!-- <?php echo $view->render('MauticCoreBundle:Helper:publishstatus_icon.html.php', ['item' => $item, 'model' => 'lasso']); ?>-->
                            <?php //if($item->getCampaignId()) :?>
                            <a data-toggle="ajax" href="<?php echo $view['router']->path(
                                'mautic_campaignlasso_action',
                                ['objectId' => $item->getId(), 'objectAction' => 'view']
                            ); ?>">
                                <?php echo $item->getName(); ?>
                            </a>
                            <?php //endif; ?>
                        </div>
                        <?php if ($lassos = $item->getLassos()): ?>
                            <?php foreach($lassos as $lasso):?>
                            <div class="text-muted mt-4">
                                <small><?php echo $lasso->getSwitch(); ?></small>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="panel-footer">
        <?php echo $view->render(
            'MauticCoreBundle:Helper:pagination.html.php',
            [
                'totalItems' => count($items),
                'page'       => $page,
                'limit'      => $limit,
                'baseUrl'    => $view['router']->path('mautic_campaignlasso_index'),
                'sessionVar' => 'campaignlasso',
            ]
        ); ?>
    </div>
<?php else: ?>
    <?php echo $view->render('MauticCoreBundle:Helper:noresults.html.php', ['tip' => 'mautic.lasso.noresults.tip']); ?>
<?php endif; ?>

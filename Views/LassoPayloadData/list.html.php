<?php

/*
 * @copyright   2016 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        https://plugin.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if ('index' == $tmpl) {
    $view->extend('MauticLassoBundle:LassoPayloadData:index.html.php');
}
?>

<?php if (count($items)): ?>
    <div class="table-responsive page-list">
        <table class="table table-hover table-striped table-bordered lasso-payload-list" id="lassoPayloadTable">
            <thead>
            <tr>
                <?php

                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'lassopayloaddata',
                        'orderBy'    => 'f.dateLastPurchase',
                        'text'       => 'plugin.lasso.dateLastPurchase',
                        'class'      => 'col-lasso-date-last-purchase',
                        'default'    => true,
                    ]
                );

                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'lassopayloaddata',
                        'orderBy'    => 'f.name',
                        'text'       => 'plugin.lasso.campaign',
                        'class'      => 'col-lasso-name',
                        'default'    => true,
                    ]
                );

                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'lassopayloaddata',
                        'orderBy'    => 'f.email',
                        'text'       => 'plugin.lasso.email',
                        'class'      => 'col-lasso-email',
                        'default'    => true,
                    ]
                );

                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'lassopayloaddata',
                        'orderBy'    => 'f.tag',
                        'text'       => 'plugin.lasso.tag',
                        'class'      => 'col-lasso-tag',
                        'default'    => true,
                    ]
                );

                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'lassopayloaddata',
                        'orderBy'    => 'f.totalSpend',
                        'text'       => 'plugin.lasso.totalSpend',
                        'class'      => 'col-lasso-total-spend',
                        'default'    => true,
                    ]
                );

                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'lassopayloaddata',
                        'orderBy'    => 'f.staticData',
                        'text'       => 'plugin.lasso.staticData',
                        'class'      => 'col-lasso-static-data',
                        'default'    => true,
                    ]
                );

                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'lassopayloaddata',
                        'orderBy'    => 'f.staticDate',
                        'text'       => 'plugin.lasso.staticDate',
                        'class'      => 'col-lasso-static-date',
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
                            $lstPurchaseDate = $item->getDateLastPurchase() ? $item->getDateLastPurchase()->format('Y-m-d') : '';
                            echo $lstPurchaseDate;
                        ?>
                    </td>
                    <td>
                        <?php  
                        $name =  $item->getLead() ? $item->getLead()->getName() : '';
                        echo $name; 
                        
                        ?>
                    </td>
                    <td>
                        <div>                            
                            <?php echo $item->getEmail(); ?>                            
                        </div>
                    </td>
                    <td>
                        <?php
                            $tag = $item->getTag() ? $item->getTag() : '';
                            echo $tag;
                        ?>
                    </td>
                    <td>
                        <?php
                            $totalSpend = $item->getTotalSpend() ? $item->getTotalSpend() : '';
                            echo $totalSpend;
                        ?>
                    </td>
                    <td>
                        <?php
                            $staticDate = $item->getStaticData() ? $item->getStaticData() : '';
                            echo $staticDate;
                        ?>
                    </td>
                    <td>
                        <?php
                            $staticDate = $item->getStaticDate() ? $item->getStaticDate()->format('Y-m-d') : '';
                            echo $staticDate;
                        ?>
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
                'baseUrl'    => $view['router']->path('mautic_lassopayloaddata_index'),
                'sessionVar' => 'campaignlasso',
            ]
        ); ?>
    </div>
<?php else: ?>
    <?php echo $view->render('MauticCoreBundle:Helper:noresults.html.php', ['tip' => 'plugin.lasso.noresults.tip']); ?>
<?php endif; ?>

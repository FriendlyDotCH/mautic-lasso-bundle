<?php

$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('mauticContent', 'lasso');

$header = ($entity->getId())
    ?
    $view['translator']->trans(
        'mautic.lasso.edit',
        ['%title%' => $view['translator']->trans($entity->getName())]
    )
    :
    $view['translator']->trans('mautic.lasso.new');

$view['slots']->set('headerTitle', $header);
echo $view['assets']->includeScript('plugins/MauticLassoBundle/Assets/js/lasso.js');
echo $view['assets']->includeStylesheet('plugins/MauticLassoBundle/Assets/css/lasso.css');

$list = $form['lassos'];
$index = 0;

foreach($form['lassos']->vars['value'] as $key => $value){
    if($key > $index){
        $index = $key;
    }
}

$datePrototype = (isset($list->vars['prototype'])) ?
$view->escape('<div class="lasso-config-widget"><div class="col-md-10 bg-auto height-auto">'.$view['form']->widget($list->vars['prototype']).'</div></div>') : '';
    
?>

<!-- start: box layout -->
<div class="box-layout">
<?php echo $view['form']->start($form);  ?>
<!-- container -->
     <div class="col-md-9 bg-auto height-auto bdr-r pa-md">
            
            <div class="row">
               <?php 
               if(isset($form['campaignId'])){
                echo $view['form']->row($form['campaignId']);
               }
               if(isset($form['name'])){
                echo $view['form']->row($form['name']);
               }
               ?>
            </div>

            <div class="row">
            <a  onClick="addlassoItems();" data-prototype="<?php echo $datePrototype; ?>" data-index="<?php echo $index + 1; ?>"
                class="btn btn-warning btn-xs btn-add-lasso-item" href="#" id="<?php echo $form->vars['id']; ?>_additem">
                <?php echo $view['translator']->trans('Add Lasso config'); ?>
            </a>
               

            <div id="lasso-<?php echo $form->vars['id']; ?>" class="list-lasso-items" >
                   
                        <?php foreach ($list->children as $key => $item): ?>
                        <div class="lasso-config-widget">
                            <div class="col-md-10 bg-auto height-auto">                                
                            <?php //echo $view['form']->row($item); ?>  
                            <?php echo $view['form']->block($item, 'form_widget_compound'); ?>                              
                            </div>
                            <div class="col-md-2 bg-auto height-auto" style="margin-top: 25px;padding-left: 0px;">
                                <a hrf="#" class="remove-lasso-item input-group-addon preaddon" style="width: 20px;/*! padding-top: 30px !important; */">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>    
                        <?php endforeach; ?>
                    </div>
                    
            </div>    
     </div>  

     <div class="modal-form-buttons" style="margin-left: 15px;">
    
    </div>   
</div>
<?php echo $view['form']->end($form); ?>
<div class="row">
<div class="col-md-4">
    <?php echo $view['form']->row($form['payload']); ?>
</div>
<div class="col-md-4">
    <?php echo $view['form']->row($form['switch']); ?>
</div>
<div class="col-md-4">
    
    <?php if(!empty($entity) && ($entity->getSwitch() == 'static' || $entity->getSwitch() == 'verification')){ ?>
        <div class="core-field lasso-hide" >
            <?php echo $view['form']->row($form['coreFields']); ?>
        </div>
        <div class="static-field " >
            <?php echo $view['form']->row($form['staticField']); ?>
        </div>
    <?php }else{ ?>
        <div class="core-field " >
            <?php echo $view['form']->row($form['coreFields']); ?>
        </div>
        <div class="static-field lasso-hide" >
            <?php echo $view['form']->row($form['staticField']); ?>
        </div>    
    <?php } ?>    
    
</div>
</div> 
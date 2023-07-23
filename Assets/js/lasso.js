
function addlassoItems(){
    let $collectionHolder;
    // Get the ul that holds the collection of tags
    $collectionHolder = mQuery('div.list-lasso');
    console.log(mQuery('.btn-add-lasso-item').data('index'));
    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find('input').length);
    // add a new tag form (see next code block)
    addTagForm($collectionHolder);
    
    return false;
}

function addTagForm($collectionHolder) {
    // Get the data-prototype explained earlier
    var prototype = mQuery('.btn-add-lasso-item').data('prototype');

    // get the new index
    var index = mQuery('.btn-add-lasso-item').data('index');
    prototype = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    mQuery('.btn-add-lasso-item').data('index', index + 1);
    var $newForm = mQuery(prototype);
    
    
    $newForm = $newForm.append('<div class="col-md-2 bg-auto height-auto" style="margin-top: 25px;padding-left: 0px;"><a hrf="#" class="remove-lasso-item input-group-addon preaddon" style="width: 20px;/*! padding-top: 30px !important; */"><i class="fa fa-times"></i></a></div>');
    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = mQuery('.list-lasso-items').append($newForm);
    
    // handle the removal, just for this example
    mQuery('.remove-tag').on('click', function(e) {
        alert('exit;    ');
        e.preventDefault();
        debugger;
        mQuery(this).parents('div').eq(0).remove();
        
        return false;
    });

    mQuery('a.remove-lasso-item').on('click', function(e){
        let $var = mQuery(this).parents('div.lasso-config-widget').eq(0);
        console.log($var.attr('class'))
        mQuery(this).parents('div.lasso-config-widget').eq(0).remove();
        
        return false;
    }); 

    mQuery('.switch-payload-value').on('change', function () {
        
        var switchVal = mQuery(this).val();
        console.log(switchVal)
        if(switchVal == 'static' || switchVal =='verification'|| switchVal == 'static_date'){
            
           mQuery(this).parents('div.row').eq(0).siblings().find('.static-mautic-field').removeClass('lasso-hide');
           mQuery(this).parents('div.row').eq(0).siblings().find('.static-mautic-field').parents('.row:first').show();
           mQuery(this).parents('div.row').eq(0).siblings().find('.core-mautic-field').parents('.row:first').hide(); 
           
        }else{
           mQuery(this).parents('div.row').eq(0).siblings().find('.static-mautic-field').val('');  
           mQuery(this).parents('div.row').eq(0).siblings().find('.static-mautic-field').addClass('lasso-hide');
           mQuery(this).parents('div.row').eq(0).siblings().find('.static-mautic-field').parents('.row:first').hide();
           mQuery(this).parents('div.row').eq(0).siblings().find('.core-mautic-field').parents('.row:first').show(); 
    
        }
    });

    // Rearrange Lasso config setting form 
    rearrangeDynamicLassoForm();
    
}


mQuery('.switch-payload-value').on('change', function () {

    var switchVal = mQuery(this).val();
    if(switchVal == 'static' || switchVal =='verification' || switchVal == 'static_date'){  

       mQuery(this).parents('div.row').eq(0).siblings().find('.static-mautic-field').removeClass('lasso-hide');
       mQuery(this).parents('div.row').eq(0).siblings().find('.static-mautic-field').parents('.row:first').show();
       mQuery(this).parents('div.row').eq(0).siblings().find('.core-mautic-field').parents('.row:first').hide(); 
       
    }else{
        mQuery(this).parents('div.row').eq(0).siblings().find('.static-mautic-field').val('');  
       mQuery(this).parents('div.row').eq(0).siblings().find('.static-mautic-field').addClass('lasso-hide');
       mQuery(this).parents('div.row').eq(0).siblings().find('.static-mautic-field').parents('.row:first').hide();
       mQuery(this).parents('div.row').eq(0).siblings().find('.core-mautic-field').parents('.row:first').show(); 

    }
});

mQuery(document).ready(function(){
    mQuery('a.remove-lasso-item').on('click', function(e){
        let $var = mQuery(this).parents('div.lasso-config-widget').eq(0);
        //console.log($var.attr('class'))
        mQuery(this).parents('div.lasso-config-widget').eq(0).remove();
        //mQuery(this).parents('div').find('.lasso-config-widget').eq(0).remove();
        //mQuery(this).parent().remove();
        
        return false;
        });
        
        rearrangeDynamicLassoForm();     
    
});

function rearrangeDynamicLassoForm(){
    //Show all the static fields which have values and hide all core fields choice box on the same row.
    mQuery( ".static-mautic-field" ).each(function( index ) {
        if(mQuery(this).val()){
            mQuery(this).parents('div.row').eq(0).siblings().find('.core-mautic-field').parents('.row:first').hide(); 

        }

      });

    // Hide all the label of the static field  
    mQuery('.lasso-hide').each(function( index ) {
        mQuery(this).parents('div.row').eq(0).hide();
    });  
}
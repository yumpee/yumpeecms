<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
$this->title='Manage'.$classname;
$saveElement = \Yii::$app->getUrlManager()->createUrl('setup/save-element');
$saveAttribute = \Yii::$app->getUrlManager()->createUrl('setup/save-attribute');
$saveElementAttribute = \Yii::$app->getUrlManager()->createUrl('setup/save-element-attribute');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('setup/delete-element');
$deleteAttribURL = \Yii::$app->getUrlManager()->createUrl('setup/delete-attrib-element');
$class_id = \Yii::$app->request->get("class");
$image_home = Yii::getAlias('@image_dir/');
$unsetFeature = \Yii::$app->getUrlManager()->createUrl('articles/unset-feature');
$mediaURL =  \Yii::$app->getUrlManager()->createUrl('media/featured-media');

$this->registerJs( <<< EOT_JS
 tinymce.init({ selector:'textarea',
           theme: 'modern',
        branding:false,
    width: 1000,
    height: 300,
        file_picker_callback: function(callback, value, meta) {
            $('#myModal').modal('show');
        },
    file_picker_types: 'file image media',
    plugins: [
      'advlist autolink link lists charmap print preview hr anchor pagebreak spellchecker',
      'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
      'save table contextmenu directionality emoticons template paste textcolor yumpeemedia yumpeeslider'
    ],
    toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons yumpeemedia yumpeeslider' });
   
$(document).on('click', '#btnSubmitElement',
       function(ev) {  
        $("#description").val(tinymce.get('description').getContent());
        $.post(
            '{$saveElement}',$( "#frmElements" ).serialize(),
            function(data) {
                alert(data);
                location.href='?r=setup/details&class={$class_id}';
            }
        )
        ev.preventDefault();
}); 

$(document).on('click', '#btnSubmitProperties',
       function(ev) { 
        $("#el_description").val(tinymce.get('el_description').getContent());
        $.post(
            '{$saveAttribute}',$( "#frmProperties" ).serialize(),
            function(data) {
                alert(data);
                location.href='?r=setup/details&class={$class_id}&actions=edit_attrib';
            }
        )
        ev.preventDefault();
});

                $('.delete_event').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete - ' + event_name)){
                        $.get(  
                            '{$deleteURL}',{id:id},
                            function(data) {
                                alert(data);
                            }
                        )
                    }            
  });
 $('.delete_attrib_event').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete - ' + event_name)){
                        $.get(  
                            '{$deleteAttribURL}',{id:id},
                            function(data) {
                                alert(data);
                            }
                        )
                    }            
  });
                            
$(document).on('click', '#btnElemAttrib',
       function(ev) {        
        $.post(
            '{$saveElementAttribute}',$( "#frmElemAttrib" ).serialize(),
            function(data) {
                alert(data);
                location.href='?r=setup/details&class={$class_id}';
            }
        )
        ev.preventDefault();
});
                
$(document).on('click','#btnInsertMedia',
       function(ev){
       var myradio = $("input[name='my_images']:checked").val();   
       var my_radio_info = myradio.split("|");
       var img_src = my_radio_info[0];
       var my_id = my_radio_info[1];
       if(localStorage.image_caller=="set_feature"){
                $("#display_image_id").val(my_id);
                $("#my_display_image").attr("src","{$image_home}" + img_src);
                localStorage.removeItem("image_caller");
       } 
       $('#myModal').modal('toggle');     
   });
 $('.media').click(function (element) {  
      localStorage.setItem("image_caller",$(this).attr('id')); //store who is calling this dialog 
      $.get(
                '{$mediaURL}',{search:'featured',exempt_the_headers_in_yumpee:'true'},
                function(data) {                    
                    $('#yumpee_media_content').html(data);
                    $('#myModal').modal();
                }    
            )
     ev.preventDefault(); 
  });
                            
 $('#unset_feature').click(function (element) {                    
                    var id = $(this).attr('id');
                    
                    if(confirm('Are you sure you want to remove this feature image')){
                        $("#display_image_id").val("0");
                        $("#my_display_image").attr("src","0");
                    }            
  });
    
 if($("#current_tab_open").val()=="edit_attrib"){
    $('#tab_property').trigger('click')        
 }
                
  $("#datalisting").DataTable();
  $("#datalisting2").DataTable();
EOT_JS
);
                
$display_image_path="";
if(isset($rs->displayImage->path)):
    $display_image_path=$rs->displayImage->path;
endif;
?>
<style>
    .thumbnail:hover {
    position:relative;
    top:-25px;
    left:-35px;
    width:200px;
    height:auto;
    display:block;
    z-index:999;
}
.modal-dialog{
    position: relative;
    display: table; /* This is important */ 
    overflow-y: auto;    
    overflow-x: auto;
    width: auto;
    min-width: 300px;   
}
</style>
<div class="container-fluid">
    <p align="right"><a href='?r=setup/index'>Classes Setup</a>
  <h2><?=$classname?></h2>
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home"><?=$classname?> List</a></li>
    <li><a data-toggle="tab" href="#menu1" id="tab_property"><?=$classname?> Properties</a></li>
    <li><a data-toggle="tab" href="#menu2" id="tab_assign">Assign <?=$classname?> Properties</a></li>
    <li><a data-toggle="tab" href="#menu3">Permissions</a></li>
  </ul>

  <div class="tab-content">
    <div id="home" class="tab-pane fade in active">
      <h3><?=$classname?> List</h3>
      <p>Manage the options available for this class type</p>
      <div id="addCategories">
        <form action="index.php?r=events/index" method="post" id="frmElements">
        <table class="table">
            <tr><td><?=$classname?> Name<td><input name="alias" id="folder" value="<?=$rs['alias']?>" class="form-control" type="text" />
            <tr><td>Identifier Name<td><input name="name" id="name" value="<?=$rs['name']?>" class="form-control" type="text" />
            <tr><td>Parent <?=$classname?><td><?=\yii\helpers\Html::dropDownList("parent_id",$rs['parent_id'],$element_list,['class'=>'form-control','prompt'=>''])?>            
            <tr><td>Notes<td><textarea name="description" id="description"><?=$rs['description']?></textarea>
            <tr><td>Display Order<td><input name="display_order" id="display_order" value="<?=$rs['display_order']?>" class="form-control" type="text" />
            <tr><td>Feature Image<td><img id='my_display_image' src='<?=$image_home?>/<?=$display_image_path?>' height='100px' align='top' width='150px' style='border:1px solid #233388' HSPACE='20' VSPACE='20'/> <a href='#' class='media' id='set_feature'>Set Feature Image</a> | <a href='#' id='unset_feature'>Unset Feature Image</a> <input type="hidden" name="display_image_id" id="display_image_id" value="<?=$rs['display_image_id']?>"/>
            <tr><td colspan="2"><button type="submit" id="btnSubmitElement" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="id" value="<?=$id?>" /> 
                    <input type='hidden' name='class_id' value='<?=Yii::$app->request->get('class')?>'/>
                    <input type='hidden' name='classname' value='<?=$classname?>'/>
            
            </td>
            
        
        
        </table>
        </form>
    </div>
    <div class="box">
        <div class="box-body">
            <table id="datalisting" class="table table-bordered table-striped">
                <thead><tr><th><th><?=$classname?><th>Short Name<th>Inherit From<th>Display Order<th>Actions</thead>
                    <tbody>
                    <?php
                        foreach ($records_element as $user) :
                            $display_image_path="";
                            if(isset($user->displayImage->path)):
                                    $display_image_path=$user->displayImage->path;
                            endif;
                    ?>
                        <tr><td><img src='<?=$image_home?>/<?=$display_image_path?>' width='60px' class="thumbnail"></img><td><?=$user['alias']?></td><td> <?=$user['name']?><td><?=$user['parent']['alias']?><td><?=$user['display_order']?><td> <a href='?actions=edit&id=<?=$user['id']?>&r=setup/details&class=<?=$user['class_id']?>'><small><i class="glyphicon glyphicon-pencil"></i></small></a>  <a href='#' class='delete_event' id='<?=$user['id']?>' event_name='<?=$user['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
                    <?php
                        endforeach;
                    ?>
                    </tbody>
            </table>
        </div>
    </div>
    </div>
    <div id="menu1" class="tab-pane fade">
      <h3>Properties</h3>
      <p>Manage the properties of this class type</p>
      <div id="addProperties">
        <form action="index.php?r=events/index" method="post" id="frmProperties">
        <table class="table">
            <tr><td>Property Name<td><input name="alias" id="propAlias" value="<?=$prop_rs['alias']?>" class="form-control" type="text" />
            <tr><td>Identifier Name<td><input name="name" id="propName" value="<?=$prop_rs['name']?>" class="form-control" type="text" />
            <tr><td>Parent property<td><?=\yii\helpers\Html::dropDownList("parent_id",$prop_rs['parent_id'],$prop_list,['class'=>'form-control','prompt'=>''])?>            
            <tr><td>Notes<td><textarea name="description" id="el_description"><?=$prop_rs['description']?></textarea>       
            <tr><td>Display Order<td><input name="display_order" id="display_order" value="<?=$rs['display_order']?>" class="form-control" type="text" />
            <tr><td colspan="2"><button type="submit" id="btnSubmitProperties" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="id" value="<?=$prop_id?>" /><input type='hidden' name='class_id' value='<?=Yii::$app->request->get('class')?>'/>
                    <input type='hidden' name='classname' value='<?=$classname?>'/>
            
            </td>
        
        
        </table>
        </form>
    </div>
    <div class="box">
        <div class="box-body">
            <table id="datalisting2" class="table table-bordered table-striped">
                <thead><tr><th>Property<th>Short Name<th>Inherit From<th>Display Order<th>Actions</thead>
                    <tbody>
                    <?php
                        foreach ($records_attribute  as $user) :
                    ?>
                        <tr><td><?=$user['alias']?></td><td> <?=$user['name']?><td><?=$user['parent']['alias']?><td><?=$user['display_order']?><td> <a href='?actions=edit_attrib&prop_id=<?=$user['id']?>&r=setup/details&class=<?=$user['class_id']?>'><small><i class="glyphicon glyphicon-pencil"></i></small></a>  <a href='#' class='delete_attrib_event' id='<?=$user['id']?>' event_name='<?=$user['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
                    <?php
                        endforeach;
                    ?>
                    </tbody>
            </table>
        </div>
    </div>
     
    </div>
    <div id="menu2" class="tab-pane fade">
      <h3>Assign Properties</h3>
      <p>Assign applicable properties to options</p>
      <div id="addCategories">
        <form action="index.php?r=events/index" method="post" id="frmElemAttrib">
        <table class="table table-striped">
            <thead><tr><th><?=$classname?><th>Properties</thead>
            <?php
                        foreach ($records_element as $elements) :                            
                        
            ?>
            <tr><td><?=$elements['alias']?><td><table width='100%'>
            <?php
                           foreach ($records_attribute  as $property) :
                               $property_obj = $elements->getElementProperties($property->id);
                               if($property_obj!=null):
                                    echo "<tr><td><input name='chk_".$elements->id."_".$property->id."' type='checkbox' checked>".$property['alias']."<td><input name='val_".$elements->id."_".$property->id."' class='form-control' type=text value=\"".$property_obj->element_attribute_val."\">";
                               else:
                                   echo "<tr><td><input name='chk_".$elements->id."_".$property->id."' type='checkbox'>".$property['alias']."<td><input name='val_".$elements->id."_".$property->id."' class='form-control' type=text value=''>";
                               endif;
                           endforeach;
                           echo "</table>";
                endforeach;
            ?>
                        
            <tr><td colspan="2"><button type="submit" id="btnElemAttrib" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="id" value="<?=$id?>" /><input type='hidden' name='class_id' value='<?=Yii::$app->request->get('class')?>'/>
            
            </td>
        
        
        </table>
        </form>
    </div>
    </div>
    <div id="menu3" class="tab-pane fade">
      <h3>Permissions</h3>
      <p>Assign Permissions to users for properties and options.</p>
    </div>
  </div>
</div>
<input type="hidden" id="current_tab_open" value="<?=Yii::$app->request->get("actions")?>" />

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Insert Media</h4>
      </div>
      <div class="modal-body">
          <div id="yumpee_media_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="btnInsertMedia">Insert Media</button> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
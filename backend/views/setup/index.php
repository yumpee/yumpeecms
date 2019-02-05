<?php

/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Project Site : http://www.yumpeecms.com


 * YumpeeCMS is a Content Management and Application Development Framework.
 *  Copyright (C) 2018  Audmaster Technologies, Australia
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.

 */
$this->title='Themes';
$saveURL = \Yii::$app->getUrlManager()->createUrl('setup/save');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('setup/delete');
$image_home = Yii::getAlias('@image_dir/');
$unsetFeature = \Yii::$app->getUrlManager()->createUrl('articles/unset-feature');
$mediaURL =  \Yii::$app->getUrlManager()->createUrl('media/featured-media');

$this->registerJs( <<< EOT_JS
       $(document).on('click', '#btnSubmit',
       function(ev) {   
        
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
            }
        )
        ev.preventDefault();
  }); 
       $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=setup/index';
        
        
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
      
$("#datalisting").DataTable(); 
                            
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
<button class="btn btn-info" data-toggle="collapse" data-target="#addCategories">Toggle View</button>
<div id="addCategories">
     <form action="index.php?r=events/index" method="post" id="frm1">
    <table class="table">
        <tr><td>Alias name<td><input name="alias" id="folder" value="<?=$rs['alias']?>" class="form-control" type="text" />
        <tr><td>Identifier Name<td><input name="name" id="name" value="<?=$rs['name']?>" class="form-control" type="text" />
        <tr><td>Parent Class<td><?=\yii\helpers\Html::dropDownList("parent_id",$rs['parent_id'],$setup_list,['class'=>'form-control','prompt'=>''])?>
        <tr><td>Display On Backend Menu<td><?=\yii\helpers\Html::dropDownList("show_in_menu",$rs['show_in_menu'],['N'=>'No','Y'=>'Yes'],['class'=>'form-control'])?>
        <tr><td>Display Order<td><input name="display_order" id="display_order" value="<?=$rs['display_order']?>" class="form-control" type="text" />
        <tr><td>Feature Image<td><img id='my_display_image' src='<?=$image_home?>/<?=$display_image_path?>' height='100px' align='top' width='150px' style='border:1px solid #233388' HSPACE='20' VSPACE='20'/> <a href='#' class='media' id='set_feature'>Set Feature Image</a> | <a href='#' id='unset_feature'>Unset Feature Image</a> <input type="hidden" name="display_image_id" id="display_image_id" value="<?=$rs['display_image_id']?>"/>
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="id" value="<?=$id?>" />
            
            </td>
        
        
    </table>
    </form>
</div>

<div class="box">
<div class="box-body">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th><th>Class<th>Short Name<th>Inherit From<th>Display Order<th>Actions</thead>
    <tbody>
      <?php
      foreach ($records as $user) :
                            $display_image_path="";
                            if(isset($user->displayImage->path)):
                                    $display_image_path=$user->displayImage->path;
                            endif;
      ?>
        <tr><td><img src='<?=$image_home?>/<?=$display_image_path?>' width='60px' class="thumbnail"></img><td><?=$user['alias']?></td><td> <?=$user['name']?><td><?=$user['parent']['alias']?><td><?=$user['display_order']?><td> <a href='?actions=edit&id=<?=$user['id']?>&r=setup/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='?actions=edit&class=<?=$user['id']?>&r=setup/details' title="Details"><small><i class="fa fa-list"></i></small></a> <a href='#' class='delete_event' id='<?=$user['id']?>' event_name='<?=$user['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
     <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>
</div>


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
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

$this->title='Sliders';
$saveURL = \Yii::$app->getUrlManager()->createUrl('slider/save');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('slider/delete');
$deleteSlideImageURL = \Yii::$app->getUrlManager()->createUrl('slider/delete-slide-image');
$mediaURL =  \Yii::$app->getUrlManager()->createUrl('media/insert-media');
$image_home = Yii::getAlias('@image_dir/');


$this->registerJs( <<< EOT_JS
        
       $(document).on('click', '#btnSubmit',
       function(ev) {   
        
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
                location.href='?r=slider/index';
            }
        )
        ev.preventDefault();
  }); 
       $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=slider/index';
        
        
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
$('input[class=media]:checked').each(function(index){
  $("#image_listing").val($("#image_listing").val() + " " + $(this).val());
  var rnd = Math.floor((Math.random() * 1000) + 100);
  
  $("#image_slides_div").html($("#image_slides_div").html() + "<div class='col-md-3' id='md" + rnd + "'><img width='200px' height='200px' src='{$image_home}/" + $(this).val() + "'></img> <br><a href='#' id='" + $(this).val() + "' class='delete_slide_image' dvid='" + rnd + "'>Delete</a></div>");
}); 
 $('#myModal').modal('toggle');     
   });
                
 $('.media').click(function (element) {  
      localStorage.setItem("image_caller",$(this).attr('id')); //store who is calling this dialog 
      $.get(
                '{$mediaURL}',{search:'featured',exempt_the_headers_in_yumpee:'true'},
                function(data) {  
                    //alert(data);
                    $('#yumpee_media_content').html(data);
                    $('#myModal').modal();
                }    
            )
     ev.preventDefault(); 
  });
                

                
                
                
                
var lists = $("#image_listing").val().split(" ");
                
for(i=0;i < lists.length;i++){
          
var image_val = lists[i];
 if(image_val.trim()!=""){              
    $("#image_slides_div").html($("#image_slides_div").html() + "<div class='col-md-3' id='md" + i + "'><img width='200px' height='200px' src='{$image_home}/" + image_val + "'></img> <br><a href='#' class='delete_slide_image' id='" + image_val + "' dvid='" + i + "'>Delete</a></div>");              
 }   
}                
         
$('.delete_slide_image').click(function(element){    
        
        var id = $(this).attr('id');
        var dvid = $(this).attr('dvid');
        if(confirm('Are you sure you want to delete the image from this slide')){
                        $.get(  
                            '{$deleteSlideImageURL}',{id:id,slide:'{$id}'},
                            function(data) {
                                alert(data);
                                var c = "md" + dvid;
                                $("#" + c).remove();
                            }
                        )
                    } 
        
   ev.preventDefault();                         
});  

                            
$("#datalisting").DataTable(); 
EOT_JS
); 
                            
    
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
     <form action="#" method="post" id="frm1">
    <table class="table">
        <tr><td width="30%">Slider Name<td><input name="name" id="name" value="<?=$rs['name']?>" class="form-control" type="text" />
        <tr><td width="30%">Title<td><input name="title" id="title" value="<?=$rs['title']?>" class="form-control" type="text" />
        <tr><td>Transition Type<td><?= \yii\helpers\Html::dropDownList("transition_type",$rs['transition_type'],['M'=>'Manual','A'=>'Automatic'])?>
        <tr><td>Transition Duration (in seconds)<td><input name="duration" id="name" value="<?=$rs['duration']?>" class="form-control" type="text" /></td>
        <tr><td>Default Height<td><input name="default_height" id="name" value="<?=$rs['default_height']?>" class="form-control" type="text" /></td>
        <tr><td>Default Width<td><input name="default_width" id="name" value="<?=$rs['default_width']?>" class="form-control" type="text" /></td>
            
        <?php
        if($id<>""):
        ?>
        <tr><td>Add Images<td><a href='#' class='media btn btn-info' id='set_slides' >Add Image to slide...</a><br><br><div class="row"><div id='image_slides_div'></div></div></td>
        <?php
        endif;
        ?>
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="id" value="<?=$id?>" />
                
                <input type='hidden' name='image_listing' id='image_listing' value='<?=$rs['images']?>'/>
        </td>
    </table>
    </form>
</div>

<div class="box">
<div class="box-body">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Slider<th>Type<th>Actions</thead>
    <tbody>
      <?php
      foreach ($records as $user) :
          if($user['transition_type']=='M'):
              $trans_type="Manual";
          else:
              $trans_type="Automatic";
          endif;
      ?>
        <tr><td><?=$user['name']?></td><td><?=$trans_type?></td><td><a href='?actions=edit&id=<?=$user['id']?>&r=slider/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$user['id']?>' event_name='<?=$user['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
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
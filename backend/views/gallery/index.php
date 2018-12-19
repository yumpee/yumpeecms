<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$this->title="Gallery";

$saveURL = \Yii::$app->getUrlManager()->createUrl('gallery/save');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('gallery/delete');
$deleteGalleryImageURL = \Yii::$app->getUrlManager()->createUrl('gallery/delete-gallery-image');
$mediaURL =  \Yii::$app->getUrlManager()->createUrl('media/insert-media');
$image_home = Yii::getAlias('@image_dir/');
$this->registerJs( <<< EOT_JS
       $(document).on('click', '#btnSubmit',
       function(ev) {   
        
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
                location.href='?r=gallery/index';
            }
        )
        ev.preventDefault();
  }); 
       $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=gallery/index';
        
        
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

$(document).on('click','#btnInsertMedia',
       function(ev){
                var image_sel="";
$('input[class=media]:checked').each(function(index){
  $("#image_listing").val($("#image_listing").val() + " " + $(this).val());
  image_sel = image_sel + "<div class='col-md-2'><img width='200px' height='200px' src='{$image_home}/" + $(this).val() + "'></img> <br><a href='#' id='" + $(this).val() + "' class='delete_slide_image'>Delete</a></div>";
}); 
if(image_sel!=""){
  $("#image_slides_div").html($("#image_slides_div").html() + image_sel);
}
 $('#myModal').modal('toggle');     
   
});
$("#image_slides_div").html("...");
var lists = $("#image_listing").val().split(" ");
                
for(i=0;i < lists.length;i++){
          
var image_val = lists[i];
 if(image_val.trim()!=""){              
    $("#image_slides_div").html($("#image_slides_div").html() + "<div class='col-md-2'><img width='200px' height='200px' src='{$image_home}/" + image_val + "'></img> <a href='#' class='delete_slide_image' id='" + image_val + "'>Delete</a></div>");              
 }   
}
            
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
  $('.delete_slide_image').click(function(element){        
        var id = $(this).attr('id');
        if(confirm('Are you sure you want to delete the image from this slide')){
                        $.get(  
                            '{$deleteGalleryImageURL}',{id:id,gallery:'{$id}'},
                            function(data) {
                                alert(data);
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
     <form action="index.php?r=gallery/index" method="post" id="frm1">
    <table class="table">
        <tr><td>Gallery Name (No spaces)<td><input name="name" id="name" value="<?=$rs['name']?>" class="form-control" type="text" />
        <tr><td>Title<td><input name="title" id="title" value="<?=$rs['title']?>" class="form-control" type="text" />
        <tr><td>Description<td><textarea name="description" class="form-control"><?=$rs['description']?></textarea>       
        <?php
        if($id<>""):
        ?>
        <tr><td>Add Media<td><a href='#' class='media' id='set_slides'>Click to Add New Media...</a><div id='image_slides_div'></div></td>
        <?php
        endif;
        ?>        
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="id" value="<?=$rs['id']?>" />
                <br><input type='hidden' name='image_listing' id='image_listing' value='<?=$rs['images']?>'/> 
            </td>
        
        
    </table>
    </form>
</div>


<div class="box">
<div class="box-body">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Title<th>Name<th>Actions</thead>
    <tbody>
      <?php
      foreach ($records as $user) :
      ?>
        <tr><td><?=$user['title']?><td><?=$user['name']?></td><td><a href='?actions=edit&id=<?=$user['id']?>&r=gallery/index' title="View Gallery"><small><i class="glyphicon glyphicon-eye-open"></i></small></a>  <a href='?actions=edit&id=<?=$user['id']?>&r=gallery/index' title="Edit"><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$user['id']?>' event_name='<?=$user['name']?>' title="Delete"><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
     <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>
</div>

<div class="row"><div id='image_slides_div'></div></div>

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Media for Gallery</h4>
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
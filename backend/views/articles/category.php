<?php
$this->title = 'Articles Categories';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$saveURL = \Yii::$app->getUrlManager()->createUrl('articles/save-category');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('articles/category-delete');
$mediaURL =  \Yii::$app->getUrlManager()->createUrl('media/featured-media');
$image_home = Yii::getAlias('@image_dir/');
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
        location.href='?r=articles/category';
        
        
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
             
   $(document).on('click','#btnInsertMedia',
       function(ev){
       var myradio = $("input[name='my_images']:checked").val();   
       var my_radio_info = myradio.split("|");
       var img_src = my_radio_info[0];
       var my_id = my_radio_info[1];
       if(localStorage.image_caller=="set_feature"){
                $("#display_image_id").val(my_id);
                $("#my_display_image").attr("src","{$image_home}/" + img_src);
                localStorage.removeItem("image_caller");
       }
       
                
       $('#myModal').modal('toggle');     
   });
            
  $('.delete_event').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete category - ' + event_name)){
                        $.get(  
                            '{$deleteURL}',{id:id},
                            function(data) {
                                alert(data);
                            }
                        )
                    }            
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
?>
<style>
    .modal-dialog{
    position: relative;
    display: table; /* This is important */ 
    overflow-y: auto;    
    overflow-x: auto;
    width: auto;
    min-width: 300px;   
}
    
</style>

<?php
$display_image_path="";
if(isset($rs->displayImage->path)):
    $display_image_path=$rs->displayImage->path;
endif;


?>

      
<div class="container-fluid">
<button class="btn btn-info" data-toggle="collapse" data-target="#addCategories">Add Category</button>
<div id="addCategories">
     <form action="index.php?r=events/index" method="post" id="frm1">
    <table class="table">
        <tr><td>Name<td><input name="name" id="name" value="<?=$rs['name']?>" class="form-control" type="text" />
        <tr><td>Feature Image<td><img id='my_display_image' src='<?=$image_home?>/<?=$display_image_path?>' height='100px' align='top' width='200px' style='border:1px solid #233388' HSPACE='20' VSPACE='20'/> <a href='#' class='media' id='set_feature'>Set Feature Image</a> | <a href='#' id='unset_feature'>Unset Feature Image</a><input type="hidden" name="display_image_id" id="display_image_id" value="<?=$rs['display_image_id']?>"/>
        <tr><td>URL<td><input name="url" value="<?=$rs['url']?>" id="url" class="form-control"type="text" />
        <tr><td>Description<td><textarea name="description" id="description" class="form-control"rows="7" cols="40"><?=$rs['description']?></textarea>
        <tr><td>Category Index<td><?=$category?>
        <tr><td>Display Order<td><input name="display_order" value="<?=$rs['display_order']?>" id="display_order"  class="form-control"type="text" />
        <tr><td>Published<td><?=$published?>
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button><input type="hidden" name="processor" value="true" /><input type="hidden" name="id" value="<?=$id?>" />
            
            </td>
        
        
    </table>
    </form>
</div>


<div class="box">
<div class="box-body">
    <table id="datalisting" class="table table-bordered table-striped"><thead><tr><th>Name</td><th>URL<th>Description<th>Display Order<th>Published<th></thead>
        <tbody>
<?php
    foreach($records as $rec):
        if($rec['published']):
                    $published="Yes";
                else:
                    $published="No";
                endif;
?>
    <tr><td><?=$rec['name']?></td><td><?=$rec['url']?></td><td><?=$rec['description']?></td><td><?=$rec['display_order']?></td><td><?=$published?></td><td><a href='?actions=edit&id=<?=$rec['id']?>&r=articles/category'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$rec['id']?>' event_name='<?=$rec['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a></td>
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
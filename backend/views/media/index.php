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
use dosamigos\fileupload\FileUploadUI;
$this->title='Media';
$saveURL = \Yii::$app->getUrlManager()->createUrl('media/save');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('media/delete');
$editURL = \Yii::$app->getUrlManager()->createUrl('media/edit');
$home_image_url= \frontend\components\ContentBuilder::getSetting("website_image_url");
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
        location.href='?r=media/index';
        
        
  }); 
            
  $('.delete_event').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete - ' + event_name)){
                        $.get(  
                            '{$deleteURL}',{id:id},
                            function(data) {
                                alert(data);
                                var ob = "im" + id;
                                $("#" + ob).remove();
                            }
                        )
                    }            
  });
  $('.editImage').click(function (element) {                    
                    var id = $(this).attr('linkid');
                    $.get(  
                            '{$editURL}',{id:id},
                            function(data) {
                            record = JSON.parse(data)
                                $("#name").val(record['name']);
                                $("#alt_tag").val(record['alt_tag']);
                                $("#caption").val(record['caption']);
                                $("#description").val(record['description']);
                                $("#id").val(record['id']);
                                $("#size").val(record['size']);
                                $("#date").val(record['size']);
                                $("#url").val('{$home_image_url}/' + record['path']);
                                
                            }
                    )
                    
  });
  $('.detailsImage').click(function (element) {                    
                    var id = $(this).attr('linkid');
                    $.get(  
                            '{$editURL}',{id:id},
                            function(data) {
                            record = JSON.parse(data)
                                $("#details_name").val(record['name']);
                                $("#details_alt_tag").val(record['alt_tag']);
                                $("#details_caption").val(record['caption']);
                                $("#details_description").val(record['description']);
                                $("#details_size").val(record['size']);
                                $("#details_uploaded").val(record['upload_date']);
                                $("#details_by").val(record['publisher']['first_name'] + " " + record['publisher']['last_name']);
                                $("#details_url").val('{$home_image_url}/' + record['path']);
                                
                            }
                    )
                    
  });
                            
 if($("#name").val()!=""){
  $('#vlibrary').trigger('click')        
 }
           

$("#datalisting").DataTable();                            
EOT_JS
);  
                            

?>

<style>
    .images {
    border: double;
}
.spacer { margin:0; padding:0; height:50px; }
    </style>

<div class="container-fluid">
<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#media">Add Media</a></li>
  <li><a data-toggle="tab" href="#library" id="vlibrary">View Library</a></li>
  
</ul>
  <div class="tab-content">
    <div id="media" class="tab-pane fade in active">
        <div><p>Click on the files you wish to upload to your library.</div>
        <p>
        <?= FileUploadUI::widget([
    'model' => $model,
    'attribute' => 'id',
    'url' => ['media/image-upload', 'id' => $id],
    'gallery' => true,
    'fieldOptions' => [
        'accept' => 'image/*'
    ],
    'clientOptions' => [
        'maxFileSize' => 80000000
    ],
    // ...
    'clientEvents' => [
        'fileuploaddone' => 'function(e, data) {
                                
                            }',
        'fileuploadfail' => 'function(e, data) {
                                
                            }',
        'fileuploadstop' => 'function(e){
            alert("File upload completed");
            window.location.reload(false); 
        }',
        'fileuploadsubmit'=> 'function(e, data) {
                                 
                                var empty_flds = 0;
                                $(".required").each(function() {
                                if(!$.trim($(this).val())) {
                                    empty_flds++;
                                    
                                }    
                                });
                                if(empty_flds > 0){
                                    alert("All alt tags must be filled");
                                    return false;
                                }
                                var input = $("#imagename");
                                var alttag=$("#alttag");
                                data.formData = {imagename:input.val(),alttag:alttag.val()};
                            }',
        'fileupload'=> 'function(e, data) {
                                
                            }'
    ],
]); ?>
        
        
</div>

<div id="library" class="tab-pane fade">
    <p>
    <div class="row col-md-12">
        <?php
      $row_count=0;
      foreach ($records as $user) :
          
          $file_type="";
          if($user['media_type']=='1'):
              $file_type="Images";
          endif;
          if($user['media_type']=='2'):
              $file_type="Video";
          endif;
          if($user['media_type']=='3'):
              $file_type="Audio";
          endif;
          
      ?>
        <div class="col-md-3 col-xs-3 images" id="im<?=$user['id']?>"><span class="border border-primary"><img src="<?=$home_image_url?>/<?=$user['path']?>" height="200px" width="100%" class="rounded"></img><br>Name :<?=$user['name']?><br>Tag:<?=$user['alt_tag']?>
                <br><br><a href='#' data-toggle="modal" data-target="#detailsModal" class="detailsImage" linkid="<?=$user['id']?>">Details </a> |<a href='#' data-toggle="modal" data-target="#myModal" class="editImage" linkid="<?=$user['id']?>">Edit </a> | <a href='#' class='delete_event' id='<?=$user['id']?>' event_name='<?=$user['name']?>'>Delete</a></span></div>
        <?php
        $row_count++;
        if($row_count >3):
            $row_count=0;
            //echo "<div class='col-xs-12' style='height:50px;'></div>";
        echo "<div class='col-md-12'>&nbsp;</div>";
        endif;
        
        endforeach;
        ?>
        
    </div>
</div>
</div>
</div>

    
    
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Update Image Info</h4>
      </div>
      <div class="modal-body">
        <form action="index.php?r=testimonials/index" method="post" id="frm1">
        <table class="table">
        <tr><td>Name<td><input required name="name" id="name" value="<?=$rs['name']?>" class="form-control" type="text"/>
        <tr><td>Alt Tag<td><input name="alt_tag" id="alt_tag" value="<?=$rs['alt_tag']?>" class="form-control" type="text" required />
        <tr><td>Caption<td><input name="caption" id="caption" value="<?=$rs['caption']?>" class="form-control" type="text" required />
        <tr><td>Description<td><textarea name="description" id="description" class="form-control"><?=$rs['description']?></textarea>
        <tr><td>URL<td><input name="url" id="url" value="<?=$home_image_url?>/<?=$rs['path']?>" class="form-control" type="text" readonly />
        <tr><td>Size<td><input name="size" id="size" value="" class="form-control" type="text" readonly />
            
            </td>
                <tr><td colspan="2">
            
                        
            </td>
        <tr><td colspan="2">
            <button type="submit" id="btnSubmit" class="btn btn-success">Save</button>        
            <input type="hidden" name="id" id="id" value="<?=$id?>" />
        </table>
    </form> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
    
<div id="detailsModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Details Image Info</h4>
      </div>
      <div class="modal-body">
        <form>
        <table class="table">
        <tr><td>Name<td><input required name="details_name" id="details_name" value="" class="form-control" type="text" readonly/>
        <tr><td>Alt Tag<td><input name="details_alt_tag" id="details_alt_tag" value="" class="form-control" type="text" readonly />
        <tr><td>Caption<td><input name="details_caption" id="details_caption" value="" class="form-control" type="text" readonly />
        <tr><td>Description<td><textarea name="details_description" id="details_description" class="form-control" readonly></textarea>
        <tr><td>URL<td><input name="details_url" id="details_url" value="" class="form-control" type="text" readonly />
        <tr><td>Size<td><input name="details_size" id="details_size" value="" class="form-control" type="text" readonly />
        <tr><td>Uploaded On<td><input name="details_uploaded" id="details_uploaded" value="" class="form-control" type="text" readonly />
        <tr><td>Uploaded By<td><input name="details_by" id="details_by" value="" class="form-control" type="text" readonly />
            </td>
                <tr><td colspan="2">
            
                        
            </td>
        <tr><td colspan="2">
            
        </table>
    </form> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>




    


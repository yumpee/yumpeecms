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
use backend\components\DBComponent;
$updateFormSubmitURL = \Yii::$app->getUrlManager()->createUrl('forms/update-form-submit');
$updateFormDataURL = \Yii::$app->getUrlManager()->createUrl('forms/update-form-data');
$deleteAttachmentURL = \Yii::$app->getUrlManager()->createUrl('forms/delete-attachment');
$documentURL =  \Yii::$app->getUrlManager()->createUrl('media/insert-media');
$image_home = Yii::getAlias('@image_dir/');

$this->registerJs( <<< EOT_JS
$(document).on('click', '#btnUpdate',
       function(ev) {   
        
        $.post(
            '{$updateFormSubmitURL}',$( "#frmSubmit" ).serialize(),
            function(data) {
                alert(data);
                
            }
        )
            
        $.post(
            '{$updateFormDataURL}',$( "#frmData" ).serialize(),
            function(data) {
                alert(data);
                
            }
        )
        ev.preventDefault();
        
  });

$('.delete_attachment').click(function(element){        
        var id = $(this).attr('id');
        if(confirm('Are you sure you want to delete the document from this article')){
                        $.get(  
                            '{$deleteAttachmentURL}',{id:id,article_id:'{$info["id"]}'},
                            function(data) {
                                alert(data);
                                var ob = "im" + id;
                                $("#" + ob).remove();
                            }
                        )
                    } 
        
   ev.preventDefault();                         
});
                            
$(document).on('click','#btnInsertDocument',
       function(ev){
                var image_sel="";
$('input[class=media]:checked').each(function(index){
  var document_name = $(this).attr('document_name');
  $("#document_listing").val($("#document_listing").val() + " " + $(this).val());
                            
        if($(this).attr('imtype')=="image"){
            image_sel = image_sel + "<div class='col-md-2' id='im" + $(this).val() + "'><a href='{$image_home}/" + $(this).val() + "'><img width='100px' height='100px' src='{$image_home}/" + $(this).val() + "'></img></a> <br>" + document_name + "<br><a href='#' id='" + $(this).val() + "' class='delete_slide_image'>Delete</a></div>";
        }else{
            image_sel = image_sel + "<div class='col-md-2' id='im" + $(this).val() + "'><a href='{$image_home}/" + $(this).val() + "'><i class='fa fa-file fa-document' aria-hidden='true'></i></a> <br>" + document_name + "<br><a href='#' id='" + $(this).val() + "' class='delete_slide_image'>Delete</a></div>";
        }
}); 
if(image_sel!=""){
  $("#documents_div").html($("#documents_div").html() + image_sel);
}
 $('#documentModal').modal('toggle');     
   
});
      
$("#btnCustomAdd").click(function(){
        $("#custom_form_header").html($("#custom_form_header").html() + "<tr><td>" + $("#yumpee_custom_field").val() + "<td><input class='form-control' type='text' name='"  + $("#yumpee_custom_field").val() + "'>")  ;    
});
            
$('.documents').click(function (element) {  
      localStorage.setItem("image_caller",$(this).attr('id')); //store who is calling this dialog 
      
      $.get(
                '{$documentURL}',{search:'featured',exempt_the_headers_in_yumpee:'true'},
                function(data) {  
                    //alert(data);
                    $('#yumpee_document_content').html(data);
                    $('#documentModal').modal();
                }    
            )
     ev.preventDefault(); 
  });
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

.fa-document{
    font-size:100px;
}
</style>
<div id="addfield" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add field to form</h4>
      </div>
      <div class="modal-body">
        <p>Enter field name</p>
        <input type="text" class="form-control" id="yumpee_custom_field" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" id="btnCustomAdd">Add</button> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<form id="frmSubmit">
    <div class="pull-right"><button data-toggle="modal" data-target="#addfield" type="button">+ Add Field</button></div>
<table class="table table-bordered table-striped">
    <thead>
    <tr><td width="30%">Published<td><?=\yii\helpers\Html::dropDownList("published",$info['published'],['0'=>'No','1'=>'Yes'],['class'=>'form-control'])?></td>
    <tr><td>Rating<td><input type="text" name="rating" value="<?=$info['rating']?>" class="form-control" />
    <tr><td>No of views<td><?=$info['no_of_views']?><input type="hidden" name="id" value="<?=Yii::$app->request->get('id')?>">
    </thead>
</table>
</form>
<form id="frmData">
<table  class="table table-bordered table-striped">
    <thead  id="custom_form_header">
    
                
<?php
foreach($records as $rec):
    if(strlen($rec['param_val']) > 100):
            echo "<tr><td width='30%'>".DBComponent::parseField($rec,$info['form_id'])."<td><textarea class='form-control' type=text name='".$rec['param']."'>".$rec['param_val']."</textarea>";
        else:
            echo "<tr><td width='30%'>".DBComponent::parseField($rec,$info['form_id'])."<td><input class='form-control' type=text name='".$rec['param']."' value=\"".$rec['param_val']."\">";
    endif;
    
endforeach;

?>
 <?=$backend_data?>  
 <input type="hidden" name="id" value="<?=Yii::$app->request->get('id')?>">   
 
 <?php

$document_div="";
$document_listing="";
foreach($files as $document):
   if(file_exists(Yii::getAlias('@uploads/uploads/')."/".$document['file_path'])):
   $mime_type = mime_content_type(Yii::getAlias('@uploads/uploads/')."/".$document['file_path']);
    list($type_file,$extension) = explode("/",$mime_type);
    if($type_file=="image"):
        $document_div.="<div class='col-md-2' id='im".$document['id']."'><a href='".$image_home."/".$document['file_path']."'><img width='100px' height='100px' src='".$image_home."/".$document['file_path']."'></img></a><br>".$document['file_name']."<br> <a href='#' class='delete_attachment' id='".$document['id']."'>Delete</a></div>";
    else:
        $document_div.="<div class='col-md-2' id='im".$document['id']."'><p><a href='".$image_home."/".$document['file_path']."'><i class='fa fa-file fa-document' aria-hidden='true'></i></a><br>".$document['file_name']."<br> <a href='#' class='delete_attahement' id='".$document['id']."'>Delete</a></div>";
    endif;
    endif;
    
endforeach;
?>
 
 
 <tr><td>Attachments<td>
                 <div class="panel-group">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">
                                <a data-toggle="collapse" href="#collapse1">Attached Documents (<?=count($files)?>)</a>
                        </h5>
                        </div>
                        <div id="collapse1" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p align="right"><a href='#' class='documents' id='set_slides'>Click to Add Documents...</a>
                                <div id='documents_div'><?=$document_div?></div>
                            </div>
                            <div class="panel-footer"></div>
                        </div>
                    </div>
                </div> </td>
        
</table>
<input type='hidden' name="document_listing" id="document_listing" value="<?=$document_listing?>">    
</form>
<input type="button" class="btn btn-primary" value="Update" id="btnUpdate" />


<div id="documentModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Insert Documents</h4>
      </div>
      <div class="modal-body">
          <div id="yumpee_document_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="btnInsertDocument">Insert Documents</button> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
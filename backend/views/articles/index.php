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

use dosamigos\datepicker\DatePicker;

$this->title = 'Articles';

$saveURL = \Yii::$app->getUrlManager()->createUrl('articles/save');
$duplicateURL = \Yii::$app->getUrlManager()->createUrl('articles/duplicate');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('articles/delete');
$eventURL =  \Yii::$app->getUrlManager()->createUrl('articles/search-event');
$unsetFeature = \Yii::$app->getUrlManager()->createUrl('articles/unset-feature');
$tagURL =  \Yii::$app->getUrlManager()->createUrl('tags/search-tags');
$addTagURL =  \Yii::$app->getUrlManager()->createUrl('articles/add-tag');
$mediaURL =  \Yii::$app->getUrlManager()->createUrl('media/featured-media');
$documentURL =  \Yii::$app->getUrlManager()->createUrl('media/insert-media');
$deleteAttachmentURL = \Yii::$app->getUrlManager()->createUrl('articles/delete-attachment');
$detailsURL = \Yii::$app->getUrlManager()->createUrl('articles/details');
$image_home = Yii::getAlias('@image_dir/');
$document_div="";
$document_listing="";
if($rs['documents']!=null):
foreach($rs['documents'] as $document):
    $mime_type = mime_content_type(Yii::getAlias('@uploads/uploads/')."/".$document['media_id']);
    list($type_file,$extension) = explode("/",$mime_type);
    if($type_file=="image"):
        $document_div.="<div class='col-md-2' id='im".$document['media_id']."'><a href='".$image_home."/".$document['media_id']."'><img width='100px' height='100px' src='".$image_home."/".$document['media_id']."'></img></a><br>".$document['details']['name']."<br> <a href='#' class='delete_attachment' id='".$document['media_id']."'>Delete</a></div>";
    else:
        $document_div.="<div class='col-md-2' id='im".$document['media_id']."'><p><a href='".$image_home."/".$document['media_id']."'><i class='fa fa-file fa-document' aria-hidden='true'></i></a><br>".$document['details']['name']."<br> <a href='#' class='delete_attachment' id='".$document['media_id']."'>Delete</a></div>";
    endif;
    $document_listing.=$document['media_id']." ";
endforeach;
endif;

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
              
            
        
        //below is jquery for when the tag field is triggered
        $('#search_tag').on('input',function(e){ 
            
            $.get(
                '{$tagURL}',{search:$("#search_tag").val()},
                function(data) {     
                    //alert(data);
                    $('#tag_list').find('option').remove().end().append(data);
                    $('#tag_list').css("display","block");
                }    
            )
        });
        
       $(document).on('click', '#btnSubmit',
       function(ev) { 
                    if($("#title").val()==""){
                        alert("Your article must have a title");
                        return;
                    }
                    var chk_arr =  document.getElementsByName("blog_index[]");
                    var chklength = chk_arr.length;             
                    var blog_check=0;
                    for(k=0;k< chklength;k++)
                    {
                        if(chk_arr[k].checked){
                            blog_check++;
                        }
                    } 
                if(blog_check < 1){
                    alert("Your article must have at least one Blog Index selected");
                    return;
                }
                
                
                
                
        $("#lead_content").val(tinymce.get('lead_content').getContent());
        $("#body_content").val(tinymce.get('body_content').getContent());
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
                //location.href='?r=articles/index';
            }
        )
        ev.preventDefault();
  }); 
            
 
    
 $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=articles/index';
  }); 
 
 $(document).on('click', '#btnDuplicate',
           function(ev){
            if(confirm('Are you sure you wish to duplicate this article')){
             $.post(
                '{$duplicateURL}',$( "#frm1" ).serialize(),
                function(data) {
                    alert(data);
                
                }
                )
            }
            
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
                $("#my_display_image").attr("src","{$image_home}" + img_src);
                localStorage.removeItem("image_caller");
       }
       if(localStorage.image_caller=="set_thumbnail"){
                $("#thumbnail_image_id").val(my_id);
                $("#my_thumbnail_image").attr("src","{$image_home}" + img_src);
                localStorage.removeItem("image_caller");
       }
                
       $('#myModal').modal('toggle');     
   });
                
            
  $('.delete_event').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete - ' + event_name)){
                        $.get(  
                            '{$deleteURL}',{id:id},
                            function(data) {
                             var ids = "tr" + id;
                            $("#" + ids).remove();
                                alert(data);
                            }
                        )
                    }            
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
            
   

                            
  $('#unset_feature').click(function (element) {                    
                    var id = $(this).attr('id');
                    
                    if(confirm('Are you sure you want to remove this feature image')){
                        $("#display_image_id").val("0");
                        $("#my_display_image").attr("src","0");
                    }            
  });
      
  $('#unset_thumbnail').click(function (element) {                    
                    var id = $(this).attr('id');
                    if(confirm('Are you sure you want to remove this thumbnail image')){
                        $("#thumbnail_image_id").val("0");
                        $("#my_thumbnail_image").attr("src","0");
                    }            
  });
   
$('.delete_attachment').click(function(element){        
        var id = $(this).attr('id');
        if(confirm('Are you sure you want to delete the document from this article')){
                        $.get(  
                            '{$deleteAttachmentURL}',{id:id,article_id:'{$id}'},
                            function(data) {
                                alert(data);
                                var ob = "im" + id;
                                $("#" + ob).remove();
                            }
                        )
                    } 
        
   ev.preventDefault();                         
});
  
  $("#datalisting").DataTable();
                            
 $("#search_tag").keyup(function(e){
        var search_tag_val = $(this).val();
        if($(this).val()==""){
            $("#tag_list").css("display","none");             
        }else{
            var code = e.which; 
            if(code==13)e.preventDefault();
            if(code==32||code==13||code==188||code==186){
                //add this tag and also add it into the form
                $.get(  
                            '{$addTagURL}',{tag:$(this).val()},
                            function(data) {                                
                                $("#selected_tag").append("<span id='" + data + "'>" + search_tag_val + " <a href='#' onClick=\"javascript:remTag('" + data + "');return false\">Remove</a><br></span>");
                            }
                        )
                $(this).val("");
                $("#tag_list").css("display","none");
                return;
            }
            $("#tag_list").css("display","block");         
        }
    }) 
 
$("#lnkDetails").click(function(){
   $.get(
                '{$detailsURL}',{article:$(this).attr("account_id")},
                function(data) {                    
                    $("#details-content").html(data);
                }    
            )  
 })
   
$("#btnCustomAdd").click(function(){
                $("#custom_form_header").append("<tr><td>" + $("#yumpee_custom_field").val() + "<td><input class='form-control' type='text' name='" + $("#yumpee_custom_field").val() + "' id='" + $("#yumpee_custom_field").val() + "'>");    
})
    
                
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
<style>
    .modal-dialog{
    position: relative;
    display: table; /* This is important */ 
    overflow-y: auto;    
    overflow-x: auto;
    width: auto;
    min-width: 300px; 
    z-index:999;
}



 /* The sidepanel menu */
.sidepanel {
  height: 1000px; /* Specify a height */
  width: 0; /* 0 width - change this with JavaScript */
  position: fixed; /* Stay in place */
  z-index: 10; /* Stay on top */
  top: 200;
  left: 200;
  background-color: #ffffff; /* Black*/
  overflow-x: hidden; /* Disable horizontal scroll */
  padding-top: 60px; /* Place content 60px from the top */
  transition: 0.5s; /* 0.5 second transition effect to slide in the sidepanel */
}

/* The sidepanel links */
.sidepanel a {
  padding: 8px 8px 8px 32px;
  text-decoration: none;
  color: #818181;
  display: block;
  transition: 0.3s;
}

/* When you mouse over the navigation links, change their color */
.sidepanel a:hover {
  color: #f1f1f1;
}

/* Position and style the close button (top right corner) */
.sidepanel .closebtn {
  position: absolute;
  top: 0;
  right: 25px;
  font-size: 36px;
  margin-left: 50px;
}

/* Style the button that is used to open the sidepanel */
.openbtn {
  font-size: 20px;
  cursor: pointer;
  background-color: #111;
  color: white;
  padding: 10px 15px;
  border: none;
}

.openbtn:hover {
  background-color: #444;
} 
</style>

<?php
$display_image_path="";
$thumbnail_image_path="";
if(isset($rs->displayImage->path)):
    $display_image_path=$rs->displayImage->path;
endif;
if(isset($rs->thumbnail->path)):
    $thumbnail_image_path=$rs->thumbnail->path;
endif;
$tag_array="";
foreach($selected_tags as $st):
    $tag_array.=$st['id']." ";
endforeach;

?>
<div id="mySidepanel" class="sidepanel">
    <div class="container">
     <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
     <div id="details-content"></div>
    </div>
</div>

<div class="container-fluid">

<?php
if($id!=null):
?>
    <p align="right"><button class="btn btn-primary" data-toggle="collapse" onClick="javascript:window.open('<?=$home_url['setting_value']."/".$rs['indexURL']."/".$rs['url']?>','_blank')">Preview</button> <button class="btn btn-info" data-toggle="collapse" id="btnDuplicate">Save As New</button> 
<?php
endif;
?>
        
<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#addNews">Add Articles</a></li>
  <li><a data-toggle="tab" href="#article_list">Article Listing</a></li>
  
</ul>    
<div class="tab-content">    
<div id="addNews" class="tab-pane fade in active">
    <form action="index.php?r=news/index" method="post" name="frm1" id="frm1" class="form-group">
     <?php
        if(Yii::$app->request->get("id")!=null):
        ?>
        <div class="pull-right"><a href='#' onclick="openNav()" id="lnkDetails" account_id="<?=Yii::$app->request->get("id")?>"><i class="fa fa-info-circle"></i> Additional Details</a> | <a href='#' data-toggle="modal" data-dismiss="modal" data-target="#addfield">+ Add Field</a></div>
             
         
        <?php
        endif;
        ?>
    <table class="table" >
        
    </table>
    <table class="table">  
        <thead id="custom_form_header"></thead>
        <tr><td>Title *<td><input name="title" id="title" value="<?=$rs['title']?>" class="form-control" type="text"/>
                
        <tr><td>Article Header Type<td><?= \yii\helpers\Html::dropDownList("article_type",$rs['article_type'],['1'=>'Standard Article','2'=>'Generic Video','3'=>'Youtube Video','4'=>'Generic Audio'],['class'=>'form-control'])?>
        <tr><td>Video URL/Youtube Identifier/Audio URL<td><input type="text" class="form-control" name="featured_media" id="featured_media" value="<?=$rs['featured_media']?>"/>
        <tr><td>Feature Image<td><img id='my_display_image' src='<?=$image_home?>/<?=$display_image_path?>' height='100px' align='top' width='200px' style='border:1px solid #233388' HSPACE='20' VSPACE='20'/> <a href='#' class='media' id='set_feature'>Set Feature Image</a> | <a href='#' id='unset_feature'>Unset Feature Image</a> <input type="hidden" name="display_image_id" id="display_image_id" value="<?=$rs['display_image_id']?>"/>
        <tr><td>Thumbnail<td><img id='my_thumbnail_image' src='<?=$image_home?>/<?=$thumbnail_image_path?>' height='100px' align='top' width='100px' style='border:1px solid #233388' HSPACE='20' VSPACE='20'/> <a href='#' class='media' id='set_thumbnail'>Set thumbnail Image</a> | <a href='#' id='unset_thumbnail'>Unset thumbnail Image</a> <input type="hidden" name="thumbnail_image_id" id="thumbnail_image_id" value="<?=$rs['thumbnail_image_id']?>"/>
        <tr><td>Date Published *<td><?= DatePicker::widget([
    'name' => 'date',
    'value' => $rs['date'],
    'template' => '{addon}{input}',
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
]);?>
                
        <tr><td>URL<td><input name="url" id="url" value="<?=$rs['url']?>" class="form-control"type="text" />       
        <tr><td>Lead Content<td><textarea name="lead_content" id="lead_content" class="form-control"rows="7" cols="40"><?=$rs['lead_content']?></textarea>
        <tr><td>Body Content  <td><textarea name="body_content" id="body_content"  class="form-control"rows="7" cols="40"><?=$rs['body_content']?></textarea>
        <tr><td>Published<td><?=$published?>   &nbsp;&nbsp;&nbsp;Include Author Info <?=$published_by_stat?>        
        <tr><td valign='top'>Category<td><?=$category?>
        <tr><td valign='top'>Blog Index<td><?=$blog_index?>
        <tr><td>Renderer Template<td><?=$renderer?>
        <tr><td>Sort Order<td><input name="sort_order" id="sort_order" value="<?=$rs['sort_order']?>" class="form-control"type="text" /> 
        <tr><td>Require Login to view<td><?=\yii\helpers\Html::dropDownList("require_login",$rs['require_login'],['N'=>'No','Y'=>'Yes'],['class'=>'form-control'])?></td>
        <tr><td>Role Permission<td><?=$permissions?>    
        <tr><td>Disable comments on Article<td><?=\yii\helpers\Html::dropDownList("disable_comments",$rs['disable_comments'],['N'=>'No','Y'=>'Yes'],['class'=>'form-control'])?></td>    
        <tr><td>Attach Feedback Form<td><?=$feedback?>
        <tr><td>Tags<td>Type to drop down tags or press enter key to add new. Remember to save the article form if you delete tags for this article
        <tr><td>Search for a tags<td><input class="form-control"type="text" placeholder="Type tag" name="search_tag" id="search_tag" /> <select size=5 name=tag_list id=tag_list style="width:300px;display:none;height:100px" onChange="javascript:selectTag()" list='tag_listing'></select><span id="selected_tag">
                    <?php
                        foreach($selected_tags as $rec):
                    ?>
                        <span id="<?=$rec['id']?>"><?=$rec['name']?> <a href='#' onClick="javascript:remTag('<?=$rec['id']?>');return false">Remove</a><br /></span>
                    <?php
                        endforeach;
                    ?>
                </span>
        <?php
        if($id<>""):
        ?>
        <tr><td>Attachments<td>
                 <div class="panel-group">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">
                                <a data-toggle="collapse" href="#collapse1">Listings</a>
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
        <?php
        endif;
        ?>
        
        <tr><td colspan="2"><button type="button" class="btn btn-success" id="btnSubmit">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button><input type="hidden" name="processor" value="true" /><input type="hidden" name="id" value="<?=$id?>" /> <input type="hidden" name="tag_array" id="tag_array" value="<?=$tag_array?>" />
            <input type='hidden' name='document_listing' id='document_listing' value='<?=$document_listing?>'>
            </td>
        
        
    </table>
    </form>
</div>


<div class="box tab-pane fade" id="article_list">
<div class="box-body">
    <table id="datalisting" class="table table-bordered table-striped"><thead><tr><th>Display Image</th><th>Date Published</th><th>Title Tag / On-page Title</th><th>URL<th>Categories<th>Author<th>Order<th>Views</th><th>Published</th><th>Actions</th></thead>
        <tbody>
<?php

foreach ($records as $rec):
                if($rec['published']):
                    $published="Yes";
                else:
                    $published="No";
                endif;
                if($rec['require_login']=="Y"):
                    $lock="<sup><font color='red'><span class='fa fa-lock'></span></font></sup>";
                else:
                    $lock="";
                endif;
                
                if($rec['master_content']):
                    $master_content="Yes";
                else:
                    $master_content="No";
                endif;
                $display_image_path="";
                $thumbnail_image_path="";
                if(isset($rec->displayImage->path)):
                        $display_image_path=$rec->displayImage->path;
                endif;
                
                $categories="";
                foreach($rec->articleCategories as $category):
                    $categories.=$category->name.", ";
                endforeach;
?>
            <tr id="tr<?=$rec["id"]?>"><td><img src='<?=$image_home?>/<?=$display_image_path?>' width='80px' class="thumbnail"></img><td><?=$rec['date']?></td><td><?=$rec['title']?><?=$lock?></td><td><?=$rec['url']?><td><?=$categories?></td><td><?=$rec['author']['first_name']." ".$rec['author']['last_name']?></td><td><?=$rec['sort_order']?></td><td><?=$rec['no_of_views']?></td><td><?=$published?></td><td><a href='?actions=edit&id=<?=$rec['id']?>&r=articles/index' title="Edit"><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='?actions=edit&id=<?=$rec['id']?>&r=articles/index' title="Attachment"><small><i class="glyphicon glyphicon-paperclip"></i></small></a> <a href="#" onClick="javascript:window.open('<?=$home_url['setting_value']."/".$rec['indexURL']."/".$rec['url']?>','_blank')" title="Preview" id='<?=$rec['id']?>' class="preview_event"><small><i class="fa fa-eye"></i></small></a> <a href='#' class='delete_event' id='<?=$rec['id']?>' title="Delete" event_name='<?=$rec['title']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a> <a href='?r=comment/index&article_id=<?=$rec['id']?>' title="Comments"><span class="badge label label-primary"><?=count($rec['comments'])?></span></a></td>
<?php
endforeach;
?>
        </tbody>
</table>
</div>
</div>
</div>
</div>

<script language='Javascript'>
    
    function selectTag(){  
        //this function is used update the drop down list of the tags when you select the tag from the drop down list
            $("#tag_list").css("display","none");
            $("#search_tag").val('');
            if($("#tag_array").val().indexOf($("#tag_list").val()) > 0){
                
            }else{
            $("#tag_array").val($("#tag_array").val() + ' ' + ($("#tag_list").val()));            
            $("#selected_tag").append("<span id='" + $("#tag_list").val() + "'>" + $('#tag_list :selected').text() + " <a href='#' onClick=\"javascript:remTag('" + $("#tag_list").val() + "');return false\">Remove</a><br></span>");
        }
            
        }
    function remTag(id){
    //This function is called when you hit on the Remove tag link. After the tag has been removed, it also removes the ID from the hidden tag array to be sent to the server
        $('#' + id).remove();        
        $("#tag_array").val($("#tag_array").val().replace(id,''));        
    }
</script>

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

<div id="documentModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
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

<div id="addfield" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add field to article</h4>
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

<script>
    /* Set the width of the sidebar to 250px (show it) */
function openNav() {
  document.getElementById("mySidepanel").style.width = "1000px";
}

/* Set the width of the sidebar to 0 (hide it) */
function closeNav() {
  document.getElementById("mySidepanel").style.width = "0";
}
</script>
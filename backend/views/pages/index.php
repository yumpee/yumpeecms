<?php
$this->title = 'Pages';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use backend\models\Pages;
use backend\models\Templates;

$saveURL = \Yii::$app->getUrlManager()->createUrl('pages/save');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('pages/delete');
$duplicateURL = \Yii::$app->getUrlManager()->createUrl('pages/duplicate');
$tagURL =  \Yii::$app->getUrlManager()->createUrl('tags/search-tags');
$mediaURL =  \Yii::$app->getUrlManager()->createUrl('media/featured-media');
$image_home = Yii::getAlias('@image_dir/');

$this->registerJs( <<< EOT_JS
    tinymce.init({ editor_selector:'myTextEditor',
            mode : "specific_textareas",
           theme: 'modern',
        branding:false,
    width: 1000,
    height: 300,
    plugins: [
      'advlist autolink link lists charmap print preview hr anchor pagebreak spellchecker',
      'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
      'save table contextmenu directionality emoticons template paste textcolor yumpeemedia yumpeeslider'
    ],
    toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link print | preview media fullpage | forecolor backcolor emoticons yumpeemedia yumpeeslider' });
  
 
       $(document).on('click', '#btnSubmit',
       function(ev) {   
        $("#description").val(tinymce.get('description').getContent());
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
                //location.href='?r=pages/index';
            }
        )
        ev.preventDefault();
  });
            
  $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=pages/index';
        
        
  }); 
            
  $(document).on('click', '#btnDuplicate',
           function(ev){
            if(confirm('Are you sure you wish to duplicate this page')){
             $.post(
                '{$duplicateURL}',$( "#frm1" ).serialize(),
                function(data) {
                    alert(data);
                
                }
                )
            }
            
 });
            
  //below is jquery for when the tag field is triggered
  $('#search_tag').on('input',function(e){            
            $.get(
                '{$tagURL}',{search:$("#search_tag").val()},
                function(data) {                    
                    $('#tag_list').find('option').remove().end().append(data);
                    $('#tag_list').css("display","block");
                }    
            )
        });
            
  $('.delete_event').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete - ' + event_name + '?. This action will remove the page and all its related contents')){
                        $.get(  
                            '{$deleteURL}',{id:id},
                            function(data) {
                                alert(data);
                                var ids = "tr" + id;
                                $("#" + ids).remove();
                            }
                        )
                    }
                    ev.preventDefault(); 
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
       
                
       $('#myModal').modal('toggle');     
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
    .thumbnail:hover {
    position:relative;
    top:-25px;
    left:-35px;
    width:500px;
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
<?php
$display_image_path="";
if(isset($rs->displayImage->path)):
    $display_image_path=$rs->displayImage->path;
endif;
?>
 <div class="container-fluid">
<?php
if($id!=null):
?>
     <p align="right"><button class="btn btn-primary" data-toggle="collapse" onClick="javascript:window.open('<?=$home_url['setting_value']."/".$rs['url']?>','_blank')">Preview</button> <button class="btn btn-info" data-toggle="collapse" id="btnDuplicate">Save As New</button> 
<?php
endif;
?>
<div id="addPage">
    <form action="index.php?r=pages/index" method="post" id="frm1">
    <table class="table">
        <tr><td>Title Tag / On-page Title<td><input name="title" id="title" value="<?=$rs['title']?>" class="form-control" type="text" />
        <tr><td>Feature Image<td><img id='my_display_image' src='<?=$image_home?>/<?=$display_image_path?>' height='100px' align='top' width='200px' style='border:1px solid #233388' HSPACE='20' VSPACE='20'/> <a href='#' class='media' id='set_feature'>Set Feature Image</a> | <a href='#' id='unset_feature'>Unset Feature Image</a> <input type="hidden" name="display_image_id" id="display_image_id" value="<?=$rs['display_image_id']?>"/>
        <tr><td>Lead Paragraph  *<td><textarea name="description" id="description" class="form-control myTextEditor" rows="7" cols="40"><?=$rs['description']?></textarea>
        <tr><td>Menu Title<td><input name="menu_title" id="menu_title" value="<?=$rs['menu_title']?>" class="form-control"type="text" />
        <tr><td>Breadcrumb Title<td><input name="breadcrumb_title" id="breadcrumb_title" value="<?=$rs['breadcrumb_title']?>" class="form-control"type="text" />
        <tr><td>Meta Description<td><textarea name="meta_description" id="meta_description" class="form-control"rows="7" cols="40"><?=$rs['meta_description']?></textarea>
        <tr><td>Template<td><?=$template?>
        <tr><td>Select Form (Form templates only)<td><?=$forms?>
        <tr><td>List for Role (Users Index only)<td><?=$roles?>
        <tr><td>Role Renderer (Users Index only)<td><?=$renderer?>
        <tr><td>Sort Order<td><input name="sort_order" id="sort_order" value="<?=$rs['sort_order']?>" class="form-control"type="text" />
        <tr><td><td><input type="checkbox" /> Editable
        <tr><td>Show in main Menu</td><td><?=$rs['show_in_menu']?></td>
        <tr><td>URL<td><input name="url" id="url" value="<?=$rs['url']?>" class="form-control"type="text" />
        <tr><td>Meta Robots<td><input name="robots" id="robots" value="<?=$rs['robots']?>" class="form-control"type="text" />
        <tr><td>Parent Page<td><?=$parent_id?>
        <tr><td>Layout<td><?=$layout?>
        <tr><td>CSS Profile<td><?=$css?>
        <tr><td>Menu Profile<td><?=$menu_profile?>
        <tr><td>Require Login to view<td><?=\yii\helpers\Html::dropDownList("require_login",$rs['require_login'],['N'=>'No','Y'=>'Yes'],['class'=>'form-control'])?></td>
        <tr><td>Hide on Login<td><?=\yii\helpers\Html::dropDownList("hideon_login",$rs['hideon_login'],['N'=>'No','Y'=>'Yes'],['class'=>'form-control'])?></td>
        <tr><td colspan='2'><hr /></td>
        <tr class='addons' style="display:none"><td>Tag to display (<b>for 'tags page' templates only</b>)<td><?=$tags?>
        <tr class='addons' style="display:none"><td>Associated Tags (<b>for 'tag category' templates only</b>)<td><input class="form-control"type="text" placeholder="Type tag" name="search_tag" id="search_tag" /> <select size=5 name=tag_list id=tag_list style="width:300px;display:none;height:100px" onChange="javascript:selectTag()" list='tag_listing'></select><span id="selected_tag">
                    
                    <?php
                        foreach($selected_tags as $rec):
                    ?>
                        <span id="<?=$rec['id']?>"><?=$rec['name']?> <a href='#' onClick="javascript:remTag('<?=$rec['id']?>');return false">Remove</a><br /></span>
                    <?php
                        endforeach;
                    ?>
                </span>
        <tr><td colspan="2"><button type="button" class="btn btn-success" id="btnSubmit">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="processor" value="true" /><input type="hidden" name="id" value="<?=$id?>" /><input type="hidden" name="tag_array" id="tag_array" value="<?=$selected_tags[0]['id']?>" />
            
            </td>
    </table>
    </form>
</div>

<div class="box">
<div class="box-body">
    <table id="datalisting" class="table table-bordered table-striped"><thead><tr><th>Display Image</td><th>Title Tag / On-page Title<th>Menu Title<th>Template<th>URL<th>Parent Page<th>Published<th>Show in main menu<th>Global Content<th>Sort Order<th>Actions</thead>
    <tbody>
        <?php
        foreach ($records as $record):
                if($record['published']):
                    $published="Yes";
                else:
                    $published="No";
                endif;
                if($record['show_in_menu']):
                    $show_in_menu="Yes";
                else:
                    $show_in_menu="No";
                endif;
                if($record['master_content']):
                    $master_content="Yes";
                else:
                    $master_content="No";
                endif;
                $pname=Pages::find()->where(['id'=>$record['parent_id']])->one();
                $parent_name = $pname['title'];
                $tname = Templates::find()->where(['id'=>$record['template']])->one();
                $template = $tname['name'];
                $display_image_path="";
                
                if(isset($record->displayImage->path)):
                        $display_image_path=$record->displayImage->path;
                endif;
    ?>
    <tr id="tr<?=$record["id"]?>"><td><img src="<?=$image_home?>/<?=$display_image_path?>" width='40' class="thumbnail"></img><td><?=$record['title']?><td><?=$record['menu_title']?><td><?=$template?><td><?=$record['url']?><td><?=$parent_name?><td><?=$published?><td><?=$show_in_menu?><td><?=$master_content?><td><?=$record['sort_order']?><td><a href='?actions=edit&id=<?=$record['id']?>&r=pages/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a>  <a href="#" onClick="javascript:window.open('<?=$home_url['setting_value']."/".$record['url']?>','_blank')" title="Preview" id='<?=$rec['id']?>' class="preview_event"><small><i class="fa fa-eye"></i></small></a> <a href='#' class='delete_event' id='<?=$record['id']?>' event_name='<?=$record['title']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a></td>
<?php
        endforeach; 
?>
    <tbody>
</table>
</div>
</div>
</div>
     
  
  
</section>
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



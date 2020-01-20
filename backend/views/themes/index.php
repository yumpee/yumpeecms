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
$saveURL = \Yii::$app->getUrlManager()->createUrl('themes/save');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('themes/delete');
$this->registerJs( <<< EOT_JS
       tinymce.init({ 
           mode : "specific_textareas",
           editor_selector : "mceEditor",
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
  
       $(document).on('click', '#btnSubmit',
       function(ev) {   
        $("#header").val(tinymce.get('header').getContent());
        $("#footer").val(tinymce.get('footer').getContent());
        saveAce();    
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
                //location.href='?r=themes/index';
            }
        )
        ev.preventDefault();
  }); 
       $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=themes/index';       
        
  }); 
     
  $('.preview_event').click(function (element){
            var theme_id=$(this).attr('id');
            window.open('{$home_url}?yumpee_template_preview=on&theme_id=' + theme_id);
            
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
$("#datalisting").DataTable(); 
EOT_JS
);  
?>

<style type="text/css" media="screen">
    #yumpee_widget_content { 
        position: relative;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0px;
        min-width:1000px;
        height:500px;
     
    }
</style>


<div class="container-fluid">
<button class="btn btn-info" data-toggle="collapse" data-target="#addCategories">Toggle View</button>
<div id="addCategories">
     <form action="index.php?r=events/index" method="post" name="frm1" id="frm1">
    <table class="table">
        <tr><td>Theme Name<td><input name="name" id="name" value="<?=$rs['name']?>" class="form-control" type="text" />
        <tr><td>Folder<td><input name="folder" id="folder" value="<?=$rs['folder']?>" class="form-control" type="text" />
        <tr><td>Stylesheet (separate with ;)<td><input name="stylesheet" id="stylesheet" value="<?=$rs['stylesheet']?>" class="form-control" type="text" />   
        <tr><td>Javascript (separate with ;)<td><input name="javascript" id="javascript" value="<?=$rs['javascript']?>" class="form-control" type="text" />
        <tr><td valign='top'>Header<td><textarea id="header" name="header" class="mceEditor"><?=$rs['header']?></textarea>
        <tr><td valign="top">Footer<td><textarea id="footer" name="footer" class="mceEditor"><?=$rs['footer']?></textarea>
        <tr><td>CSS Style<td><textarea class="form-control" rows="8" name="custom_styles" style="display:none"><?=$rs['custom_styles']?></textarea><div id="yumpee_widget_content"></div>
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="id" value="<?=$id?>" />
            
            </td>
        
        
    </table>
    </form>
</div>

<div class="box">
<div class="box-body">
    <p align="right"><font color="red">**</font> denotes current theme used
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Theme<th>Folder<th>Actions</thead>
    <tbody>
      <?php
      foreach ($records as $user) :
          $bgcolor="";
          if($user['id']==$current_theme['setting_value']):
              $bgcolor="<font color=red>**</font>";
          endif;
      ?>
        <tr><td><?=$bgcolor?><?=$user['name']?></td><td><?=$user['folder']?><td> <a href='#' class='preview_event' id='<?=$user['id']?>' event_name='<?=$user['name']?>' title='Preview'><small><i class="glyphicon glyphicon-eye-open"></i></small></a> <a href='?actions=edit&id=<?=$user['id']?>&r=themes/manage-settings' title='Settings'><small><i class="fa fa-cog"></i></small></a> <a href='?actions=edit&id=<?=$user['id']?>&r=themes/index' title='Edit'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$user['id']?>' event_name='<?=$user['name']?>' title='Delete'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
     <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>
</div>
<script src="https://www.yumpeecms.com/yp-admin/js/ace-builds-master/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
    var editor = ace.edit("yumpee_widget_content");
    editor.setTheme("ace/theme/monokai");
    editor.session.setMode("ace/mode/css");
    function readAce(data){
       editor.getSession().setValue(data); 
        
    }
    function saveAce(){
       document.frm1.custom_styles.value=editor.getValue();
    }
    editor.getSession().setValue(document.frm1.custom_styles.value); 
</script>
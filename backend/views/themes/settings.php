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
$customURL =  \Yii::$app->getUrlManager()->createUrl('themes/fetch-twig-settings');
$saveURL =  \Yii::$app->getUrlManager()->createUrl('themes/save-twig-settings');

$this->registerJs( <<< EOT_JS
    
$("#theme").change(function(){
        var new_theme=$("#theme").val();
        location.href='?r=widgets/extensions&reload=true&theme=' + new_theme;
        
});
   
       $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=widgets/index';
        
        
  }); 
    
 $(document).on('click', '#btnSaveTwig',
       function(ev) {   
        saveAce();     
        $.post(
            '{$saveURL}',$( "#frmTheme" ).serialize(),
            function(data) {
                alert(data);
                
            }
        )
        ev.preventDefault();
  }); 
            
  $('.twig_event').click(function (element) {  
            
      var id = $(this).attr('id');
            
      var program_name = $(this).attr('event_name');
           
      var filename = $(this).attr('filename');
           
      
      var theme = id;
      document.frmTheme.renderer.value=id + "_" + program_name;
      document.frmTheme.theme_id.value=id;
        if(theme==""){
            alert("Please select a valid theme");
            return;
        }
      $.get(
                '{$customURL}',{renderer:id + "_" + program_name,theme_id:theme},
                function(data) {                     
                    //$('#yumpee_widget_content').text("");
                    //$('#yumpee_widget_content').text(data);
                    readAce(data);    
                    $('#myModal').modal();
                    $('#program_name').html(program_name);
                    $('#filename').val(filename);
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
</style>
<style type="text/css" media="screen">
    #yumpee_widget_content { 
        position: relative;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0px;
        min-width:1500px;
        height:800px;
     
    }
</style>
<div class="container-fluid">
<form id="frmTheme" name="frmTheme">
       
    <div class="box">
<div class="box-body">
    <p align="right"><font color="red">**</font> denotes current theme used
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Theme<th>Folder<th>Actions</thead>
    <tbody>
      <?php
      foreach ($records as $user) :
          $filename="";
          $bgcolor="";
          if($user['id']==$current_theme['setting_value']):
              $bgcolor="<font color=red>**</font>";
          endif;
        
          if(substr($user['hasContents']['filename'], 0, strlen("twig/")) === "twig/"):
                $filename = $user['hasContents']['filename'];
          endif;
          if($user['hasContents']!=null &&  $user['hasContents']['code']!=""):
          ?>
        <tr><td><font color='green'><?=$bgcolor?><?=$user['name']?></font></td><td><font color='green'><?=$user['folder']?></font><td><font color='green'><a href='#' class='twig_event' id='<?=$user['id']?>' event_name='<?=$user['folder']?>' title="Twig" filename="<?=$filename?>"><small><i class="fa fa-file-code-o"></i></small></a> </font></td>
          
          <?php
          else:
          ?>   
            <tr><td><?=$bgcolor?><?=$user['name']?></td><td><?=$user['folder']?><td><a href='#' class='twig_event' id='<?=$user['id']?>' event_name='<?=$user['folder']?>' title="Twig" filename="<?=$filename?>"><small><i class="fa fa-file-code-o"></i></small></a> </td>
          <?php
          endif;
      ?>
        
     <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>
    
 <div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="btn btn-primary" id="btnSaveTwig">Save</button> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Settings File name - <span id="program_name"></span> <input type="text" name="filename" id="filename" size="100" placeholder="File Location (for local files)"/></h4>
      </div>
      <div class="modal-body">
          <textarea rows="30" cols="100"  name="code" style="display:none"></textarea><input type="hidden" name="renderer" /><input type="hidden" name="theme_id" value="" />
          <div id="yumpee_widget_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btnSaveTwig">Save</button> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
 </div>
</form>   
    
</div>

<script src="js/ace-builds-master/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
    var editor = ace.edit("yumpee_widget_content");
    editor.setTheme("ace/theme/monokai");
    editor.session.setMode("ace/mode/twig");
    function readAce(data){
       editor.getSession().setValue(data); 
        
    }
    function saveAce(){
       document.frmTheme.code.value=editor.getValue();
    }
</script>
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

$customURL =  \Yii::$app->getUrlManager()->createUrl('forms/fetch-widget-twig-theme');
$saveURL =  \Yii::$app->getUrlManager()->createUrl('forms/save-widget-twig-theme');
$saveWidget = \Yii::$app->getUrlManager()->createUrl('forms/save-widget');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('forms/delete-widget');

$this->registerJs( <<< EOT_JS
 $("#theme").change(function(){
        var new_theme=$("#theme").val();
        location.href='?r=forms/fwidgets&reload=true&theme=' + new_theme;
        
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
  
  $(document).on('click', '#btnWidgetSubmit',
       function(ev) {   
        
        $.post(
            '{$saveWidget}',$( "#frmWidget" ).serialize(),
            function(data) {
                alert(data);
            }
        )
        ev.preventDefault();
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

            
  $('.twig_event').click(function (element) {  
      var id = $(this).attr('id');
      var program_name = $(this).attr('event_name');
      var filename = $(this).attr('filename');
          
      var getSelectedIndex = document.frmTheme.theme.selectedIndex;
      var theme = document.frmTheme.theme[getSelectedIndex].value;
      document.frmTheme.renderer.value=id;
        if(theme==""){
            alert("Please select a valid theme");
            return;
        }
      $.get(
                '{$customURL}',{renderer:id,theme_id:theme},
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
                
    if($("#custom_id").val()!=""){
        $('#widget').trigger('click')        
    }
                
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
    <h3>Custom Widgets</h3>
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#twig_tab">Manage Widget Twig</a></li>
        <li><a data-toggle="tab" href="#widget_tab" id="widget">Add Custom Widget</a></li>
  
    </ul>
<div class="tab-content">
        <div id="twig_tab" class="tab-pane fade in active">
<form id="frmTheme" name="frmTheme">
    <br />
    
    
    
    
    
    
    
    
    
    <table>
        <tr><td>Select Theme<td><?=$theme?>
    </table>
    <div class="box">
<div class="box-body">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Widget<th>Name<th>Primary Form<th>Actions</thead>
    <tbody>
      <?php
      foreach ($records as $user) :
          $filename="";  
      if($user['hasContents']!=null && $user['hasContents']['theme_id']==$selected_theme && $user['hasContents']['code']!=""):
          if(substr($user['hasContents']['filename'], 0, strlen("twig/")) === "twig/"):
                $filename = $user['hasContents']['filename'];
          endif;
      ?>
        <tr><td><font color='green'><?=$user['title']?></font></td><td><font color='green'><?=$user['name']?></font></td><td><font color='green'><?=$user['form']['title']?></font></td><td> <a href='#' class='twig_event' id='<?=$user['name']?>' event_name='<?=$user['title']." (".$user['name'].")"?>' title="User Create Twig" filename="<?=$filename?>"><small><i class="fa fa-file-code-o"></i></small></a> <a href='?actions=edit&id=<?=$user['id']?>&r=forms/fwidgets'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$user['id']?>' event_name='<?=$user['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a></td>
        <?php
            else:
        ?>
        <tr><td><?=$user['title']?></td><td><?=$user['name']?></td><td><?=$user['form']['title']?></td><td> <a href='#' class='twig_event' id='<?=$user['name']?>' event_name='<?=$user['title']." (".$user['name'].")"?>' title="User Create Twig" filename="<?=$filename?>"><small><i class="fa fa-file-code-o"></i></small></a> <a href='?actions=edit&id=<?=$user['id']?>&r=forms/fwidgets'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$user['id']?>' event_name='<?=$user['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a></td>
     <?php
        endif;
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
        <h4 class="modal-title">File name - <span id="program_name"></span> <input type="text" name="filename" id="filename" size="100" placeholder="File Location (for local files)"/></h4>
      </div>
      <div class="modal-body">
          <textarea rows="30" cols="100"  name="code" style="display:none"></textarea><input type="hidden" name="renderer" />
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
            
            <div id="widget_tab" class="tab-pane fade in">
                <div id="addUser">
     <form action="index.php?r=events/index" method="post" id="frmWidget">   
    <table class="table">
        <tr><td>Widget Name(no spaces)<td><input name="name" id="name" class="form-control" type="text" value="<?=$rs['name']?>"/>
        <tr><td>Title<td><input name="title" id="title" class="form-control" type="text" value="<?=$rs['title']?>"/>
        <tr><td>Primary Form<td><?=$forms?>
        <tr><td>Require Login to view<td><?=\yii\helpers\Html::dropDownList("require_login",$rs['require_login'],['N'=>'No','Y'=>'Yes'],['class'=>'form-control'])?></td>
        <tr><td>Role Permission<td><?=$permissions?>
        
        <tr><td colspan="2"><button type="submit" id="btnWidgetSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button><input type="hidden" name="processor" value="true" /><input type="hidden" name="id" id="custom_id" value="<?=$id?>" /></td>
    </table>
    </form>
</div>
            </div>
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
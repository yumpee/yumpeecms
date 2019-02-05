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
$saveURL =  \Yii::$app->getUrlManager()->createUrl('rating/save-profile-details');
$saveProfile = \Yii::$app->getUrlManager()->createUrl('rating/save-profile');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('rating/delete-rating');
$deleteDetailsURL = \Yii::$app->getUrlManager()->createUrl('rating/delete-details');

$this->registerJs( <<< EOT_JS
       
       $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=rating/index';
        
        
  }); 
        
  $(document).on('click', '#btnNewDetails',
       function(ev) {   
        location.href='?r=rating/index&tab=details';
        
        
  }); 
  
  $('.delete_rating').click(function (element) {                    
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
  $('.delete_details').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete - ' + event_name)){
                        $.get(  
                            '{$deleteDetailsURL}',{id:id},
                            function(data) {
                                alert(data);
                            }
                        )
                    }            
  });
                            
  $(document).on('click', '#btnProfileDetailSubmit',
       function(ev) {   
        
        $.post(
            '{$saveURL}',$( "#frmProfileDetails" ).serialize(),
            function(data) {
                alert(data);
                
            }
        )
        ev.preventDefault();
  }); 
  
  $(document).on('click', '#btnProfileSubmit',
       function(ev) {   
        
        $.post(
            '{$saveProfile}',$( "#frmProfile" ).serialize(),
            function(data) {
                alert(data);
            }
        )
        ev.preventDefault();
  });
            
  $('.twig_event').click(function (element) {  
      var id = $(this).attr('id');
            
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
                    $('#yumpee_widget_content').text("");
                    $('#yumpee_widget_content').text(data);
                    $('#myModal').modal();
                }    
            )
     ev.preventDefault(); 
  });
                
if($("#details_id").val()!="" || $("#details_tab").val()!=""){
  $('#manage_rating_tab').trigger('click')        
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
<div class="container-fluid">
    <h3>Ratings Management</h3>
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#widget_tab" id="widget">Add Profiles</a></li>
        <li><a data-toggle="tab" href="#twig_tab" id="manage_rating_tab">Manage Ratings</a></li>
        
  
    </ul>
<div class="tab-content">
    <div id="widget_tab" class="tab-pane fade in active">
                <div id="addUser">
     <form action="index.php?r=events/index" method="post" id="frmProfile">   
    <table class="table">
        <tr><td>Name(no spaces)<td><input name="name" id="name" class="form-control" type="text" value="<?=$rs['name']?>"/>
        <tr><td>Title<td><input name="title" id="title" class="form-control" type="text" value="<?=$rs['title']?>"/>
        <tr><td>Description<td><textarea class="form-control" name="description" rows="3" cols="40"><?=$rs['description']?></textarea>
        <tr><td colspan="2"><button type="submit" id="btnProfileSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button><input type="hidden" name="processor" value="true" /><input type="hidden" name="id" value="<?=$id?>" /></td>
    </table>
    </form>
    <div class="box">
<div class="box-body">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Profile Title<th>Profile Name<th>Actions</thead>
    <tbody>
      <?php
      foreach ($records as $user) :
      ?>
        <tr><td><?=$user['title']?></td><td><?=$user['name']?></td><td><a href='?actions=edit&id=<?=$user['id']?>&r=rating/index' title="Edit"><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_rating' id='<?=$user['id']?>' title="Delete" event_name='<?=$user['title']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
     <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>
</div>
            </div>
        <div id="twig_tab" class="tab-pane fade in">
<form id="frmProfileDetails" name="frmProfileDetails">
    <br />
    <table class="table">
        <tr><td>Rating Label<td><input name="rating_name" id="rating_name" class="form-control" type="text" value="<?=$rs_details['rating_name']?>"/>
        <tr><td>Rating Profile<td><?=$rating_profile?>
        <tr><td>Rating Value<td><input size="2" name="rating_value" id="rating_value" class="form-control" type="text" value="<?=$rs_details['rating_value']?>"/>
        <tr><td>Rating Color(RGB)<td><input size="8" name="rating_rgb_color" id="rating_rgb_color" class="form-control" type="text" value="<?=$rs_details['rating_rgb_color']?>"/>
        <tr><td colspan="2"><button type="submit" id="btnProfileDetailSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNewDetails" class="btn btn-primary">New</button><input type="hidden" name="processor" value="true" /><input type="hidden" name="id" id="details_id" value="<?=$details_id?>" />
            <input type="hidden" id="details_tab" value="<?=Yii::$app->request->get("tab")?>">
            </td>
    </table>
    <div class="box">
<div class="box-body">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Rating Label<th>Rating Profile<th>Rating Value<th>Rating Color<th>Actions</thead>
    <tbody>
      <?php
      foreach ($record_details as $user) :
      ?>
        <tr><td><?=$user['rating_name']?></td><td><?=$user['profile']['title']?></td><td><?=$user['rating_value']?></td><td><?=$user['rating_rgb_color']?></td><td> <a href='?actions=edit&details_id=<?=$user['id']?>&r=rating/index' title="Edit"><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_details' id='<?=$user['id']?>' title="Delete" event_name='<?=$user['rating_name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a> </td>
     <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>
 
</form>
        </div>
            
            
</div>


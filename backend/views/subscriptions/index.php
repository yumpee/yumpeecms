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

$this->title='Subscriptions';
$saveURL = \Yii::$app->getUrlManager()->createUrl('subscriptions/save');
$saveCategoryURL = \Yii::$app->getUrlManager()->createUrl('subscriptions/save-category');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('subscriptions/delete');
$deleteCategoryURL = \Yii::$app->getUrlManager()->createUrl('subscriptions/delete-category');
$this->registerJs( <<< EOT_JS
       
 
  $(document).on('click', '#btnSubmit',
       function(ev) {   
        
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
                location.href='?r=subscriptions/index';
            }
        )
        ev.preventDefault();
  });
        
  $(document).on('click', '#btnSubmitCategory',
       function(ev) {   
        
        $.post(
            '{$saveCategoryURL}',$( "#frmCategory" ).serialize(),
            function(data) {
                alert(data);
                location.href='?r=subscriptions/index';
            }
        )
        ev.preventDefault();
  });
            
 
 $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=subscriptions/index';     
        
  });
            
 $(document).on('click', '#btnNewCategory',
       function(ev) {   
        location.href='?r=subscriptions/index&cat_id=0';     
        
  }); 
            
  $('.delete_event').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete - ' + event_name)){
                        $.get(  
                            '{$deleteURL}',{id:id},
                            function(data) {
                                alert(data);
                                var ids = "tr" + id;
                                $("#" + ids).remove();
                            }
                        )
                    }            
  });
                            
  $('.delete_category').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete - ' + event_name)){
                        $.get(  
                            '{$deleteCategoryURL}',{id:id},
                            function(data) {
                                alert(data);
                                var ids = "tr" + id;
                                $("#" + ids).remove();
                            }
                        )
                    }            
  });                          
                            
if($("#cat_id").val()!=""){
  $('#bl_tab').trigger('click')        
 }
$("#datalisting").DataTable();                            
EOT_JS
);  
?>



<div class="container-fluid">
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home">Subscriptions</a></li>
    <li><a data-toggle="tab" href="#menu1" id="bl_tab">Subscription Categories</a></li>
    
  </ul>
<div class="tab-content">
<div id="home" class="tab-pane fade in active">
     <form action="index.php?r=subscriptions/index" method="post" id="frm1">
    <table class="table">
        <tr><td>Name<td><input name="name" id="name" value="<?=$rs['name']?>" class="form-control" type="text" />
        <tr><td>Email<td><input name="email" id="email" value="<?=$rs['email']?>" class="form-control" type="text" />
        <tr><td>Category<td><?=$category_dropdown?>
        
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="id" value="<?=$id?>" />
            
            </td>
        
        
    </table>
    </form>


<div class="box">
<div class="box-body">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Name<th>Email<th>Actions</thead>
    <tbody>
      <?php
      foreach ($records as $user) :
      ?>
        <tr id="tr<?=$user['id']?>"><td><?=$user['name']?></td><td><?=$user['email']?><td><a href='?actions=edit&id=<?=$user['id']?>&r=subscriptions/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$user['id']?>' event_name='<?=$user['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
     <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>
</div>
<div id="menu1" class="tab-pane fade">
  <form action="index.php?r=subscriptions/index" method="post" id="frmCategory">
    <table class="table">
        <tr><td>Category Name<td><input name="name" id="name" value="<?=$rs_category['name']?>" class="form-control" type="text" />
        <tr><td>Description<td><input name="description" id="email" value="<?=$rs_category['description']?>" class="form-control" type="text" />
               
        <tr><td colspan="2"><button type="submit" id="btnSubmitCategory" class="btn btn-success">Save</button> <button type="button" id="btnNewCategory" class="btn btn-primary">New</button> <input type="hidden" name="id" value="<?=$cat_id?>" />
            
            </td>
        
        
    </table>
    </form>


<div class="box">
<div class="box-body">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Name<th>Actions</thead>
    <tbody>
      <?php
      foreach ($category as $user) :
      ?>
        <tr id="tr<?=$user['id']?>"><td><?=$user['name']?></td><td><a href='?actions=edit&cat_id=<?=$user['id']?>&r=subscriptions/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_category' id='<?=$user['id']?>' event_name='<?=$user['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
     <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>      
</div>
</div>
</div>
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
$this->title="Configure form view";
$saveURL = \Yii::$app->getUrlManager()->createUrl('forms/save-configure');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('forms/delete-configure');

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

$('.delete_event').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete - ' + event_name)){
                        $.get(  
                            '{$deleteURL}',{id:id},
                            function(data) {
                                alert(data);
                                $("#row" + id).remove();
                            }
                        )
                    }            
  });
                            
$(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=forms/configure&form_id={$form_id}';
        
        
  }); 
            
EOT_JS
);         
?>
<div class="container-fluid">
<p align='right'><a href='?r=forms/index'>Back to forms</a>
<p>Fill below the fields you would like to display when this form is viewed in the backend
<form id='frm1'>
    <table class='table'>
        <tr><td width='30%'>Field Name<td><input type='text' class='form-control' name='field_name' value="<?=$rs['field_name']?>"></td>
        <tr><td>View Label<td><input type='text' class='form-control' name='view_label' value="<?=$rs['view_label']?>">
        <tr><td>View Order<td><input type='text' class='form-control' name='view_order' value="<?=$rs['view_order']?>"><input type='hidden' name='form_id' value='<?=$form_id?>'>
        <tr><td>Return Alias name<td><?=\yii\helpers\Html::dropDownList("return_alias",$rs['return_alias'],['N'=>'No','Y'=>'Yes'],['class'=>'form-control'])?>
        <tr><td>Value relates to class(optional)<td><input type='text' class='form-control' name='class_related' value="<?=$rs['class_related']?>">
        <tr><td>Value relates to property(optional)<td><input type='text' class='form-control' name='property_related' value="<?=$rs['property_related']?>">
        <tr><td>Return eval</td><td><input type='text' class='form-control' name='return_eval' value="<?=$rs['return_eval']?>">
        <tr><td>Return Widget Output<td><?=\yii\helpers\Html::dropDownList("return_widget",$rs['return_widget'],$custom_widget,['class'=>'form-control','prompt'=>''])?>
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button>
                <input type="hidden" name="id" value="<?=$id?>" />
    </table>   
</form>

<div class="box-body">
    <p align="right">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Field Label<th>Field Name<th>Class<th>Property<th>View Order<th>Actions</thead>
    <tbody>
      <?php
      foreach ($records as $user) :
          
      ?>
        <tr id="row<?=$user['id']?>"><td><?=$user['view_label']?></td><td><?=$user['field_name']?><td><?=$user['class_related']?><td><?=$user['property_related']?><td><?=$user['view_order']?><td><a href='?actions=edit&id=<?=$user['id']?>&r=forms/configure&form_id=<?=Yii::$app->request->get('form_id')?>'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$user['id']?>' event_name='<?=$user['field_name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
     <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>
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
$this->title='Relationships';
$saveURL = \Yii::$app->getUrlManager()->createUrl('relationships/save');

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
        location.href='?r=relationships/index';
        
        
  });
            
$("#datalisting").DataTable(); 
EOT_JS
);  
?>

<div class="container-fluid">
    <div class="box-body">
    <form action="index.php?r=relationships/index" method="post" id="frm1">
        <table class="table">
            <tr><td width="30%">Relationship Title : <td><input name="title" id="title" value="<?=$rs['title']?>" class="form-control" type="text" />
            <tr><td>Relationship ID : <td><input name="name" id="title" value="<?=$rs['name']?>" class="form-control" type="text" />
            <tr><td>Relationship Source Type :<td><?= \yii\helpers\Html::dropDownList("source_type",$rs['source_type'],['form-article'=>'Article','form-feedback'=>'Feedback','form-profile'=>'User Profile','form-twig'=>'Twig Form'],['class'=>'form-control','id'=>'source_type','prompt'=>''])?>
            <tr><td>Source Profile <td><?= \yii\helpers\Html::dropDownList("source_id",$rs['source_id'],$source_arr,['class'=>'form-control','id'=>'source_id'])?>
            <tr><td>Relationship Target Type:<td><?= \yii\helpers\Html::dropDownList("target_type",$rs['target_type'],['form-article'=>'Article','form-feedback'=>'Feedback','form-profile'=>'User Profile','form-twig'=>'Twig Form'],['class'=>'form-control','id'=>'target_type','prompt'=>''])?>
            <tr><td>Target Profile :<td><?= \yii\helpers\Html::dropDownList("target_id",$rs['target_id'],$target_arr,['class'=>'form-control','id'=>'target_id'])?>  
            <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="id" value="<?=$id?>" />
        </table>
    </form>
    </div>
    <div class="box-body">
    <p align="right">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Title<th>ID<th>Source Type<th>Source<th>Target Type<th>Target<th>Relationships<th>Actions</thead>
    <tbody>
      <?php
      foreach ($records as $relations) :         
      ?>
        <tr><td><?=$relations['title']?></td><td><?=$relations['name']?><td><?=$relations['source_type']?><td><?=$relations['source_id']?><td><?=$relations['target_type']?><td><?=$relations['target_id']?><td><?=$relations->relationCount?><td><a href='?actions=edit&id=<?=$relations['id']?>&r=relationships/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='?actions=edit&relations_id=<?=$relations['id']?>&r=relationships/configure' title="Configure Relationship"><small><i class="fa fa-cog"></i></small></a> <a href='#' class='delete_event' id='<?=$relations['id']?>' event_name='<?=$relations['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
     <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
    
    
    
</div>
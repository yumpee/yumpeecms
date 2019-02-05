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
$saveURL = \Yii::$app->getUrlManager()->createUrl('widgets/save');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('widgets/delete');
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
        location.href='?r=widgets/index';
        
        
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
EOT_JS
);  
?>



<div class="container-fluid">
<button class="btn btn-info" data-toggle="collapse" data-target="#addCategories">Toggle View</button>
<div id="addCategories">
     <form action="index.php?r=events/index" method="post" id="frm1">
    <table class="table">
        <tr><td>Widget Name<td><input name="name" id="name" value="<?=$rs['name']?>" class="form-control" type="text" />
        <tr><td>Short name<td><input name="short_name" id="folder" value="<?=$rs['short_name']?>" class="form-control" type="text" />
        <tr><td>Inherit data from<td><?=\yii\helpers\Html::dropDownList("parent_id",$rs['parent_id'],$widget_list,['class'=>'form-control',"prompt"=>''])?>
        <tr><td>Require Login to view<td><?=\yii\helpers\Html::dropDownList("require_login",$rs['require_login'],['N'=>'No','Y'=>'Yes'],['class'=>'form-control'])?></td>
        <tr><td>Role Permission<td><?=$permissions?>
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="id" value="<?=$id?>" />
            
            </td>
        
        
    </table>
    </form>
</div>

<div class="box">
<div class="box-body">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Widget<th>Short Name<th>Inherit From<th>Template Type<th>Actions</thead>
    <tbody>
      <?php
      foreach ($records as $user) :
          $tem_type="System";
          if($user->template_type=="C"):
              $tem_type="<font color=blue>Custom</font>";
          endif;
      ?>
        <tr><td><?=$user['name']?></td><td> <?=$user['short_name']?><td><?=$user['parent']['name']?><td><?=$tem_type?><td> <a href='?actions=edit&id=<?=$user['id']?>&r=widgets/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$user['id']?>' event_name='<?=$user['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
     <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>
</div>
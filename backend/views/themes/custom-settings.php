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
$custom_name="";
//$custom_id="0";
$customURL = \Yii::$app->getUrlManager()->createUrl('themes/custom-save');
$deleteCustomURL =  \Yii::$app->getUrlManager()->createUrl('themes/delete-custom');
$importURL = \Yii::$app->getUrlManager()->createUrl('themes/import-theme');

$this->registerJs( <<< EOT_JS
$(document).on('click', '#btnSubmitCustom',
       function(ev) {   
        
        $.post(
            '{$customURL}',$( "#frmCustom" ).serialize(),
            function(data) {
                alert(data);
            }
        )
        ev.preventDefault();
});         
   

$('.delete_custom').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete - ' + event_name)){
                        $.get(  
                            '{$deleteCustomURL}',{id:id},
                            function(data) {
                                alert(data);
                            }
                        )
                    }            
  });     
      
 $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=themes/manage-settings&actions=edit&id={$theme_id}';
  });
        
 $(document).on('click', '#btnImportSave',
       function(ev) {   
        if(!confirm("Are you sure you want to import settings. This will overwrite existing settings if it exists in this theme")){
            return;
        }
        $.post(
            '{$importURL}',$( "#frmImport" ).serialize(),
            function(data) {
                alert(data);
                location.href='?r=themes/manage-settings&actions=edit&id={$theme_id}';
            }
        )
        ev.preventDefault();
});
 
EOT_JS
);         
?>
<div class="container-fluid">
    <p align="right"> <a href="#" id="lnkImport" data-toggle="modal" data-target="#modalThemes"><i class="fa fa-file"></i> Import Settings</a>
        <form action="index.php?r=settings/index" method="post" id="frmCustom">
    <table class="table">
        <tr><td>Setting Name<td><input name="setting_name" id="setting_name" value="<?=$custom_rs['setting_name']?>" class="form-control" type="text" />
        <tr><td>Setting Value<td><input name="setting_value" id="setting_value" value="<?=$custom_rs['setting_value']?>" class="form-control" type="text" />
        <tr><td>Description<td><textarea class="form-control" name="description"><?=$custom_rs['description']?></textarea>
        
        <tr><td colspan="2"><button type="submit" id="btnSubmitCustom" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" id="custom_id" name="id" value="<?=$custom_id?>" />
        <input type="hidden" name="theme_id" value="<?=$theme_id?>" />
            
            </td>
        
        
    </table>
    </form>
        <div class="box">
            <div class="box-body">
                <table id="datalisting" class="table table-bordered table-striped">
                    <thead><tr><th>Name<th>Value<th>Actions</thead>
                    <tbody>
                        <?php
                            foreach ($custom_records as $user) :
                        ?>
                        <tr><td><?=$user['setting_name']?></td><td><?=$user['setting_value']?><td> <a href='?actions=edit&custom_id=<?=$user['id']?>&r=themes/manage-settings&id=<?=$theme_id?>'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_custom' id='<?=$user['id']?>' event_name='<?=$user['setting_name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
                        <?php
                        endforeach;
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

    
</div>

<div id="modalThemes" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
    <form id="frmImport" method="post">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Import Theme settings</h4>
      </div>
      <div class="modal-body">
        <p>Select the theme to import settings from.</p>
        <?= \yii\helpers\Html::dropDownList("target_theme","0",$theme_list,['class'=>'form-control'])?>
      </div>
      <div class="modal-footer">
          <button class="btn btn-primary" type="button" id="btnImportSave">Import</button> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <input type="hidden" name="current_theme" value="<?=$theme_id?>" />
      </div>
    </form>
    </div>

  </div>
</div>



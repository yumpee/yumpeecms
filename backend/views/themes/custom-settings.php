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
$custom_id="0";
$customURL = \Yii::$app->getUrlManager()->createUrl('themes/custom-save');
$deleteCustomURL =  \Yii::$app->getUrlManager()->createUrl('themes/delete-custom');

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
  
EOT_JS
);         
?>
<div class="container-fluid">

        <form action="index.php?r=settings/index" method="post" id="frmCustom">
    <table class="table">
        <tr><td>Setting Name<td><input name="setting_name" id="setting_name" value="<?=$custom_name?>" class="form-control" type="text" />
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
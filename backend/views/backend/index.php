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
$this->title = 'Manage Menus';

$saveURL = \Yii::$app->getUrlManager()->createUrl('backend/save');
$applyURL = \Yii::$app->getUrlManager()->createUrl('backend/apply');

$this->registerJs( <<< EOT_JS
  $(document).on('click', '#btnSubmit',
       function(ev) { 
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
                location.href='?r=backend/index';
            }
        )
        ev.preventDefault();
  }); 
  
  $(document).on('click', '#btnApply',
       function(ev) { 
        $.post(
            '{$applyURL}',$( "#frmApply" ).serialize(),
            function(data) {
                alert(data);
                //location.href='?r=backend/index';
            }
        )
        ev.preventDefault();
  }); 
            
$(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=backend/index';
        
        
  });
            
$( "#role_id" ).change(function() {
  location.href='?r=backend/index&role_id=' + $("#role_id").val();
});
 
if($("#role_id").val()!=""){
  $('#bl_tab').trigger('click')        
 }
   $("#datalisting").DataTable();                          
  
EOT_JS
);  

if($rs['custom_stat']=="N" || $rs['original_label']=="Custom" || $rs['original_label']=="Setup" || $rs['original_label']=="Form Data"):
    $readonly="readonly";
else:
    $readonly="";
endif;

?>

<div class="container-fluid">
    
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home">Manage Menus</a></li>
    <li><a data-toggle="tab" href="#permissions" id="bl_tab">Role Permissions</a></li>
    
  </ul>
    
<div class="tab-content">
    <div id="home" class="tab-pane fade in active"> <br />   

<div id="addBlock">
     <form action="index.php?r=blocks/index" method="post" id="frm1">
    <table class="table">
        <tr><td>Menu Label<td><input name="label" id="name" value="<?=$rs['label']?>" maxlength="100" class="form-control" type="text" />
        <tr><td>URL<td><input name="url" id="url" value="<?=$rs['url']?>" <?=$readonly?> class="form-control" type="text" />        
        <tr><td>Icon  <td><input name="icon" id="icon" value="<?=$rs['icon']?>" class="form-control" type="text" /> 
        <tr><td>Parent Menu<td><?=$parent_menus?>              
        <tr><td>Priority<td><input name="priority" id="priority" value="<?=$rs['priority']?>" class="form-control" type="text" /> 
        <tr><td>Notes<td><textarea rows="5" class="form-control" name="notes"><?=$rs['notes']?></textarea> 
        <tr><td colspan="2"><button type="button" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="processor" value="true" /> <input type="hidden" name="id" value="<?=$rs['id']?>" /><input type="hidden" name="cont" id="cont"/></td>
        
        
    </table>
    </form>
</div>
<div class="box">
<div class="box-body">
    <table id="datalisting" class="table table-bordered table-striped"><thead><tr><th>Label</td><th>Parent<th>URL<th>Icon<th>Priority<th>Custom Menu<th>Original Label<th>Actions</thead>
        <tbody>
        <?php
        foreach ($records as $record):
                if($record['custom_stat']=="N"):
                    $custom="No";
                    $delete="none";
                else:
                    $custom="Yes";
                    if($record['original_label']=="Custom" || $record['original_label']=="Form Data"):
                        $delete="none";
                    else:
                        $delete="";
                    endif;
                    
                endif;
            ?>
            <tr><td><?=$record['name']?><td><?=$record['parent']['label']?><td><?=$record['url']?></td><td><?=$record['icon']?></td><td><?=$record['priority']?></td><td><?=$custom?></td><td><?=$record['original_label']?></td><td><a href='?actions=edit&id=<?=$record['id']?>&r=backend/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <span style="display:<?=$delete?>"><a href='#' class='delete_event' id='<?=$record['id']?>' block='<?=$record['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a></span>
        <?php
        endforeach;
        ?>
        </tbody>
</table>
</div>
</div>
</div>
<div id="permissions" class="tab-pane fade">
    <form action="index.php?r=blocks/index" method="post" id="frmApply">
    <table class="table">
        <tr><td>Select Role<td><?=$roles?>
        <tr><td>Menus<td><?=$menus_list?> 
        <tr><td colspan="2"><button type="button" id="btnApply" class="btn btn-success">Apply</button> <input type="hidden" name="processor" value="true" /> <input type="hidden" name="role_id" id="role_id" value="<?=Yii::$app->request->get("role_id")?>" /><input type="hidden" name="cont" id="cont"/></td>
        
        
    </table>
    </form> 
        
</div>
</div>
</div>
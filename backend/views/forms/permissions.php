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
$permissionPostURL = \Yii::$app->getUrlManager()->createUrl('forms/add-permissions');
if($can_create=="on"):
    $can_create=" checked";
endif;
?>

<div class="container-fluid">
<form id="permission_form">
<h3>Permissions for <?=$selected_role?>  </h3>
<p>Set permissions that this role has <br>
Can Create Record <input type="checkbox" name="can_create_record" <?=$can_create?>> <br>
Can update records created by the following roles
<?=$update_roles?>
Can view records created by the following roles<br>
<?=$view_roles?>
Can delete records created by the following roles <br>
<?=$delete_roles?>
			
<p align="right"><button type="button" id="btnPermission" class="btn btn-success">Update Permissions</button>&nbsp;&nbsp;
    <input type="hidden" name="permission_form_id" value="<?=$form_id?>">
    <input type="hidden" name="permission_role_id" value="<?=$role_id?>">
</form>
</div>

<script>
    $('#btnPermission').click(function(){
        //alert("{$permissionPostURL}");
         $.post(
            '<?=$permissionPostURL?>',$( "#permission_form" ).serialize(),
            function(data) {
                alert(data);                
            }
        )
});
</script>
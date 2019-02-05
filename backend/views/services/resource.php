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


?>
<div class="container-fluid">
<div id="addResource">
     <form action="index.php?r=services/outgoing" method="post" name="frm1" id="frm1">
    <table class="table">
        <tr><td>Resource Name<td><input name="alias" id="alias" value="" class="form-control" type="text" />
        <tr><td>Resource ID<td><input name="name" id="name" value="" class="form-control" type="text" />
        <tr><td>Resource Type<td><?=\yii\helpers\Html::dropDownList("resource_type",$resource_type,['articles'=>'Articles','form'=>'Form Data','profile'=>'User Profile'],['class'=>'form-control','prompt'=>'Select a resource type'])?>
        <tr><td>Resource<td><?=\yii\helpers\Html::dropDownList("resource_type",$resource_type,['articles'=>'Articles','form'=>'Form Data','profile'=>'User Profile'],['class'=>'form-control','prompt'=>'Select a resource type'])?>
        <tr><td>Description<td><textarea name="description" id="description" rows="2" cols="30" class="form-control"></textarea>
        
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="processor" value="true" /><input type="hidden" name="id" value="" />
            
            </td>
    </table>
    </form>
</div>


<div class="box">
<div class="box-body">
    <table id="datalisting" class="table table-bordered table-hover"><thead><tr><th>Name</th><th>Client ID</th><th>Key<th>Action</thead>
        <tbody>
<?php
    foreach($records as $record):
                
?>
    <tr><td><?=$record['name']?></td><td><?=$record['client_id']?><td><?=$record['client_key']?></td><td><a href='?actions=edit&id=<?=$record['id']?>&r=services/outgoing'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$record['id']?>' event_name='<?=$record['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a> </td>
        
 <?php
        endforeach;
?>
        </tbody>
</table>
</div>
</div>
</div>


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
$this->title = 'Outgoing Profiles';


$saveURL = \Yii::$app->getUrlManager()->createUrl('services/outgoing-save');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('services/outgoing-delete');
$this->registerJs( <<< EOT_JS
       $(document).on('click', '#btnSubmit',
       function(ev) {  
        
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
                location.href='?r=services/outgoing';
            }
        )
        ev.preventDefault();
  }); 
  
  $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=services/outgoing';
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
                            
$("#datalisting").DataTable();   
EOT_JS
);  
?>

<style type="text/css" media="screen">
    #yumpee_widget_content { 
        position: relative;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0px;
        min-width:1000px;
        height:500px;
     
    }
</style>

<?php
$authentication=$encryption=$auth_url=$body_content=$authenticate_method=$bearer_token="";

$config = json_decode($rs['config']);
if(isset($config->authentication)):
    $authentication=($config ? $config->authentication:"");
endif;
if (isset($config->encryption)):
    $encryption=($config ? $config->encryption:"");
endif;
if (isset($config->auth_url)):
    $auth_url=($config ? $config->auth_url:"");
endif;
if(isset($config->body_content)):
    $body_content=($config ? $config->body_content:"");
endif;
if(isset($config->authenticate_method)):
    $authenticate_method=($config ? $config->authenticate_method:"");
endif;
if(isset($config->bearer_token)):
    $bearer_token=($config ? $config->bearer_token:"");
endif;
?>
<div class="container-fluid">
<div id="addCategories">
     <form action="index.php?r=services/outgoing" method="post" name="frm1" id="frm1">
    <table class="table">
        <tr><td width="30%">Client Name<td><input name="name" id="name" value="<?=$rs['name']?>" class="form-control" type="text" />
        <tr><td>Authentication Type<td><?=\yii\helpers\Html::dropDownList("authentication",$authentication,['basic'=>'Basic','bearer'=>'Bearer Token','oauth'=>'OAuth'],['class'=>'form-control'])?></td>
        <tr><td>Encryption Type(for Basic)<td><?=\yii\helpers\Html::dropDownList("encryption",$encryption,['plain'=>'Plain','base64'=>'Base64'],['class'=>'form-control'])?></td>
        <tr><td>Client ID<td><input name="client_id" id="client_id" value="<?=$rs['client_id']?>" class="form-control" type="text" />
        <tr><td>Client / Token Key<td><input name="client_key" id="client_key" value="<?=$rs['client_key']?>" class="form-control" type="text" />
        <tr><td>Authentication URL (for Bearer Token / OAuth 2 )<td><input name="auth_url" id="auth_url" value="<?=$auth_url?>" class="form-control" type="text" />
        <tr><td>Preferred Authentication Method<td><?=\yii\helpers\Html::dropDownList("authenticate_method",$authenticate_method,['POST'=>'POST','GET'=>'GET'],['class'=>'form-control'])?>      
        <tr><td>Preferred Authentication Header<td><textarea name="header" id="header" rows="8" cols="30" class="form-control"><?=$rs['header']?></textarea>
        <tr><td>Preferred Authentication Body(JSON)<td><textarea name="body_content" id="body_content" rows="8" cols="30" class="form-control"><?=$body_content?></textarea>        
        <tr><td>Bearer Token Variable Name<td><input name="bearer_token" id="bearer_token" value="<?=$bearer_token?>" class="form-control" type="text" />
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="processor" value="true" /><input type="hidden" name="id" value="<?=$rs['id']?>" />
            
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

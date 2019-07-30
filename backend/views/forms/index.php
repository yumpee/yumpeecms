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

$this->title='Forms';
$saveURL = \Yii::$app->getUrlManager()->createUrl('forms/save');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('forms/delete');
$notifyURL = \Yii::$app->getUrlManager()->createUrl('web-hook-email/notify');
$formURL = \Yii::$app->getUrlManager()->createUrl('web-hook-email/form');
$responseURL = \Yii::$app->getUrlManager()->createUrl('web-hook-email/response');
$internalURL = \Yii::$app->getUrlManager()->createUrl('web-hook/internal');
$externalURL = \Yii::$app->getUrlManager()->createUrl('web-hook/external');
$permissionURL = \Yii::$app->getUrlManager()->createUrl('forms/permissions');

$this->registerJs( <<< EOT_JS
       tinymce.init({ 
           mode : "specific_textareas",
           editor_selector : "mceEditor",
           theme: 'modern',
    width: 1000,
    height: 300,
        file_picker_callback: function(callback, value, meta) {
            $('#myModal').modal('show');
        },
    file_picker_types: 'file image media',
    plugins: [
      'advlist autolink link lists charmap print preview hr anchor pagebreak spellchecker',
      'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
      'save table contextmenu directionality emoticons template paste textcolor yumpeemedia yumpeeslider'
    ],
    toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons yumpeemedia yumpeeslider' });
  
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
  $(document).on('click', '#btnNotify',
       function(ev) {   
        $.post(
            '{$notifyURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);                
            }
        )
        ev.preventDefault();
  });
            
  $(document).on('click', '#btnFormNotify',
       function(ev) {   
        $.post(
            '{$formURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);                
            }
        )
        ev.preventDefault();
  });
            
  $(document).on('click', '#btnResponse',
       function(ev) {   
        $.post(
            '{$responseURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);                
            }
        )
        ev.preventDefault();
  });
  
  $(document).on('click', '#btnInternal',
       function(ev) {   
        $.post(
            '{$internalURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);                
            }
        )
        ev.preventDefault();
  });
  $(document).on('click', '#btnExternal',
       function(ev) {   
        $.post(
            '{$externalURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);                
            }
        )
        ev.preventDefault();
  });
            
       $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=forms/index';
        
        
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
 

$(".role_permit_label").each(function(){
    
    if($("#form_id_val").val()!=""){
        $(this).html($(this).html() + "<a href='#' class='permission_fetcher' role_id='" + $(this).find("input").val() + "' role_name='" + $(this).text() + "' data-toggle='collapse' data-target='#demo'><i class='fa fa-caret-down'></i></a>");
    }
})
                           
$(".permission_fetcher").click(function(){        
        $.get(
            '{$permissionURL}',{'role':$(this).attr("role_name"),'role_id':$(this).attr("role_id"),'form_id':'{$id}'},
            function(data) {
                $("#demo").html(data);         
            }
        )             
});
 

    
$("#datalisting").DataTable(); 
EOT_JS
);  
?>



<div class="container-fluid">
    
<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#addForms">Forms</a></li>
  <li><a data-toggle="tab" href="#forms_list">Forms Listing</a></li>
  
</ul>
<div class="tab-content">
<div id="addForms" class="tab-pane fade in active">
     <form action="index.php?r=forms/index" method="post" id="frm1">
    <table class="table">
        <tr><td width="30%">Form Title / Label<td><input name="title" id="title" value="<?=$rs['title']?>" class="form-control" type="text" />
        <tr><td>Form ID<td><input name="name" id="name" value="<?=$rs['name']?>" class="form-control" type="text" />
        <tr><td>Form Type<td><?= \yii\helpers\Html::dropDownList("form_type",$rs['form_type'],['form-article'=>'Article','form-feedback'=>'Feedback','form-profile'=>'User Profile','form-twig'=>'Twig Form'],['class'=>'form-control'])?>       
        <tr><td>Form fill entry type<td><?=\yii\helpers\Html::dropDownList("form_fill_entry_type",$rs['form_fill_entry_type'],['S'=>'Single','M'=>'Multiple'],['class'=>'form-control'])?></td>   
        <tr><td>Form Fill limit (how many entries)<td><input name="form_fill_limit" id="form_fill_limit" value="<?=$rs['form_fill_limit']?>" class="form-control" type="text" />
        <tr><td>Display On Backend Menu<td><?=\yii\helpers\Html::dropDownList("show_in_menu",$rs['show_in_menu'],['N'=>'No','Y'=>'Yes'],['class'=>'form-control'])?>
        <tr><td>Auto-publish form data to views<td><input type="checkbox" name="published" <?=$published?>> 
        <tr><td>Role permissions<td><?=$roles?>
                <div id="demo" class="collapse panel panel-default">
                            
                </div>
        <?php
        if($id!=null):
        ?>
        
        <tr><td>Process on submission<td><ul class="nav nav-tabs">
                                        <li class="active"><a data-toggle="tab" href="#w_email">Notify Admin Email</a></li>
                                        <li><a data-toggle="tab" href="#w_nmail">Notify Form Email</a></li>
                                        <li><a data-toggle="tab" href="#w_remail">Response Email</a></li>
                                        <li><a data-toggle="tab" href="#w_internal">Internal Web Hooks</a></li>
                                        <li><a data-toggle="tab" href="#w_external">External API Calls</a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <div id="w_email" class="tab-pane fade in active"> <br />
                                                <p>Configure the email to notify on submission</p>
                                                
                                                <p>Email Address:
                                                <input type="text" class="form-control" name='notify_email' value="<?=$notify_rs['email']?>"/>
                                                <p>Email Subject:
                                                <input type="text" class="form-control" name='notify_subject' value="<?=$notify_rs['subject']?>"/>
                                                Message:
                                                <textarea class="form-control" rows="10" name="notify_message"><?=$notify_rs['message']?></textarea>
                                                <p><input type="checkbox" name="notify_send_data" <?=$notify_send_data?>/> Include form data submitted
                                                <p align="right"><input type="button" class="btn btn-success" id='btnNotify' value="Update" />
                                                
                                            </div>
                                            <div id="w_nmail" class="tab-pane fade"> 
                                                <p>Configure the response message on submission
                                                <p>Enter form field to notify
                                                    <input type="text" class="form-control" name='form_email' value="<?=$form_rs['email']?>"/>
                                                <p>Email Subject:
                                                <input type="text" class="form-control" name='form_subject' value="<?=$form_rs['subject']?>"/>
                                                Message:
                                                <textarea class="form-control" rows="10" name="form_message"><?=$form_rs['message']?></textarea>
                                                <p><input type="checkbox" name="form_send_data" <?=$form_send_data?>> Include form data submitted
                                                <p align="right"><input type="button" class="btn btn-success" id='btnFormNotify' value="Update" />
                                            </div>
                                            <div id="w_remail" class="tab-pane fade"> 
                                                <p>Configure the response message on submission
                                                <p>Select email Address field:
                                                    <select class="form-control" name='response_email'><option value="0">From User Profile</option></select>
                                                <p>Email Subject:
                                                <input type="text" class="form-control" name='response_subject' value="<?=$response_rs['subject']?>"/>
                                                Message:
                                                <textarea class="form-control" rows="10" name="response_message"><?=$response_rs['message']?></textarea>
                                                <p><input type="checkbox" name="response_send_data" <?=$response_send_data?>> Include form data submitted
                                                <p align="right"><input type="button" class="btn btn-success" id='btnResponse' value="Update" />
                                            </div>
                                            <div id="w_internal" class="tab-pane fade"> 
                                                <p>Configure Internal Hook calls on submission
                                                <p>End Point
                                                    <input type="text" class="form-control" name="internal_endpoint" value="<?=$win_rs["end_point"]?>"/>
                                                <p>JSON template data
                                                <textarea class="form-control" rows="10" name="internal_json_data"><?=$win_rs["json_data"]?></textarea>
                                                <p align="right"><input type="button" id="btnInternal" class="btn btn-success" value="Update" />
                                            </div>
                                            <div id="w_external" class="tab-pane fade"> 
                                                <p>Configure External API calls on submission
                                                <p>External Client Profile
                                                <p><?=\yii\helpers\Html::dropDownList("external_profile",$wex_rs['client_profile'],$client_profiles,['class'=>'form-control','prompt'=>'None'])?>
                                                <p>Call Type : 
                                                <p><?=\yii\helpers\Html::dropDownList("external_post",$wex_rs['post_type'],['G'=>'GET','P'=>'POST','T'=>'PUT','D'=>'DELETE'],['class'=>'form-control'])?>
                                                <p>End Point URL
                                                 <input type="text" name="external_endpoint" value="<?=$wex_rs["end_point"]?>" class="form-control" />
                                                <p>JSON template data
                                                <textarea class="form-control" rows="10" name="external_json_data"><?=$wex_rs["json_data"]?></textarea>
                                                <p>Response Target
                                                <p><?=\yii\helpers\Html::dropDownList("external_response_target",$wex_rs["response_target"],$widgets,['class'=>'form-control','prompt'=>''])?>
                                                <p align="right"><input type="button" class="btn btn-success" id="btnExternal" value="Update" />
                                                
                                            </div>
        
                                        </div>
        <?php
        endif;
        ?>
                                        
  
                    
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="id" id="form_id_val" value="<?=$id?>" />
            
            </td>
        
        
    </table>
    </form>
</div>

<div class="box tab-pane fade" id="forms_list">
<div class="box-body">
    <p align="right">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>ID<th>Form<th>Form ID<th>Form Type<th>Actions</thead>
    <tbody>
      <?php
      foreach ($records as $user) :
          
      ?>
        <tr id="tr<?=$user['id']?>"><td><?=$user['id']?><td><?=$user['title']?></td><td><?=$user['name']?><td><?=$user['form_type']?><td><a href='?actions=edit&id=<?=$user['id']?>&r=forms/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='?actions=edit&id=<?=$user['id']?>&r=forms/data' title="View Data"><small><i class="fa fa-eye"></i></small></a> <a href='?actions=edit&form_id=<?=$user['id']?>&r=forms/configure' title="Configure View"><small><i class="fa fa-cog"></i></small></a> <a href='#' class='delete_event' id='<?=$user['id']?>' event_name='<?=$user['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
     <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>
</div>
</div>
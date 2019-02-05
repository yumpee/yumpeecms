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
$this->title = 'Users';

$saveURL = \Yii::$app->getUrlManager()->createUrl('users/save-profile');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('users/delete');
$saveRoleURL = \Yii::$app->getUrlManager()->createUrl('users/save-role');
$deleteRoleURL = \Yii::$app->getUrlManager()->createUrl('users/delete-role');
$mediaURL =  \Yii::$app->getUrlManager()->createUrl('media/featured-media');

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
        location.href="?r=users/index";
        ev.preventDefault();
  });
  
       $(document).on('click', '#btnSubmitRole',
       function(ev) {   
        $.post(
            '{$saveRoleURL}',$( "#frmRole" ).serialize(),
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
                            }
                        )
                    }            
  });
                            
  $('.media').click(function (element) {  
      localStorage.setItem("image_caller",$(this).attr('id')); //store who is calling this dialog 
      $.get(
                '{$mediaURL}',{search:'featured',exempt_the_headers_in_yumpee:'true'},
                function(data) {                    
                    $('#yumpee_media_content').html(data);
                    $('#myModal').modal();
                }    
            )
     ev.preventDefault(); 
  });
             
   $(document).on('click','#btnInsertMedia',
       function(ev){
       var myradio = $("input[name='my_images']:checked").val();   
       var my_radio_info = myradio.split("|");
       var img_src = my_radio_info[0];
       var my_id = my_radio_info[1];
       if(localStorage.image_caller=="set_feature"){
                $("#display_image_id").val(my_id);
                $("#my_display_image").attr("src","../../uploads/" + img_src);
                localStorage.removeItem("image_caller");
       }
       
                
       $('#myModal').modal('toggle');     
   });
 
 if($("#edit_roles").val()=="edit_roles"){
  $('#role_tab').trigger('click')        
 }
 $("#datalisting").DataTable();
EOT_JS
);
?>
<style>
    .modal-dialog{
    position: relative;
    display: table; /* This is important */ 
    overflow-y: auto;    
    overflow-x: auto;
    width: auto;
    min-width: 300px;   
}
    
</style>
<?php
$display_image_path="";
if(isset($rs->displayImage->path)):
    $display_image_path=$rs->displayImage->path;
endif;

?>
<div class="container-fluid">
    
        
        
    <div class="tab-content">
        <div id="users" class="tab-pane fade in active">

<div id="addUser">
     <form action="index.php?r=events/index" method="post" id="frm1">   
    <table class="table">
        <tr><td>Username<td><?=$rs['username']?>
        <tr><td>Password<td><input name="passwd" id="passwd" class="form-control" type="password" value="<?=$rs['password_hash']?>"/>
        <tr><td>First Name<td><input name="first_name" id="first_name" value="<?=$rs['first_name']?>" class="form-control"type="text" />
        <tr><td>Last Name<td><input name="last_name" value="<?=$rs['last_name']?>" id="last_name" class="form-control" type="text" />
        <tr><td>Title<td><input name="title" value="<?=$rs['title']?>" id="title" class="form-control" type="text" />  
         <tr><td>Email<td><input name="email" value="<?=$rs['email']?>" id="email" class="form-control" type="text" /> 
         <tr><td>My profile theme<td><?=\yii\helpers\Html::dropDownList("extension",$rs['extension'],['1'=>'Red-Light','2'=>'Yellow-Light','3'=>'Green-Light','4'=>'Blue-Light','5'=>'Purple-Light','6'=>'Black-Light','7'=>'Red','8'=>'Yellow','9'=>'Green','10'=>'Blue','11'=>'Purple','12'=>'Black'],['class'=>'form-control'])?>
        <tr><td>About<td><textarea name="about" id="about" class="form-control"><?=$rs['about']?></textarea>
        <tr><td>Feature Image<td><img id='my_display_image' src='<?=Yii::getAlias("@image_dir")?>/<?=$display_image_path?>' height='100px' align='top' width='200px' style='border:1px solid #233388' HSPACE='20' VSPACE='20'/> <a href='#' class='media' id='set_feature'>Set Feature Image</a><input type="hidden" name="display_image_id" id="display_image_id" value="<?=$rs['display_image_id']?>"/>
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <input type="hidden" name="processor" value="true" /><input type="hidden" name="id" value="<?=$id?>" /></td>
    </table>
    </form>
</div>



        </div>

</div>




<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Insert Media</h4>
      </div>
      <div class="modal-body">
          <div id="yumpee_media_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="btnInsertMedia">Insert Media</button> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

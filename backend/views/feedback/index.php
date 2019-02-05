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

$this->title='Contact';
$saveURL = \Yii::$app->getUrlManager()->createUrl('feedback/save');
$contactURL = \Yii::$app->getUrlManager()->createUrl('feedback/details');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('feedback/delete');

$this->registerJs( <<< EOT_JS
       
 
  $(document).on('click', '#btnSubmit',
       function(ev) {   
        
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
                location.href='?r=subscriptions/index';
            }
        )
        ev.preventDefault();
  });
        
             
 
 $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=subscriptions/index';     
        
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
   
                            
$('.contact_event').click(function(element){
         var id = $(this).attr('id');
         $.get(
            '{$contactURL}',{id:id},
            function(data) {
            
                $("#contact_details").html(data);
                $('#myModal').modal('toggle');
            }
        )
        ev.preventDefault();                   
                            
                            
});
                            
                            
if($("#cat_id").val()!=""){
  $('#bl_tab').trigger('click')        
 }
$("#datalisting").DataTable();                            
EOT_JS
);  
?>



<div class="container-fluid">


<div id="home" class="tab-pane fade in active">
     


<div class="box">
<div class="box-body">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Date<th>Name<th>Email<th>Actions</thead>
    <tbody>
      <?php
      foreach ($records as $user) :
          $name="";
          $email="";
          $website="";
          $read_stat="<font>";
          foreach($user->details as $details):
            if($details['param']=="name"):
                $name = $details['param_val'];
            endif;
            if($details['param']=="email"):
                $email = $details['param_val'];
            endif;
          endforeach;
          if($user['status']=="N"):
              $read_stat="<font color='green'>";
          endif;
      ?>
        <tr id="tr<?=$user['id']?>"><td><?=$read_stat?><?=$user['date_submitted']?></font><td><?=$read_stat?><?=$name?></font></td><td><?=$read_stat?><?=$email?></font><td><a href='#' title="Details" class="contact_event" id="<?=$user["id"]?>"><small><i class="glyphicon glyphicon-file"></i></small></a> <a href='#' class='delete_event' id='<?=$user['id']?>' event_name='<?=$name?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
     <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>
</div>
    

</div>


<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Contact Details</h4>
      </div>
      <div class="modal-body" id="contact_details">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


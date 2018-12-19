<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use backend\components\ACDWidget;

$this->title="Pay Type";

$saveURL = \Yii::$app->getUrlManager()->createUrl('pay/save');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('pay/delete');
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
        location.href='?r=pay/index';
        
        
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
        <tr><td>Pay Type<td><input name="name" id="name" value="<?=$name?>" class="form-control" type="text" />
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="processor" value="true" /><input type="hidden" name="id" value="<?=$id?>" />
            
            </td>
        
        
    </table>
    </form>
</div>


<div class="box">
<div class="box-body">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Name<th>Actions</thead>
    <tbody>
      <?php
      foreach ($records as $user) :
      ?>
        <tr><td><?=$user['name']?></td><td><td><a href='?actions=edit&id=<?=$user['id']?>&r=skills/extensions'><small><i class="glyphicon glyphicon-user"></i></small></a> <a href='?actions=edit&id=<?=$user['id']?>&r=pay/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$user['id']?>' event_name='<?=$user['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
     <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>
</div>
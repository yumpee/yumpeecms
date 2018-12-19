<?php
/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
$this->title='Relationship Details';
$saveURL = \Yii::$app->getUrlManager()->createUrl('relationships/save-details');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('relationships/delete-details');

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
$(document).on('click','#btnNew',
            function(ev){
                location.href='?actions=edit&relations_id={$relations_id}&r=relationships/configure'
            
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
<p><h3><?=$details['title']?></h3>
<div class="container-fluid">
    <p align="right"><a href="?r=relationships/index">Relationship List</a>
    <form method="post" id="frm1">
        <table class="table">
            <tr><td width="30%">Source field <b>(<?=$details['source']['title']?>)</b><td><input type="text" class="form-control" name="source_field" value="<?=$settings['source_field']?>"/>
            <tr><td>Target field <b>(<?=$details['target']['title']?>)</b><td><input type="text" class="form-control" name="target_field" value="<?=$settings['target_field']?>"/>  
            <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="relations_id" value="<?=$relations_id?>" /><input type="hidden" name="id" value="<?=$id?>" />
        </table>
        
    </form>   
    
</div>

<div class="box">
<div class="box-body">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Source Field<th>Target Field<th>Actions</thead>
    <tbody>
      <?php
      foreach ($records as $relate) :
      ?>
        <tr><td><?=$relate['source_field']?></td><td><?=$relate['target_field']?><td><a href='?actions=edit_settings&id=<?=$relate['id']?>&relations_id=<?=$relations_id?>&r=relationships/configure'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$relate['id']?>' event_name='<?=$relate['source_field']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
     <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>
</div>

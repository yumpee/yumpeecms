<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
use backend\components\DBComponent;

$this->title="View Form Data";
$deleteURL = \Yii::$app->getUrlManager()->createUrl('forms/delete-form-submit');
$addRecordURL = \Yii::$app->getUrlManager()->createUrl('forms/post');
$form_id=Yii::$app->request->get("id");
$this->registerJs( <<< EOT_JS

        
$('.delete_event').click(function (element) {    
                    
                    var id = $(this).attr('id');
        
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete this record ')){
                        $.get(  
                            '{$deleteURL}',{id:id},
                            function(data) {
                                alert(data);
                                $("#row" + id).remove();
                            }
                        )
                    }  
    
  });

$(document).on('click', '#btnSubmit',
       function(ev) {   
        location.href='?r=forms/post&form_id={$form_id}';
        $.post(
            '{$addRecordURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);                
            }
        )
        ev.preventDefault();
  });
$("#datalisting").DataTable();
EOT_JS
);  
?>

<div class="box">
<div class="box-body">
    <h3><?=$related['title']?> record(s)</h3>
    <p align="right"><form action="?r=forms/post" method="get"><p align="right"><input type="button" value="Add Record" class="btn btn-primary" id="btnSubmit"/><input type="hidden" name="form_id" value="<?=Yii::$app->request->get("id")?>"></form>
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr>
       <?php
      $field=[];
      $return_eval=[];
      $return_widget=[];
      $counter=0;
      foreach ($header as $record_header) :    
          echo "<th>".$record_header['view_label']."</th>";
          $field[$counter]=$record_header['field_name'];
          $return_eval[$counter]=$record_header['return_eval'];
          $return_widget[$counter]=$record_header['return_widget'];
          $counter++;
      endforeach;      
      ?>
            <th>Username<th>Date Submitted<th>Actions
    </thead>
    
    <tbody>
      <?php
      foreach ($records as $user) :          
      ?>
        <tr id="row<?=$user['id']?>">
        <?php
            $counter=0;
            foreach($user['backendData'] as $data):
            if($data['param']<>$field[$counter]):
                if($return_eval[$counter]!=""):
                    echo "<td>".DBComponent::parseRecord($return_eval[$counter],$user);
                elseif($return_widget[$counter]!=""):
                    echo "<td>".DBComponent::parseWidget($return_widget[$counter],$user);
                else:
                    echo "<td>";
                endif;
                $counter++;
            endif;
            
        ?>
        <td><?=DBComponent::parseData($data,Yii::$app->request->get("id"))?>
        <?php  
        $counter++;
            endforeach;
        ?>
        <td><?=$user['usrname']?></td><td><?=$user['date_stamp']?><td><a href='?actions=edit&id=<?=$user['id']?>&r=forms/details' title="View Details"><small><i class="fa fa-info"></i></small></a> <a href='?actions=edit&id=<?=$user['id']?>&r=forms/edit-details'><small><i class="glyphicon glyphicon-pencil"></i></small></a>  <a href='#' class='delete_event' id='<?=$user['id']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  
        <?php
        $found=0;
        $links="";
        foreach($related['relatedForms'] as $data):
            $found++;
        if($found > 1):
            $links.=" | ";
        endif;
            $links.="<a href='?actions=edit&id=".$data['id']."&r=forms/data&owner=".$user['id']."&source=".$related['name']."&target=".$data['name']."'>".$data['title']."</a> ";
        endforeach;
        if($found > 0):
            echo "<br>".$links;
        endif;
        ?>
        
        </td>
     <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>
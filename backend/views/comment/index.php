<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
$deleteURL = \Yii::$app->getUrlManager()->createUrl('comment/delete');
$approveURL = \Yii::$app->getUrlManager()->createUrl('comment/approve');
$this->registerJs( <<< EOT_JS
$('.delete_event').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete this comment ')){
                        $.get(  
                            '{$deleteURL}',{id:id},
                            function(data) {
                                alert(data);
                                location.href='?r=comment/index';
                            }
                        )
                    }            
  });    
$('.enable_event').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to enable this comment ')){
                        $.get(  
                            '{$approveURL}',{id:id},
                            function(data) {
                                alert(data);
                                location.href='?r=comment/index';
                            }
                        )
                    }            
  }); 
$("#datalisting").DataTable();   
                            
EOT_JS
);
?>
<div class="container-fluid">
<div class="box">
<div class="box-body">
    <table id="datalisting" class="table table-bordered table-striped"><thead><tr><th>Author</th><th>Comment</th><th>Related to</th><th>Date</th><th>IP Address</th><th>Actions</th></thead>
        <tbody>
<?php
foreach ($records as $rec):
    if($rec['author']!=null):
            $author=$rec['author'];
        else:
            $author=$rec['commentor'];
    endif;
    if($rec['status']=='N'):
?>
    <tr><td><?=$author?><td><?=$rec['comment']?></td><td><?=($rec->article ?$rec->article->title:"")?></td><td><?=$rec['date_commented']?></td><td><?=$rec['ip_address']?></td><td> <a href='#' class='enable_event' id='<?=$rec['id']?>' event_name='<?=$rec['id']?>' title="Approve"><small><i class="glyphicon glyphicon-remove"></i></small></a> <a href='#' class='delete_event' id='<?=$rec['id']?>' event_name='<?=$rec['id']?>' title="Delete"><small><i class="glyphicon glyphicon-trash"></i></small></a></td>
<?php
    else:
?>
    <tr><td><?=$author?><td><?=$rec['comment']?></td><td><?=($rec->article ?$rec->article->title:"")?></td><td><?=$rec['date_commented']?></td><td><?=$rec['ip_address']?></td><td> <a href='#' class='enable_event' id='<?=$rec['id']?>' event_name='<?=$rec['id']?>' title="Disapprove"><small><i class="glyphicon glyphicon-ok"></i></small></a> <a href='#' class='delete_event' id='<?=$rec['id']?>' event_name='<?=$rec['id']?>' title="Delete"><small><i class="glyphicon glyphicon-trash"></i></small></a></td>
<?php
    endif;
endforeach;
?>
        </tbody>
</table>
</div>
</div>
</div>

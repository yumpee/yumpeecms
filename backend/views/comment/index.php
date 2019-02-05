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
    <p align="right"><a href='?r=articles/index'>Go to Articles</a>
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

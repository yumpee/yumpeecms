<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
?>
<div class="container-fluid">
    <div class="box">
<div class="box-body">
    <table id="datalisting" class="table table-bordered table-hover"><thead><tr><th>Alias</th><th>Name</th><th>Description<th>Action</thead>
        <tbody>
<?php
    foreach($records as $record):
                
?>
    <tr><td><?=$record['alias']?></td><td><?=$record['name']?><td><?=$record['description']?></td><td><a href='?actions=edit&id=<?=$record['id']?>&r=reports/setup'><small><i class="glyphicon glyphicon-eye-open"></i></small></a> <a href='#' class='delete_event' id='<?=$record['id']?>' event_name='<?=$record['name']?>'><small><i class="glyphicon glyphicon-export"></i></small></a> </td>
        
 <?php
        endforeach;
?>
        </tbody>
</table>
</div>
</div>
</div>

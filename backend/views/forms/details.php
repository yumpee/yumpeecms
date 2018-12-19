<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
use backend\components\DBComponent;
$this->registerJs( <<< EOT_JS
        
$("#datalisting").DataTable(); 
EOT_JS
);
?>
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Field<th>Value
<?php
foreach($records as $rec):
    echo "<tr><td>".DBComponent::parseField($rec,$info['form_id'])."<td>".DBComponent::parseData($rec,$info['form_id']);
endforeach;

?>
    
    
</table>

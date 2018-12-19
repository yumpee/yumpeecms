<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
?>
<div class="container">
<table border='1' width="100%">
    
<?php
$counter=0;
foreach ($records as $rec):
    $counter++;
        echo "<tr><td><b>Record ".$counter."</b>";
        foreach ($rec['data'] as $details):            
            echo "<tr><td>".$details['param']."<td>".$details['param_val'];
        endforeach;
        echo "<br><a href='".$rec['formattedIndexURL']."/".$rec['url']."'>Got to Page</a>";
endforeach;
?>
</table>
</div>
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

        echo "<tr><td>Record";
        foreach ($records['data'] as $details):            
            echo "<tr><td>".$details['param']."<td>".$details['param_val'];
        endforeach;
        

?>
</table>
</div>
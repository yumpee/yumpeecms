<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
?>
<p>
<div class="row">
<?php
$counter=0;
foreach($gallery as $rec):
    $counter++;
    if($counter > 2):
        $counter=0;
        echo "<div class=\"row\"><p></div>";
    endif;
?>

    <div class="col-md-6"><img src="<?=$rec['uploadURL']."/".$rec['image_id']?>" height="200px"></img></div>
<?php

endforeach;
?>

</div>
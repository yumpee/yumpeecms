<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

?>
<h4>Tags</h4>
<?php
foreach ($tag_object as $tag):
?>
<a href="<?=$baseURL?>/<?=$tag->url?>"><span class="label label-default"><?=$tag->name?></span></a> 
<?php
endforeach;
?>
<br />
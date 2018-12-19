<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

?>
<div class="categoriesBox boxshadow">
                                <h4 class="BoxshadowTitle"> <?=$title?></h4>
<p>
<?php
foreach ($tag_object as $tag):
?>
<a href="<?=$baseURL?>/<?=$tag->url?>"><span class="label label-default"><?=$tag->name?></span></a> 
<?php
endforeach;
?>
</div>

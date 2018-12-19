<?php
/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 * This widget is used to display the languages that are in the system
 */
?>
<select id="yumpee_language">
<?php
foreach($language as $lang):
?>
<option value="<?=$lang->code?>"><?=$lang->name?></option>
<?php endforeach; ?>
</select>

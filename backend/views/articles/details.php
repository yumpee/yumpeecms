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
$saveProfileURL = \Yii::$app->getUrlManager()->createUrl('articles/save-profile-details');
?>
<style>
    .modal-dialog{
        z-index:999;
    }
</style>

<h4>Details of  <b><?=$article->title?> </b></h4>
<form id="frmDetails">
    
    <table border='1' width='100%'>
    <?php
    
    foreach($records as $record):
        if($record->param=="_csrf-frontend"):
            continue;
        endif;
        if(strlen($record->param_val) > 60):
            echo "<tr><td>".$record->param."<td><textarea rows='5' name='".$record->param."' class='form-control'>".$record->param_val."</textarea>";
        else:
            echo "<tr><td>".$record->param."<td><input type='text' name='".$record->param."' class='form-control' value=\"".$record->param_val."\" />";
        endif;
        
    endforeach;
    ?>
    </table><br>
    <?php
    if(count($records) > 0):
    ?> 
    <button class="btn btn-success" type="button" id="btnDetailsUpdate">Update</button>
    <input type='hidden' name='article_id' value='<?=$article->id?>' />
    <?php
    endif;
    ?>
</form>
<script>
  $("#btnDetailsUpdate").click(function(){    
  $.post(
            '<?=$saveProfileURL?>',$( "#frmDetails" ).serialize(),
            function(data) {
                alert(data);                
            }
        )
  })  
</script>

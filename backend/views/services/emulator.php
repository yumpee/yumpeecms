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
$saveURL = \Yii::$app->getUrlManager()->createUrl('services/emulator');

$this->registerJs( <<< EOT_JS
       $(document).on('click', '#btnSubmit',
       function(ev) {  
        
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                
            $("#result").val(data);
            $("#result_id").css("display","block");
            
            }
        )
        ev.preventDefault();
  }); 
  
  $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=services/emulator';
  }); 
            
EOT_JS
);  
?>

<div class="container-fluid">
<div id="addCategories">
     <form action="index.php?r=services/emulator" method="post" name="frm1" id="frm1">
    <table class="table">
        <tr><td>Host URL<td><input name="url" id="url" value="" class="form-control" type="text" />
        <tr><td>Client ID<td><input name="client_id" id="client_id" value="" class="form-control" type="text" />
        <tr><td>Client Key<td><input name="client_key" id="client_key" value="" class="form-control" type="text" />
        <tr><td>Authentication Type<td><select name="authentication" class="form-control"><option value="0">None</option><option value="Basic">Basic</option></select><br>
                <input type="checkbox" name="use_passwd"> Do not encrypt user / password
        <tr><td>Submission Type<td><select name="ptype" class="form-control"><option>POST</option><option>GET</option></select>
        <tr><td>Format Type<td><select name="format_type" class="form-control"><option value='json'>JSON</option><option value='plain'>Plain</option></select>
        <tr><td>Header<td><textarea class="form-control" name="header"></textarea>       
        <tr><td>Body<td><textarea class="form-control" name="body" rows="8" ></textarea>
        <tr><td><td style="display:none" id="result_id"><b>Result</b><br><textarea class="form-control" id="result" rows="8" readonly></textarea>
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Test</button> <button type="reset" id="btnNew" class="btn btn-primary">Clear</button> <input type="hidden" name="processor" value="true" /><input type="hidden" name="id" value="" />
            
            </td>
        
        
    </table>
    </form>
</div>
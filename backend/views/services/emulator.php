<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
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
        <tr><td>Authentication Type<td><select name="authentication" class="form-control"><option value="0">None</option><option value="Basic">Basic</option></select>
        <tr><td>Submission Type<td><select name="ptype" class="form-control"><option>POST</option><option>GET</option></select>
        <tr><td>Header<td><textarea class="form-control" name="header"></textarea>       
        <tr><td>Body<td><textarea class="form-control" name="body" rows="8" ></textarea>
        <tr><td><td style="display:none" id="result_id"><b>Result</b><br><textarea class="form-control" id="result" rows="8" readonly></textarea>
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Test</button> <button type="reset" id="btnNew" class="btn btn-primary">Clear</button> <input type="hidden" name="processor" value="true" /><input type="hidden" name="id" value="" />
            
            </td>
        
        
    </table>
    </form>
</div>
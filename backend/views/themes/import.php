<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
$this->title="Import Twig Programs";
$fetchURL = \Yii::$app->getUrlManager()->createUrl('themes/twig');
$saveURL = \Yii::$app->getUrlManager()->createUrl('themes/save-import');
$this->registerJs( <<< EOT_JS
      
$("#source_theme").change(function(){
    $.get(
            '{$fetchURL}',{source:$("#source_theme").val()},
            function(data) {
                var twigs = JSON.parse(data);
                var html="";
                for (var key in twigs) {
                if (twigs.hasOwnProperty(key)) {
                    //alert(key + " -> " + twigs[key]["renderer"]);
                    html = html + "<input type='checkbox' name='c" + twigs[key]["id"] + "'> " + twigs[key]["renderer"] + "<br />";
                }
                }
                $("#source_twig").html(html);
            }
        )
});
            
$(document).on('click', '#btnSubmit',
       function(ev) {   
        if(!confirm("Are you sure you want to import programs into another theme. This will overwrite programs with the same name in the target theme if they exist")){
            return;
        }
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);                
            }
        )
        ev.preventDefault();
  }); 
        
EOT_JS
);       
?>

<div class="container-fluid">
<form id="frm1">
    <table class="table">
        <tr><td valign='top'>Select Source theme <td><?=$source_theme?><div id='source_twig'></div></td>
        <tr><td>Select Target theme <td><?=$target_theme?> </td>
        
        <tr><td colspan="2"><input type="button" class="btn btn-primary" value="Import Twig" id="btnSubmit"/>
        
    </table>
    
    
</form>
</div>
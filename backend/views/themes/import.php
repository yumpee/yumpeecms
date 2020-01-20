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
                var counter=0;
                var curr_renderer="";
                var cclass="";
                for (var key in twigs) {
                if (twigs.hasOwnProperty(key)) {
                    //alert(key + " -> " + twigs[key]["renderer"]);
                    if(curr_renderer!=twigs[key]["renderer_type"]){
                        counter=0;
                    }
                    if(counter==0){
                        if(twigs[key]["renderer_type"]=="V"){
                            html = html + "<br><b>View Templates</b> <a href='#' onClick=\"javascript:check('cview')\">Select All</a><br>";
                            cclass="cview";
                        }
                        if(twigs[key]["renderer_type"]=="W"){
                            html = html + "<br><b>Standard Widget</b> <a href='#' onClick=\"javascript:check('cstandard')\">Select All</a><br>";
                            cclass="cstandard";
                        }
                        if(twigs[key]["renderer_type"]=="I"){
                            html = html + "<br><b>Form Custom Widget</b> <a href='#' onClick=\"javascript:check('ccustom')\">Select All</a><br>";
                            cclass="ccustom";
         
                        }
                        if(twigs[key]["renderer_type"]=="F"){
                            html = html + "<br><b>Form Post</b> <a href='#' onClick=\"javascript:check('cpost')\"'>Select All</a><br>";
                            cclass="cpost";
                        }
                        if(twigs[key]["renderer_type"]=="R"){
                            html = html + "<br><b>Form Summary View</b> <a href='#' onClick=\"javascript:check('csummary')\">Select All</a><br>";
                            cclass="csummary";
                        }
                        if(twigs[key]["renderer_type"]=="Z"){
                            html = html + "<br><b>Form Details View</b> <a href='#' onClick=\"javascript:check('cdetails')\">Select All</a><br>";
                            cclass="cdetails";
                        }   
                    }
                    curr_renderer=twigs[key]["renderer_type"];
                    html = html + "<input type='checkbox' name='c" + twigs[key]["id"] + "' class='" + cclass + "'> " + twigs[key]["renderer"] + " <br />";
                    counter++;
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

<script>
function check(view){
    $("." + view).each(function(){
        $(this).attr("checked","checked");
    })
    
}    
</script>
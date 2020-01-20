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

$fetchURL = \Yii::$app->getUrlManager()->createUrl('themes/twig');
$generateURL = \Yii::$app->getUrlManager()->createUrl('package/generate');

$this->registerJs( <<< EOT_JS
  
$(document).on('click', '#btnGenerate',
       function(ev) {  
        
        $.post(
            '{$generateURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);                
            }
        )
        ev.preventDefault();
  }); 
        
$("#btnStep1").click(function(){
       $("#step_1").css("display","none");
       $("#step_2").css("display","block");
});
        
$("#btnStep2Previous").click(function(){
       $("#step_2").css("display","none");
       $("#step_1").css("display","block"); 
});
   
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
                    counter++;
                    if(twigs[key]["renderer_type"]=="F"){
                        html = html + "<input type='checkbox' name='c" + twigs[key]["id"] + "' class='" + cclass + "'> " + twigs[key]["form"]["title"] + " <br />";
                        continue;
                    }
                    if((twigs[key]["renderer_type"]=="R")||(twigs[key]["renderer_type"]=="Z")){
                        html = html + "<input type='checkbox' name='c" + twigs[key]["id"] + "' class='" + cclass + "'> " + twigs[key]["page"]["title"] + " <br />";
                        continue;
                    }
                    if(twigs[key]["renderer_type"]=="V"){
                        html = html + "<input type='checkbox' name='c" + twigs[key]["id"] + "' class='" + cclass + "'> " + twigs[key]["templates"]["name"] + " <br />";
                        continue;
                    }
                    
                    html = html + "<input type='checkbox' name='c" + twigs[key]["id"] + "' class='" + cclass + "'> " + twigs[key]["renderer"] + " <br />";
                    
                    
                }
                }
                $("#install_packages").html(html);
            }
        )
});        
        
EOT_JS
);        
?>

<div class="container">
    <form id="frm1">
    <div id="step_1">
    <div class="row">
        <h4>Package Information</h4><hr>
        <label>Package Type</label>
        <select class="form-control" name="package_type"><option>Plugin</option><option>Extension</option><option>Theme</option></select>
    </div>
    <div class="row"><br>
        <label>Package Name</label>
        <input type="text" class="form-control" name="package_name" />
    </div>
    <div class="row"><br>
        <label>Short Description</label>
        <input type="text" class="form-control" name="short_description" />
    </div>
    <div class="row"><br>
        <label>Full Description</label>
        <textarea class="form-control" rows="5" name="full_description"></textarea>
    </div>
    <div class="row"><br>
        <h4>Contact Information</h4><hr>
        <label>Author Name</label>
        <input type="text" class="form-control" name="author_name" />
    </div>
    <div class="row"><br>
        <label>Organisation</label>
        <input type="text" class="form-control" name="organization" />
    </div>
    <div class="row"><br>
        <label>Support Email</label>
        <input type="text" class="form-control" name="support_email" />
    </div>
    <div class="row"><br>
        <label>Support Phone</label>
        <input type="text" class="form-control" name="support_phone" />
    </div>
    <div class="row"><br>
        <label>Support Website</label>
        <input type="text" class="form-control" name="support_website" /><br>
        <p align="right"><button class="btn btn-success" id="btnStep1" type="button">Next</button>
    </div>
    </div>
    <div id="step_2" style="display:none">
        <div class="row">
        <h4>Select Theme</h4><hr>            
        <select class="form-control" name="source_theme" id="source_theme"><option>Select a theme</option>
        <?php
        foreach ($records as $user) :
         echo "<option value='$user->id'>$user->name</option>"   ;
         
        endforeach;
       ?>
        </select>
        </div>
        <div id="install_packages"></div> 
        <div class="row"><br>
            <h4>Settings File</h4><hr>
            <input type="checkbox" name="include_theme_setting">Include Theme Settings <input type="checkbox" name="include_custom_setting"> Include System Custom Settings
        </div>
        <div class="row"><br>
            <h4>Class Setup</h4><hr>
            <?php
        foreach ($classes as $item) :
         echo "<input type='checkbox' name='cl$item->id'> $item->alias<br>";
        endforeach;
       ?>
            
        </div>
    <div class="row"><br>
        <div class="pull-left"><button class="btn btn-primary" id="btnStep2Previous" type="button">Previous</button></div><div class="pull-right"><button class="btn btn-success" id="btnGenerate" type="button">Generate Package</button></div>
    </div>
        
        
    </div>
    </form>
</div>

<script>
function check(view){
    $("." + view).each(function(){
        $(this).attr("checked","checked");
    })
    
}    
</script>
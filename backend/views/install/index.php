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
$uploadURL = \Yii::$app->getUrlManager()->createUrl('install/upload-exten');
$installURL = \Yii::$app->getUrlManager()->createUrl('install/install');

$this->registerJs( <<< EOT_JS
 
     
        
        
$("#btnInstall").click(function(){
                        $.get(  
                            '{$installURL}',{id:$("#folders").val()},
                            function(data) {
                                $("#btnInstall").css("display","none");
                                alert(data);
                            }
                        )
        
        
        
});   
    
$("form#install-frm").submit(function(event){

  //disable the default form submission
  event.preventDefault();

  //grab all form data  
  
  var formData = new FormData();

// Attach file
formData.append('image', $('input[type=file]')[0].files[0]); 
  $.ajax({
    url: '{$uploadURL}',
    type: 'POST',
    data: formData,
    async: false,
    cache: false,
    contentType: false,
    processData: false,
    success: function (returndata) {
      alert("uploading");
      
      var str = JSON.parse(returndata);
      $("#folders").val(str.folders);
      $("#btnInstall").css("display","inline");
      $("#btnUpload").css("display","none");
      $("#install_info").html(JSON.stringify(str.properties) + "<br>");  
      $("#install_info").css("display","block");
    }
  });
 
  return false;
});         
EOT_JS
);         
?>       


<div class="container-fluid">
<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#home">Install from File</a></li>
  <li><a data-toggle="tab" href="#menu1">Browse Market Place</a></li>  
</ul>

<div class="tab-content">
  <div id="home" class="tab-pane fade in active">
      <form id="install-frm" enctype="multipart/form-data" method="post">
          Upload Installation File <input type="file" name="image" id="image"><br>
          <div style="display:none" id="install_info">
              
          </div>
          <button class="btn btn-success" type="submit" id="btnUpload">Upload</button> <button class="btn btn-primary" type="button" id="btnInstall" style="display:none">Install</button>
          <input type="hidden" id="folders" />
          <div id="result"></div>
      </form>
  </div>
  <div id="menu1" class="tab-pane fade">
    <iframe src="http://marketplace.yumpeecms.com/ads" width="100%" height="1200">
    
    
</iframe>
  </div>
  
</div>
</div>



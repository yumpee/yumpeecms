<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$reloadMedia = \Yii::$app->getUrlManager()->createUrl('media/featured-media');

use dosamigos\fileupload\FileUploadUI;
$this->title='Media';
$saveURL = \Yii::$app->getUrlManager()->createUrl('media/save');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('media/delete');

$image_home = Yii::getAlias('@image_dir');

$this->registerJs( <<< EOT_JS
       
       $(document).on('click', '#btnSubmit',
       function(ev) {   
        
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
            }
        )
        ev.preventDefault();
  }); 
       
           
  $('.media').click(function (element) {   
      var selected_list="";
      var id = $(this).attr('id'); 
      var counter=0;
      $(".media").each(function() {
        counter++;
        id="c" + counter;
        
        if(document.getElementById(id).checked){
            selected_list = selected_list + "{$image_home}/" + $(this).val() + " ";
        }
        
      });  
      
      document.cookie ="yumpee_image=" + selected_list;

                               
  });
EOT_JS
);  
?>



<div class="container-fluid">
<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#library">Select from library</a></li>
  <li><a data-toggle="tab" href="#media">Upload from your machine</a></li>
  
  
</ul>
  <div class="tab-content">
    <div id="media" class="tab-pane fade">
        <div><p>Click on the files you wish to upload to your library.</div>
        <p>
        <?= FileUploadUI::widget([
    'model' => $model,
    'attribute' => 'id',
    'url' => ['media/image-upload', 'id' => $id],
    'gallery' => false,
    'fieldOptions' => [
        'accept' => 'image/*'
    ],
    'clientOptions' => [
        'maxFileSize' => 2000000
    ],
    // ...
    'clientEvents' => [
        'fileuploaddone' => 'function(e, data) {
                                
                                
                            }',
        'fileuploadfail' => 'function(e, data) {
                                console.log(e);
                                console.log(data);
                            }',
        'fileuploadsubmit'=> 'function(e, data) {
                                 
                                var empty_flds = 0;
                                $(".required").each(function() {
                                if(!$.trim($(this).val())) {
                                    empty_flds++;
                                    
                                }    
                                });
                                if(empty_flds > 0){
                                    alert("All alt tags must be filled");
                                    return false;
                                }
                                var input = $("#imagename");
                                var alttag=$("#alttag");
                                data.formData = {imagename:input.val(),alttag:alttag.val()};
                               
                            }',
        'fileupload'=> 'function(e, data) {
                               
                            }'
    ],
]); ?>
        
        
    </div>

<div id="library" class="tab-pane fade in active">
<div class="box">
<div class="box-body">
<table id="datalisting" class="table table-bordered table-striped">
    <tbody id="full_image_gallery">
      <?php
      $counter=0;
      foreach($records as $user):
          $counter++;
      ?>
        <input type='radio' name='my_images' class="media" id='my_images' value='<?=$user['path']."|".$user['id']?>'><img src='<?=$image_home?>/<?=$user['path']?>' height='100px' align='top' width='100px' style='border:1px solid #233388' HSPACE='20' VSPACE='20'/> 
      <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>
</div>
</div>
</div>


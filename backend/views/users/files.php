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
$image_home = Yii::getAlias('@image_dir/');
$saveURL = \Yii::$app->getUrlManager()->createUrl('users/save-files');

      
?>
<h4>List of Files for <b><?=$account->first_name?> <?=$account->last_name?></b></h4>
<form id="frmUpload" enctype="multipart/form-data">
<p><input type="file" name="files[]" multiple> <button class="btn btn-primary" id="btnUploadFiles" type="submit">Upload</button>
    <input type="hidden" name="user_id" value="<?=Yii::$app->request->get("user")?>" />
</form>
<?php
$document_div="";
foreach($records as $document):
    $mime_type = mime_content_type(Yii::getAlias('@uploads/uploads/')."/".$document['file_path']);
    list($type_file,$extension) = explode("/",$mime_type);
    if($type_file=="image"):
        $document_div.="<div class='col-md-4' id='im".$document['file_path']."'><a target='_blank' href='".$image_home."/".$document['file_path']."'><img width='200px' height='200px' src='".$image_home."/".$document['file_path']."'></img></a><br>".$document['file_name']."<br> <a href='#' target='_blank' class='delete_attachment' id='".$document['file_path']."'>Delete</a></div>";
    else:
        $document_div.="<div class='col-md-4' id='im".$document['file_path']."'><p><a target='_blank' href='".$image_home."/".$document['file_path']."'><i class='fa fa-file fa-document fa-5x' aria-hidden='true'></i></a><br>".$document['file_name']."<br> <a href='#' target='_blank' class='delete_attachment' id='".$document['file_path']."'>Delete</a></div>";
    endif;
    //$document_listing.=$document['file_path']." ";
endforeach;
?>
<hr>
<?php
echo $document_div;
?>
<script>
 $("#frmUpload").submit(function(){
  event.preventDefault();
  
  var formData = new FormData($(this)[0]);
    $.ajax({
    url: '<?=$saveURL?>',
    type: 'POST',
    data: formData,
    async: false,
    cache: false,
    contentType: false,
    processData: false,
    success: function (returndata) {
      alert("Your form has been successfully saved");
      
    }
  });
 
  return false;
});   
</script>
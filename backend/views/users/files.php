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
?>
<h4>List of Files for <b><?=$account->first_name?> <?=$account->last_name?></b></h4>
<?php
foreach($records as $document):
    $mime_type = mime_content_type(Yii::getAlias('@uploads/uploads/')."/".$document['media_id']);
    list($type_file,$extension) = explode("/",$mime_type);
    if($type_file=="image"):
        $document_div.="<div class='col-md-2' id='im".$document['media_id']."'><a href='".$image_home."/".$document['media_id']."'><img width='100px' height='100px' src='".$image_home."/".$document['media_id']."'></img></a><br>".$document['details']['name']."<br> <a href='#' class='delete_attachment' id='".$document['media_id']."'>Delete</a></div>";
    else:
        $document_div.="<div class='col-md-2' id='im".$document['media_id']."'><p><a href='".$image_home."/".$document['media_id']."'><i class='fa fa-file fa-document' aria-hidden='true'></i></a><br>".$document['details']['name']."<br> <a href='#' class='delete_attachment' id='".$document['media_id']."'>Delete</a></div>";
    endif;
    $document_listing.=$document['media_id']." ";
endforeach;
?>
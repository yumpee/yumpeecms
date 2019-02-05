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

$this->title = 'Upload News Images';


use dosamigos\fileupload\FileUploadUI;
$event_id=Yii::$app->request->get('id');
// with UI
    $model = new backend\models\News();

?>

<?= FileUploadUI::widget([
    'model' => $model,
    'attribute' => 'image',
    'url' => ['events/image-upload', 'id' => $event_id],
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
                                console.log(e);
                                console.log(data);
                            }',
        'fileuploadfail' => 'function(e, data) {
                                console.log(e);
                                console.log(data);
                            }',
    ],
]); ?>

<div class="box">
<div class="box-body">
    <table id="datalisting" class="table table-bordered table-striped"><thead><tr><th>Display Image</td><th>Action</th>
        <tbody>
{{#image_list}}
    <tr><td><img src='/basic/images/{{image_id}}' width='50'></img><td><a href='?actions=edit&id={{id}}&r=events/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='{{id}}' event_name='{{name}}'><small><i class="glyphicon glyphicon-trash"></i></small></a> 
{{/image_list}}
        </tbody>
</table>
</div>
</div>
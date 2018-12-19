<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
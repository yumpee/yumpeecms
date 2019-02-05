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
$this->title = 'Translation';


$saveURL = \Yii::$app->getUrlManager()->createUrl('translation/save');
$saveCategoryURL = \Yii::$app->getUrlManager()->createUrl('translation/save-category');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('translation/delete');
$deleteCategoryURL = \Yii::$app->getUrlManager()->createUrl('translation/delete-category');

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
  $(document).on('click', '#btnCategorySubmit',
       function(ev) {   
        $.post(
            '{$saveCategoryURL}',$( "#frmCategory" ).serialize(),
            function(data) {
                alert(data);
            }
        )
        ev.preventDefault();
  }); 
            
  $('.delete_event').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete - ' + event_name)){
                        $.get(  
                            '{$deleteURL}',{id:id},
                            function(data) {
                                alert(data);
                            }
                        )
                    }            
  });
                            
$('.delete_list').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete category ' + event_name)){
                        $.get(  
                            '{$deleteCategoryURL}',{id:id},
                            function(data) {
                                alert(data);
                            }
                        )
                    }            
  });
                            
if($("#category_id").val()!=""){
  $('#bl_tab').trigger('click')        
 }
$("#datalisting").DataTable();                          
                            
EOT_JS
);  
?>



      
<div class="container-fluid">
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#translation_tab">Translation</a></li>
    <li><a data-toggle="tab" href="#cat_tab" id="bl_tab">Categories</a></li>
    
  </ul>
<div class="tab-content">
<div id="translation_tab" class="tab-pane fade in active">
    <div>
     <form action="index.php?r=events/index" method="post" id="frm1">
    <table class="table">
        <tr><td>Category<td><?=\yii\helpers\Html::dropDownList("category",'',$category,['class'=>'form-control','prompt'=>'Select Category'])?>
        <tr><td>Source Message<td><textarea name="message" id="message" rows="3" cols="30" class="form-control"><?=$rs['message']?></textarea> 
        <tr><td>Translated Language<td><?=\yii\helpers\Html::dropDownList("language",'',$language,['class'=>'form-control','prompt'=>'Select Target Languarge'])?>
        <tr><td>Translated Message<td><textarea name="translation" id="translation" rows="3" cols="30" class="form-control"><?=$translation['translation']?></textarea>  
        
        
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-primary">Save</button><input type="hidden" name="processor" value="true" /><input type="hidden" name="id" value="<?=$id?>" />
            
            </td>
        
        
    </table>
    </form>
    </div>



<div class="box">
<div class="box-body">
    <table id="datalisting" class="table table-bordered table-striped"><thead><tr><th>Category</th><th>Message</thead>
        <tbody>
<?php
    foreach($records as $record):
?>
    <tr><td><?=$record['category']?></td><td><?=$record['message']?></td><td><a href='?actions=edit&id=<?=$record['id']?>&r=translation/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$record['id']?>' event_name='<?=$record['id']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a></td>
<?php
    endforeach;
?>
        </tbody>
</table>
</div>
</div>
</div>
<div id="cat_tab" class="tab-pane fade in">
        <form action="index.php?r=language/index" method="post" name="frmCategory" id="frmCategory">
        <table class="table">
        <tr><td>Label<td><input name="alias" id="alias" value="<?=$rscat['alias']?>" class="form-control" type="text" />
        <tr><td>ID (no space)<td><input name="name" id="name" value="<?=$rscat['name']?>" class="form-control" type="text" />
        <tr><td>Description<td><textarea name="description" id="description" rows="2" cols="30" class="form-control"><?=$rscat['description']?></textarea>
        
        <tr><td colspan="2"><button type="submit" id="btnCategorySubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="processor" value="true" /><input type="hidden" id="category_id" name="category_id" value="<?=$rscat['id']?>" />
            
            </td>
        
        
    </table>
    </form>
    
    <table class="table table-bordered table-striped">
        <tr><th>Category Alias<th>Category Name<th>Action
        <?php
        foreach($category_list as $category):
        ?>
        <tr><td><?=$category['alias']?><td><?=$category['name']?><td><a href='?actions=edit&cat_id=<?=$category['id']?>&r=translation/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_list' id='<?=$category['id']?>' event_name='<?=$category['id']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>       
                
        <?php
        
        endforeach;
        
        
        ?>
        
    </table>
</div>
    
</div>
</div>
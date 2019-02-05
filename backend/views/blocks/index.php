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
$this->title = 'Blocks';


$saveURL = \Yii::$app->getUrlManager()->createUrl('blocks/save');
$saveGroupURL = \Yii::$app->getUrlManager()->createUrl('blocks/save-group');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('blocks/delete');

$this->registerJs( <<< EOT_JS
tinymce.init({ selector:'textarea',
           theme: 'modern',
        branding:false,
    width: 1000,
    height: 300,
    plugins: [
      'advlist autolink link lists charmap print preview hr anchor pagebreak spellchecker',
      'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
      'save table contextmenu directionality emoticons template paste textcolor yumpeemedia yumpeeslider'
    ],
    toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link print | preview media fullpage | forecolor backcolor emoticons yumpeemedia yumpeeslider' });
  
        
  $(document).on('click', '#btnSubmit',
       function(ev) {   
        $("#content").val(tinymce.get('content').getContent());
        
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
                location.href='?r=blocks/index';
            }
        )
        ev.preventDefault();
  }); 
            
  $(document).on('click', '#btnSubmitGroup',
       function(ev) {   
        
        
        $.post(
            '{$saveGroupURL}',$( "#frmGroup" ).serialize(),
            function(data) {
                alert(data);
                location.href='?r=blocks/index';
            }
        )
        ev.preventDefault();
  }); 
 
   $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=blocks/index';
        
        
  }); 
  
  $(document).on('click', '#btnNewGroup',
       function(ev) {   
        location.href='?r=blocks/index&block_group_id=0';
        
        
  });
            
  $('.delete_event').click(function (element) {                    
                    var id = $(this).attr('id');
                    var block = $(this).attr('block');
                    if(confirm('Are you sure you want to delete - ' + block)){
                        $.get(  
                            '{$deleteURL}',{id:id},
                            function(data) {
                                alert(data);
                            }
                        )
                    }            
  });
  
 if($("#block_group_id").val()!=""){
  $('#bl_tab').trigger('click')        
 }
   $("#datalisting").DataTable();                          
  
EOT_JS
);  

?>

<div class="container-fluid">
    
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home">Blocks</a></li>
    <li><a data-toggle="tab" href="#menu1" id="bl_tab">Block Groups</a></li>
    
  </ul>
    
<div class="tab-content">
    <div id="home" class="tab-pane fade in active"> <br />   
<div id="addBlock">
     <form action="index.php?r=blocks/index" method="post" id="frm1">
    <table class="table">
        <tr><td width="15%">Name<td><input name="name" id="name" value="<?=$rs['name']?>" class="form-control" type="text" />
        <tr><td>Title<td><input name="title" id="title" value="<?=$rs['title']?>" class="form-control" type="text" />        
        <tr><td>Content  <td><textarea name="content" id="content" class="form-control" rows="7" cols="40"><?=$rs['content']?></textarea>
        <tr><td><td><?=$editable?>User Editable  <?=$show_title?>Show Title <?=$published?>Published
        <tr><td>Title Level<td><?=$title_level?>
        <tr><td>Standard Position<td><?=$position?>
        <tr><td>Widget Position<td><?=$custom_position?>
        <tr><td>Sort Order<td><input name="sort_order" id="sort_order" value="<?=$rs['sort_order']?>" class="form-control" type="text" />
        <tr><td>Require Login to view<td><?=\yii\helpers\Html::dropDownList("require_login",$rs['require_login'],['N'=>'No','Y'=>'Yes'],['class'=>'form-control'])?></td>
        <tr><td>Role Permission<td><?=$permissions?>
        <tr><td>Linked Pages<td>  <?=$pages?>      
        <tr><td colspan="2"><button type="button" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="processor" value="true" /> <input type="hidden" name="id" value="<?=$id?>" /><input type="hidden" name="cont" id="cont"/></td>
        
        
    </table>
    </form>
</div>


<div class="box">
<div class="box-body">
    <table id="datalisting" class="table table-bordered table-striped"><thead><tr><th>Name</td><th>Position<th>Published<th>Global Content<th>User Editable<th>Sort Order<th>Actions</thead>
        <tbody>
        <?php
        foreach ($records as $record):
            if($record['published']):
                    $published="Yes";
                else:
                    $published="No";
                endif;
                if($record['editable']):
                    $editable="Yes";
                else:
                    $editable="No";
                endif;
                if($record['master_content']):
                    $master_content="Yes";
                else:
                    $master_content="No";
                endif;
            ?>
        <tr><td><?=$record['name']?></td><td><?=$record['position']?></td><td><?=$published?></td><td><?=$master_content?></td><td><?=$editable?><td><?=$record['sort_order']?><td><a href='?actions=edit&id=<?=$record['id']?>&r=blocks/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$record['id']?>' block='<?=$record['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>
        <?php
        endforeach;
        ?>
        </tbody>
</table>
</div>
</div>
</div>
    <div id="menu1" class="tab-pane fade">
        <form action="index.php?r=blocks/index" method="post" id="frmGroup">
    <table class="table">
        <tr><td>Name<td><input name="name" id="name" value="<?=$rsg['name']?>" class="form-control" type="text" />
        <tr><td>Blocks<td><?=$blocks?>   
        <tr><td colspan="2"><button type="button" id="btnSubmitGroup" class="btn btn-success">Save</button> <button type="button" id="btnNewGroup" class="btn btn-primary">New</button> <input type="hidden" name="processor" value="true" /> <input type="hidden" name="id" value="<?=$rsg['id']?>" /><input type="hidden" name="cont" id="cont"/></td>
        <input type="hidden" id="block_group_id" value="<?=$rsg['id']?>">
        
    </table>
    </form>
    <div class="box">
<div class="box-body">
    <table id="datalisting" class="table table-bordered table-striped"><thead><tr><th>Group Name</th><th>Actions</th></thead>
        <tbody>
        <?php
        foreach ($group_records as $record):
            
            ?>
        <tr><td><?=$record['name']?></td><td><a href='?actions=edit&group_id=<?=$record['id']?>&r=blocks/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$record['id']?>' block='<?=$record['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>
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

 <script src="https://cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>
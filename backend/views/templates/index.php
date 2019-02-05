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
$this->title = 'Templates';


$saveURL = \Yii::$app->getUrlManager()->createUrl('templates/save');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('templates/delete');
$deletePosition = \Yii::$app->getUrlManager()->createUrl('templates/delete-position');
$manageURL = \Yii::$app->getUrlManager()->createUrl('templates/manage');
$saveWidgetURL = \Yii::$app->getUrlManager()->createUrl('templates/save-widget');
$saveCustom = \Yii::$app->getUrlManager()->createUrl('templates/save-custom');
$saveWidgetPosition = \Yii::$app->getUrlManager()->createUrl('templates/save-position');


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
  $(document).on('click', '#btnAdd',
       function(ev) {   
        $.post(
            '{$saveCustom}',$( "#frmAdd" ).serialize(),
            function(data) {
                alert(data);
            }
        )
        ev.preventDefault();
       }); 
            
  $(document).on('click', '#btnSaveWidget',
       function(ev) {   
        $.post(
            '{$saveWidgetURL}',$( "#frmWidget" ).serialize(),
            function(data) {
                alert(data);
            }
        )
        ev.preventDefault();
       }); 
  $(document).on('click', '#btnSubmitPos',
       function(ev) {   
        $.post(
            '{$saveWidgetPosition}',$( "#frmWidgetPosition" ).serialize(),
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
 $('.delete_event_pos').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete - ' + event_name)){
                        $.get(  
                            '{$deletePosition}',{id:id},
                            function(data) {
                                alert(data);
                            }
                        )
                    }            
  });
 $('.widget_manager').click(function (element){
                   var id = $(this).attr('id');
                   var template_id = $(this).attr('template_id');
                   $.get(  
                            '{$manageURL}',{id:id,template_id:template_id},
                            function(data) {
                                $('#widget_loader').html(data);
                                $('#widget_loader').modal('show');
                            }
                        )
 });
                            
  if($("#position_id").val()!=""){
  $('#widget_position_tab').trigger('click')        
 }                          
 
EOT_JS
);  
?>
<?php
$url_readonly="";
if($rs['internal_route_stat']=="N"):
    $url_readonly="readonly";
endif;

$custom_name="";
    $pos = strpos($rsp['name'], "yumpee_pos_");
        if ($pos !== false) {
            $custom_name = substr_replace($rsp['name'], "", $pos, strlen("yumpee_pos_"));
        }
?>

      
<div class="container-fluid">
    
<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#manageTemplate">Manage Templates</a></li>
  <li><a data-toggle="tab" href="#addTemplate">Add Child Template</a></li>
  <li><a data-toggle="tab" href="#addPosition" id="widget_position_tab">Widget Position</a></li>
  
</ul>
<div class="tab-content"> 
<div id="manageTemplate" class="tab-pane fade in active">
<div id="addCategories">
     <form action="index.php?r=events/index" method="post" id="frm1">
    <table class="table">
        <tr><td>Name of template<td><input name="name" id="name" value="<?=$rs['name']?>" class="form-control" type="text" />
        <tr><td>Internal Route<td><input name="route" id="route" readonly value="<?=$rs['route']?>" class="form-control" type="text" />
        <tr><td>External URL<br>Only for system routes<td><input name="url" id="url" <?=$url_readonly?> value="<?=$rs['url']?>" class="form-control" type="text" />
        <tr><td>Manage Page's Sidebar Widgets<td>
                <table border='1' width='80%'>
                    <tr><th>Widget<th>Display Order <th>Enable on template<th>Position<th>Configure
                    <?php
                    foreach ($widget_details as $widget):
                        $checked="";
                        $selected="";
                        $display_order="";
                        foreach($selected_widget as $c):
                            if($c['widget']==$widget['short_name']):
                                $checked = " checked";
                                $selected = $c['position'];
                                $display_order = $c['display_order'];
                            endif;
                        endforeach;
                        $color="<font>";
                        if($widget['template_type']=="C"):
                            $color="<font color='blue'>";
                        endif;
                        $drop_down = \yii\helpers\Html::dropDownList("pos_".$widget['short_name'],$selected,array_merge(['side'=>'Sidebar','bottom'=>'Bottom'],$template_position));
                    ?>
                    <tr><td><?=$color?><?=$widget['name']?></font><td><input type='text' size='3' name='<?=$widget['short_name']?>' value="<?=$display_order?>"><td><input type="checkbox" name="chk<?=$widget['short_name']?>" <?=$checked?>/><td><?=$drop_down?></td><td><?php if($checked==" checked" && $widget['template_type']=="S"):?><a href='#' id="<?=$widget['short_name']?>" template_id="<?=Yii::$app->request->get('id')?>" class="widget_manager">Manage</a> <?php endif;?>
                    <?php
                    endforeach;
                    ?>
                    
                </table>
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-primary">Save</button><input type="hidden" name="processor" value="true" /><input type="hidden" name="id" value="<?=$id?>" />
            
            </td>
        
        
    </table>
    </form>
</div>


<div class="box">
<div class="box-body">
    <table class="table table-bordered table-hover"><tr><th>Name of template<th>Route<th>Parent<th>Actions
<?php
    foreach($records as $record):
        $parent="";
        if($record->parent!=null):
            $parent=$record->parent->name;
            
        endif;
?>
    <tr><td><?=$record['name']?></td><td><?=$record['route']?><td><?=$parent?><td><a href='?actions=edit&id=<?=$record['id']?>&r=templates/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$record['id']?>' event_name='<?=$record['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a></td>
<?php
    endforeach;
?>
</table>
</div>
</div>
</div>
    
<div id="addTemplate" class="tab-pane">
    <form id="frmAdd">
    <table class="table">
       <tr><td>Name of template<td><input name="name" id="name" value="<?=$rs['name']?>" class="form-control" type="text" />
       <tr><td>Parent Template<td><?=$custom_template?>
       <tr><td>Renderer Name(no-spaces)<td><input name="renderer" id="renderer" value="<?=$rs['renderer']?>" class="form-control" type="text" />
       <tr><td colspan="2"><button type="submit" id="btnAdd" class="btn btn-primary">Save</button>
    </table>
    </form>
        
</div>
<div id="addPosition" class="tab-pane">
        <form action="index.php?r=templates/index" method="post" id="frmWidgetPosition">
    <table class="table">
        <tr><td>Position Name<td><input name="title" id="title" value="<?=$rsp['title']?>" class="form-control" type="text" />
        <tr><td width="40%">Position ID(will be prefixed with 'yumpee_pos_')<td><input name="name" id="name" value="<?=$custom_name?>" class="form-control" type="text" />
        <tr><td>Description<td><textarea name="description" class="form-control"></textarea> 
        <tr><td colspan="2"><button type="button" id="btnSubmitPos" class="btn btn-success">Save</button> <button type="button" id="btnNewPosition" class="btn btn-primary">New</button> <input type="hidden" name="processor" value="true" /> <input type="hidden" name="id" value="<?=$rsp['id']?>" /><input type="hidden" name="cont" id="cont"/></td>
        <input type="hidden" name="position_id" id="position_id" value="<?=$rsp['id']?>">
        
    </table>
    </form>
    <div class="box">
    <div class="box-body">
    <table class="table table-bordered table-hover"><tr><th>Position Title<th>Position Name<th>Actions
<?php
    foreach($widget_position as $record):
        $parent="";
        
?>
    <tr><td><?=$record['title']?></td><td><?=$record['name']?><td><a href='?actions=edit&pid=<?=$record['id']?>&r=templates/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event_pos' id='<?=$record['id']?>' event_name='<?=$record['title']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a></td>
<?php
    endforeach;
?>
</table>
</div>
    </div>
    </div>
</div>
</div>
<div id="widget_loader" class="modal fade" role="dialog">
  
</div>

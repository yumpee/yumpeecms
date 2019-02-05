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

$this->title="Tag Types";
$saveURL = \Yii::$app->getUrlManager()->createUrl('tags/save-tag');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('tags/delete-tag');
$tagURL =  \Yii::$app->getUrlManager()->createUrl('tags/search-tags');
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
    
 $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=tags/types';
        
        
  }); 
                            
 //below is jquery for when the tag field is triggered
  $('#search_tag').on('input',function(e){            
            $.get(
                '{$tagURL}',{search:$("#search_tag").val()},
                function(data) {                    
                    $('#tag_list').find('option').remove().end().append(data);
                    $('#tag_list').css("display","block");
                }    
            )
        });
  
EOT_JS
);  
?>

<div class="container">
<button class="btn btn-info" data-toggle="collapse" data-target="#addTagType">Add Tag Type</button>
<div id="addTagType">
     <form action="index.php?r=events/index" method="post" id="frm1">
    <table class="table">
        <tr><td>Name<td><input name="name" id="name" value="<?=$name?>" class="form-control" type="text" />
        <tr class='addons'><td>Tag to display <td><input class="form-control"type="text" placeholder="Type tag" name="search_tag" id="search_tag" /> <select size=5 name=tag_list id=tag_list style="width:300px;display:none;height:100px" onChange="javascript:selectTag()" list='tag_listing'></select>
                <span id="selected_tag">
                    <?php
                    $tags_val="";
                    foreach ($selected_tags as $user):
                        $tags_val = $user['id']." ";
                        ?>
                    
                    <span id="<?=$user['id']?>"> <?=$user['name']?><a href='#' onClick="javascript:remTag('<?=$user['id']?>');return false">Remove</a><br /></span>
                    
                    <?php
                    endforeach;
                    ?>               </span>
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="processor" value="true" /><input type="hidden" name="id" value="<?=$id?>" />  <input type="hidden" name="tag_array" id="tag_array" value="<?=$tags_val?>" />          
            </td>
        
        
    </table>
    </form>
</div>

<div class="box">
<div class="box-body">
<table class="table table-bordered table-hover"><tr><th>Name</td>
<?php foreach ($records as $user) :?>
    <tr><td><?=$user['name']?></td><td><a href='?actions=edit&id=<?=$user['id']?>&r=tags/types'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$user['id']?>' event_name='<?=$user['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>
 <?php endforeach;?>
</table>
</div>
</div>
</div>
<script language='Javascript'>
    function selectTag(){  
        //this function is used update the drop down list of the tags when you select the tag from the drop down list
            $("#tag_list").css("display","none");
            $("#search_tag").val('');
            if($("#tag_array").val().indexOf($("#tag_list").val()) > 0){
                
            }else{
            $("#tag_array").val($("#tag_array").val() + ' ' + ($("#tag_list").val()));            
            $("#selected_tag").append("<span id='" + $("#tag_list").val() + "'>" + $('#tag_list :selected').text() + " <a href='#' onClick=\"javascript:remTag('" + $("#tag_list").val() + "');return false\">Remove</a><br></span>");
        }
            
        }
    function remTag(id){
    //This function is called when you hit on the Remove tag link. After the tag has been removed, it also removes the ID from the hidden tag array to be sent to the server
        $('#' + id).remove();        
        $("#tag_array").val($("#tag_array").val().replace(id,''));        
    }
</script>
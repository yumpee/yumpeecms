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
$deleteURL = \Yii::$app->getUrlManager()->createUrl('media/delete');
$editURL = \Yii::$app->getUrlManager()->createUrl('media/edit');
$searchURL = \Yii::$app->getUrlManager()->createUrl('media/search');
$saveURL = \Yii::$app->getUrlManager()->createUrl('media/save');
$home_image_url= \frontend\components\ContentBuilder::getSetting("website_image_url");
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
       $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=media/index';
        
        
  }); 
            
  $('.delete_event').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete - ' + event_name)){
                        $.get(  
                            '{$deleteURL}',{id:id},
                            function(data) {
                                alert(data);
                                var ob = "im" + id;
                                $("#" + ob).remove();
                            }
                        )
                    }            
  });
  $('.editImage').click(function (element) {                    
                    var id = $(this).attr('linkid');
                    $.get(  
                            '{$editURL}',{id:id},
                            function(data) {
                            record = JSON.parse(data)
                                $("#name").val(record['name']);
                                $("#alt_tag").val(record['alt_tag']);
                                $("#caption").val(record['caption']);
                                $("#description").val(record['description']);
                                $("#id").val(record['id']);
                                $("#size").val(record['size']);
                                $("#date").val(record['size']);
                                $("#url").val('{$home_image_url}/' + record['path']);
                                
                            }
                    )
                    
  });
                                
  $('.page_links').click(function(){
     var page = $(this).attr("page_no");
     $("#page_no").val(page);
     $("#btnSearch").click();
  });
  $('.detailsImage').click(function (element) {         
                    var id = $(this).attr('linkid');
                    $.get(  
                            '{$editURL}',{id:id},
                            function(data) {
                            record = JSON.parse(data)
                                $("#details_name").val(record['name']);
                                $("#details_alt_tag").val(record['alt_tag']);
                                $("#details_caption").val(record['caption']);
                                $("#details_description").val(record['description']);
                                $("#details_size").val(record['size']);
                                $("#details_uploaded").val(record['upload_date']);
                                $("#details_by").val(record['publisher']['first_name'] + " " + record['publisher']['last_name']);
                                $("#details_url").val('{$home_image_url}/' + record['path']);
                                
                            }
                    )
                    
  });
  
                            
 if($("#name").val()!=""){
  $('#vlibrary').trigger('click')        
 }
           

$("#datalisting").DataTable();                            
EOT_JS
);  
?>


<?php
$home_image_url= \frontend\components\ContentBuilder::getSetting("website_image_url");

      $row_count=0;
      foreach ($records as $user) :          
          $file_type="";
          if($user['media_type']=='1'):
              $file_type="Image";
          endif;
          if($user['media_type']=='2'):
              $file_type="Video";
          endif;
          if($user['media_type']=='3'):
              $file_type="Audio";
          endif;
          if($user['media_type']=='4'):
              $file_type="Document";
          endif;
          if($user['media_type']=='5'):
              $file_type="Application";
          endif;
          if($user['media_type']=='6'):
              $file_type="Others";
          endif;
          
      ?>
        <div class="col-md-3 col-xs-3 images" id="im<?=$user['id']?>">
            <span class="border border-primary">
                <?php
                if($file_type=="Video"):
                ?>
                <a href="<?=$home_image_url?>/<?=$user['path']?>" target="_blank"><video height="200px" width="100%" poster="https://peach.blender.org/wp-content/uploads/title_anouncement.jpg?x11217"></video></a>
                <?php
                else:
                ?>
                    <img src="<?=$home_image_url?>/<?=$user['path']?>" height="200px" width="100%" class="rounded"></img>
                <?php
                
                endif;
                ?>
                <br>Name :<?=$user['name']?><br>Tag:<?=$user['alt_tag']?><br>Type:<?=$file_type?>
                <br><br><a href='#' data-toggle="modal" data-target="#detailsModal" class="detailsImage" linkid="<?=$user['id']?>"><i class="fa fa-info-circle" aria-hidden="true"></i> Details </a> |<a href='#' data-toggle="modal" data-target="#myModal" class="editImage" linkid="<?=$user['id']?>"> <i class="fa fa-pencil"></i> Edit </a> | <a href='#' class='delete_event' id='<?=$user['id']?>' event_name='<?=$user['name']?>'> <i class="fa fa-trash"></i> Delete</a> | 
                <a href='#' data-toggle="popover" data-placement="bottom" data-content="<a href='#' onClick='mergecase();' data-toggle='modal' data-target='#mergeModal'><i class='ion-merge'></i> Resize</a><br>
<a href='#' onClick='linkcase()' data-toggle='modal' data-target='#linkModal'><i class='fa fa-link'></i> Rotate</a><br>
<a href='#' onClick='reAssignCategory()' data-toggle='modal' data-target='#assignCategoryModal'><i class='fa fa-save'></i> Save as New</a>
<hr>
<a href='#' onClick='childcase();'><i class='fa fa-child'></i> Add to gallery</a>"> 
<i class="fa fa-tasks"></i> More Actions <i class="fa fa-caret-down"></i></a></span></div>
        <?php
        $row_count++;
        if($row_count >3):
            $row_count=0;
            //echo "<div class='col-xs-12' style='height:50px;'></div>";
        echo "<div class='col-md-12'>&nbsp;</div>";
        endif;
        
        endforeach;
        ?>
<?php
$no_of_pages = ceil($total_count /$page_count);
?>

<div class="row">  
    <div class="col-md-12">
    <ul class="pagination">
     <?php
     for($i=1;$i<=$no_of_pages;$i++):  
         if($i==$page_no):
             echo '<li class="active"><a href="#" class="page_links" page_no="'.$i.'">'.$i.'</a></li>';
         else:
     ?>
            <li><a href="#" class="page_links" page_no="<?=$i?>"><?=$i?></a></li>
    <?php
        endif;
    endfor;
    ?>
    </ul> 
    </div>
</div>
<script>
$(document).ready(function(){
    $('[data-toggle="popover"]').popover({html:"true"});   
});
</script>

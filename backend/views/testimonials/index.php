<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$this->title='Testimonials';
$saveURL = \Yii::$app->getUrlManager()->createUrl('testimonials/save');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('testimonials/delete');
$this->registerJs( <<< EOT_JS
       tinymce.init({ selector:'textarea',
           theme: 'modern',
        branding:false,
    width: 1000,
    height: 300,
    plugins: [
      'advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker',
      'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
      'save table contextmenu directionality emoticons template paste textcolor yumpeemedia'
    ],
    toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons yumpeemedia' });
 
       $(document).on('click', '#btnSubmit',
       function(ev) {   
        $("#content").val(tinymce.get('content').getContent());
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
                location.href='?r=testimonials/index';
            }
        )
        ev.preventDefault();
  }); 
       $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=testimonials/index';
        
        
  }); 
            
  $('.delete_event').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete - ' + event_name)){
                        $.get(  
                            '{$deleteURL}',{id:id},
                            function(data) {
                                alert(data);
                                var ids = "tr" + id;
                                $("#" + ids).remove();
                            }
                        )
                    }            
  });
$("#datalisting").DataTable(); 
EOT_JS
);  
?>



<div class="container-fluid">
<button class="btn btn-info" data-toggle="collapse" data-target="#addCategories">Toggle View</button>
<div id="addCategories">
     <form action="index.php?r=testimonials/index" method="post" id="frm1">
    <table class="table">
        <tr><td>Testimonial Title<td><input name="name" id="name" value="<?=$rs['name']?>" class="form-control" type="text" />
        <tr><td>Company<td><input name="company" id="company" value="<?=$rs['company']?>" class="form-control" type="text" />
        <tr><td>Author<td><input name="author" id="author" value="<?=$rs['author']?>" class="form-control" type="text" />
        <tr><td>Author Position<td><input name="author_position" id="author_position" value="<?=$rs['author_position']?>" class="form-control" type="text" />
        <tr><td>Content<td><textarea name="content" id="content"><?=$rs['content']?></textarea>
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="id" value="<?=$id?>" />
            
            </td>
        
        
    </table>
    </form>
</div>

<div class="box">
<div class="box-body">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Testimonial Title<th>Company<th>Actions</thead>
    <tbody>
      <?php
      foreach ($records as $user) :
      ?>
        <tr id="tr<?=$user['id']?>"><td><?=$user['name']?></td><td><?=$user['company']?><td><a href='?actions=edit&id=<?=$user['id']?>&r=testimonials/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$user['id']?>' event_name='<?=$user['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
     <?php
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>
</div>
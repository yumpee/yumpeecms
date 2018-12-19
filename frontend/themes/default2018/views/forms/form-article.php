<?php


$this->title = 'Articles';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$this->registerJs( <<< EOT_JS
 tinymce.init({ selector:'textarea',
           theme: 'modern',
    width: '100%',
        branding:false,
    height: 300,
        file_picker_callback: function(callback, value, meta) {
            $('#myModal').modal('show');
        },
    menubar:false,
    plugins: [
    'advlist autolink lists link image charmap print preview anchor textcolor',
    'searchreplace visualblocks code fullscreen',
    'insertdatetime media table contextmenu paste code help wordcount'
  ],
  toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help' });
              
        
        
        
        
       $(document).on('click', '#btnSubmit',
       function(ev) { 
                    if($("#title").val()==""){
                        alert("Your article must have a title");
                        return;
                    }
                    var chk_arr =  document.getElementsByName("blog_index[]");
                    var chklength = chk_arr.length;             
                    var blog_check=0;
                    for(k=0;k< chklength;k++)
                    {
                        if(chk_arr[k].checked){
                            blog_check++;
                        }
                    } 
                if(blog_check < 1){
                    alert("Your article must have at least one Blog Index selected");
                    return;
                }
        $("#body_content").val(tinymce.get('body_content').getContent());
        $.post(
            '{$metadata['saveURL']}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
                //location.href='?r=articles/index';
            }
        )
        ev.preventDefault();
  }); 
      
   
EOT_JS
);

?>

<style>
    .thumbnail:hover {
    position:relative;
    top:-25px;
    left:-35px;
    width:200px;
    height:auto;
    display:block;
    z-index:999;
}
.modal-dialog{
    position: relative;
    display: table; /* This is important */ 
    overflow-y: auto;    
    overflow-x: auto;
    width: auto;
    min-width: 300px;   
}
</style>

<section id="inner-banner">
    <div class="container">
      <h1><?=$page['title']?></h1>
    </div>
  </section>
<br />
<div class="container">
<?=$page['description']?>

<div id="addNews">
    <form action="<?=$metadata['submit_url']?>" method="post" name="frm1" id="frm1" class="form-group">
    <table class="table">
        <tr><td>Title *<td><input name="title" id="title" value="" class="form-control" type="text" />
        <tr><td>Article Type<td><?= \yii\helpers\Html::dropDownList("article_type",'0',['1'=>'Standard','2'=>'Video','3'=>'Audio'],['class'=>'form-control'])?>
        <tr><td>Youtube URL<td><input type="text" class="form-control" name="featured_media" id="featured_media" value=""/>
        <tr><td>Feature Image<td><img id='my_display_image' src='' height='100px' align='top' width='200px' style='border:1px solid #233388' HSPACE='20' VSPACE='20'/> <a href='#' class='media' id='set_feature'>Set Feature Image</a> | <a href='#' id='unset_feature'>Unset Feature Image</a> <input type="hidden" name="display_image_id" id="display_image_id" value=""/>
        <tr><td>Thumbnail<td><img id='my_thumbnail_image' src='' height='100px' align='top' width='100px' style='border:1px solid #233388' HSPACE='20' VSPACE='20'/> <a href='#' class='media' id='set_thumbnail'>Set thumbnail Image</a> | <a href='#' id='unset_thumbnail'>Unset thumbnail Image</a> <input type="hidden" name="thumbnail_image_id" id="thumbnail_image_id" value=""/>        
        <tr><td>Lead Content<td><input type="text" name="lead_content" id="lead_content" class="form-control"></input>
        <tr><td>Body Content  <td><textarea name="body_content" id="body_content"  class="form-control"rows="7" cols="40"></textarea>
        <tr><td valign='top'>Category<td><?=$metadata['category']?>
        <tr><td valign='top'>Blog Index<td><?=$metadata['blog_index']?>
        <tr><td colspan="2"><button type="button" class="btn btn-success" id="btnSubmit">Publish Article</button> <input type="hidden" name="processor" value="true" />
         <input type="hidden" name="<?=$metadata['param']?>" value="<?=$metadata['token']?>">
         <input type="hidden" name="form_type" value="<?=$form['form_type']?>"></td>
        
        
    </table>
    </form>
</div>


<div class="box">

</div></div>



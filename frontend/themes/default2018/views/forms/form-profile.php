<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

$this->registerJs( <<< EOT_JS
    tinymce.init({ selector:'textarea',
           theme: 'modern',
        branding:false,
    width: '100%',
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
        $("#about").val(tinymce.get('about').getContent());
        $.post(
            '{$metadata['saveURL']}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
            }
        )
        ev.preventDefault();
  }); 
  
EOT_JS
);
?>
<section id="inner-banner">
    <div class="container">
      <h1><?=$page['title']?></h1>
    </div>
  </section>
<br />
<div class="container">
    <?=$page['description']?>
<form action="index.php?r=events/index" method="post" id="frm1">   
    <table class="table">
        <tr><td>Username<td><input readonly name="usrname" id="usrname" class="form-control" type="text" value="<?=$metadata['rs']['username']?>"/>
        <tr><td>Password<td><input name="passwd" id="passwd" class="form-control" type="password" value="<?=$metadata['rs']['password_hash']?>"/>
        <tr><td>First Name<td><input name="first_name" id="first_name" value="<?=$metadata['rs']['first_name']?>" class="form-control"type="text" />
        <tr><td>Last Name<td><input name="last_name" value="<?=$metadata['rs']['last_name']?>" id="last_name" class="form-control" type="text" />
        <tr><td>Title<td><input name="title" value="<?=$metadata['rs']['title']?>" id="title" class="form-control" type="text" />  
        <tr><td>Email<td><input name="email" value="<?=$metadata['rs']['email']?>" id="email" class="form-control" type="text" /> 
        <tr><td>About<td><textarea name="about" id="about" class="form-control"><?=$metadata['rs']['about']?></textarea>
        <tr><td>Feature Image<td><img id='my_display_image' src='<?=Yii::getAlias("@image_dir")?>/' height='100px' align='top' width='200px' style='border:1px solid #233388' HSPACE='20' VSPACE='20'/> <a href='#' class='media' id='set_feature'>Set Feature Image</a><input type="hidden" name="display_image_id" id="display_image_id" value="<?=$metadata['rs']['display_image_id']?>"/>
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <input type="hidden" name="processor" value="true" /><input type="hidden" name="id" value="<?=$metadata['rs']['id']?>" /></td>
    </table>
    <input type="hidden" name="<?=$metadata['param']?>" value="<?=$metadata['token']?>">
    <input type="hidden" name="form_type" value="<?=$form['form_type']?>"></td>
</form>
</div>

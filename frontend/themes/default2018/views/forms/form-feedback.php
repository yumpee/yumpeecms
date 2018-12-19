<?php
$this->title="Feedback";
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
                    
        $("#comments").val(tinymce.get('comments').getContent());
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
<?php
if($page['title']<>""):
    ?>

<section id="inner-banner">
    <div class="container">
      <h1><?=$page['title']?></h1>
    </div>
  </section>
<?php endif;?>  
  
<br />
<div class="container">
<?=$page['description']?>
              <form action="#" id="frm1">
                
                <div class="row">
                  <div class="col-md-8">
                    <input name="name" class="form-control" required pattern="[a-zA-Z ]+" type="text" placeholder="Name">
                  </div>
                  <div class="col-md-8">
                    <input name="email" class="form-control" required pattern="^[a-zA-Z0-9-\_.]+@[a-zA-Z0-9-\_.]+\.[a-zA-Z0-9.]{2,5}$" type="email" placeholder="Email">
                  </div>
                  <div class="col-md-8">
                    <input name="website" class="form-control" required type="text" placeholder="Website">
                  </div>
                    <div class="col-md-8">
                        <select name="enquiry_type" class="form-control"><option>General</option><option>Technical</option></select>
                  </div>
                  <div class="col-md-8">
                    <textarea name="comments" id="comments" required cols="10" rows="10" placeholder="Comments"></textarea>
                  </div>
                  <div class="col-md-8">
                    <input type="button" class="btn btn-primary" value="Submit" id="btnSubmit">
                    <input type="hidden" name="url" value="">
                  </div>
                </div>
                    <input type="hidden" name="<?=$metadata['param']?>" value="<?=$metadata['token']?>">
                    <input type="hidden" name="form_type" value="<?=$form['form_type']?>"></td>
                    <input type="hidden" name="feedback_type" value="<?=$metadata['feedback_type']?>"></td>
                    <input type="hidden" name="target_id" value="<?=$metadata['target_id']?>">
                    <input type="hidden" name="form_id" value="<?=$form['id']?>"></td>
              </form>
</div>

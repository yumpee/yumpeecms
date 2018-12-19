<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

$customURL =  \Yii::$app->getUrlManager()->createUrl('forms/fetch-view-twig-theme');
$saveURL =  \Yii::$app->getUrlManager()->createUrl('forms/save-view-twig-theme');

$this->registerJs( <<< EOT_JS
 
$("#theme").change(function(){
        var new_theme=$("#theme").val();
        location.href='?r=forms/views&reload=true&theme=' + new_theme;
        
});
        
       $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=widgets/index';
        
        
  }); 
  
  $(document).on('click', '#btnSaveTwig',
       function(ev) {   
        saveAce();    
        $.post(
            '{$saveURL}',$( "#frmTheme" ).serialize(),
            function(data) {
                alert(data);
                
            }
        )
        ev.preventDefault();
  }); 
            
  $('.twig_event').click(function (element) {  
      var id = $(this).attr('id');
       var program_name = $(this).attr('event_name');
       var filename = $(this).attr('filename');
           
      var getSelectedIndex = document.frmTheme.theme.selectedIndex;
      var theme = document.frmTheme.theme[getSelectedIndex].value;
      document.frmTheme.renderer.value=id;
        if(theme==""){
            alert("Please select a valid theme");
            return;
        }
      $.get(
                '{$customURL}',{renderer:id,theme_id:theme},
                function(data) { 
                    //$('#yumpee_widget_content').text("");
                    //$('#yumpee_widget_content').text(data);
                    readAce(data);   
                    $('#myModal').modal();
                    $('#program_name').html(program_name);
                    $('#filename').val(filename);
                }    
            )
     ev.preventDefault(); 
  });
                $("#datalisting").DataTable();
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
<style type="text/css" media="screen">
    #yumpee_widget_content { 
        position: relative;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0px;
        min-width:1500px;
        height:800px;
     
    }
</style>
<div class="container-fluid">
    <h3>Summary Form View</h3>
<form id="frmTheme" name="frmTheme">
    <table>
        <tr><td>Select Theme<td><?=$theme?>
    </table>
    <div class="box">
<div class="box-body">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th>Page View<th>Name<th>Actions</thead>
    <tbody>
      <?php
      foreach ($records as $user) :
          $filename="";  
      if($user['hasContents']!=null && $user['hasContents']['theme_id']==$selected_theme && $user['hasContents']['code']!="" && $user['hasContents']['renderer_type']=="R"):
          if(substr($user['hasContents']['filename'], 0, strlen("twig/")) === "twig/"):
                $filename = $user['hasContents']['filename'];
          endif;
      ?>
        <tr><td><font color='green'><?=$user['title']?></font></td><td><font color='green'><?=$user['title']?></font></td></td><td> <a href='#' class='twig_event' id='<?=$user['id']?>' event_name='<?=$user['title']?>' title="User Create Twig" filename="<?=$filename?>"><small><i class="fa fa-file-code-o"></i></small></a> </td>
        <?php
            else:
        ?>
        <tr><td><?=$user['title']?></td><td><?=$user['title']?></td></td><td> <a href='#' class='twig_event' id='<?=$user['id']?>' event_name='<?=$user['title']?>' title="User Create Twig" filename="<?=$filename?>"><small><i class="fa fa-file-code-o"></i></small></a> </td>
     <?php
        endif;
      endforeach;
     ?>
    </tbody>
</table>
</div>
</div>
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="btn btn-primary" id="btnSaveTwig">Save</button> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">File name - <span id="program_name"></span> <input type="text" name="filename" id="filename" size="100" placeholder="File Location (for local files)"/></h4>
      </div>
      <div class="modal-body">
          <textarea rows="30" cols="100"  name="code" style="display:none"></textarea><input type="hidden" name="renderer" />
          <div id="yumpee_widget_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btnSaveTwig">Save</button> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div> 
</form>
</div>

<script src="js/ace-builds-master/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
    var editor = ace.edit("yumpee_widget_content");
    editor.setTheme("ace/theme/monokai");
    editor.session.setMode("ace/mode/twig");
    function readAce(data){
       editor.getSession().setValue(data); 
        
    }
    function saveAce(){
       document.frmTheme.code.value=editor.getValue();
    }
</script>
<?php
$this->title="Settings";
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$saveURL = \Yii::$app->getUrlManager()->createUrl('settings/save');
$customURL = \Yii::$app->getUrlManager()->createUrl('settings/custom-save');
$mediaURL =  \Yii::$app->getUrlManager()->createUrl('media/featured-media');
$deleteCustomURL =  \Yii::$app->getUrlManager()->createUrl('settings/delete-custom');

$this->registerJs( <<< EOT_JS
       tinymce.init({
  selector: 'a.editable',
  inline: true,
  plugins: ["image"],
  toolbar:'image',
  file_browser_callback: function(field_name, url, type, win) {
	            if(type=='image') $('#my_form input').click();
	        }
});
       
        
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

$(document).on('click', '#btnSubmitCustom',
       function(ev) {   
        
        $.post(
            '{$customURL}',$( "#frmCustom" ).serialize(),
            function(data) {
                alert(data);
            }
        )
        ev.preventDefault();
}); 
$('.delete_custom').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete - ' + event_name)){
                        $.get(  
                            '{$deleteCustomURL}',{id:id},
                            function(data) {
                                alert(data);
                            }
                        )
                    }            
  });
  $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=settings/index&custom_id=0';
        
        
  }); 
            
  $('.media').click(function (element) {  
      localStorage.setItem("image_caller",$(this).attr('id')); //store who is calling this dialog 
      $.get(
                '{$mediaURL}',{search:'featured',exempt_the_headers_in_yumpee:'true'},
                function(data) {                    
                    $('#yumpee_media_content').html(data);
                    $('#myModal').modal();
                }    
            )
     ev.preventDefault(); 
  });
  
   $('.unsetmedia').click(function (element) { 
         if(confirm('Are you sure you want to unset the logo for your website?')){
             $("#display_image_id").val("");
             $('#btnSubmit').trigger('click');
         }
                
   });
   
   $('.icon').click(function (element) {  
      localStorage.setItem("image_caller",$(this).attr('id')); //store who is calling this dialog 
      $.get(
                '{$mediaURL}',{search:'featured',exempt_the_headers_in_yumpee:'true'},
                function(data) {                    
                    $('#yumpee_media_content').html(data);
                    $('#myModal').modal();
                }    
            )
     ev.preventDefault(); 
  });
  
   $('.unseticon').click(function (element) { 
         if(confirm('Are you sure you want to unset the icon for your website?')){
             $("#fav_icon").val("");
             $('#btnSubmit').trigger('click');
         }
                
   });
            
   $(document).on('click','#btnInsertMedia',
       function(ev){
       var myradio = $("input[name='my_images']:checked").val();   
       var my_radio_info = myradio.split("|");
       var img_src = my_radio_info[0];
       var my_id = my_radio_info[1];
       if(localStorage.image_caller=="set_feature"){
                $("#display_image_id").val(my_id);
                $("#my_display_image").attr("src","../../uploads/" + img_src);
                localStorage.removeItem("image_caller");
       }
       if(localStorage.image_caller=="set_icon"){
                $("#fav_icon").val(my_id);
                $("#my_fav_icon").attr("src","../../uploads/" + img_src);
                localStorage.removeItem("image_caller");
       }
                
       $('#myModal').modal('toggle');     
   });
   
 if($("#custom_id").val()!=""){
  $('#manage_custom').trigger('click')        
 }        

                
                

$(document).ready(function(){
    $('[data-toggle="popover"]').popover();
});

EOT_JS
); 
     
$display_image_id="";
$display_image_path_info="";
$fav_icon_path_info="";
$fav_icon="";
$smtp_connection="";
if(isset($display_image_path)):
    $display_image_path_info = $display_image_path->path;
endif;
if(isset($fav_icon_path)):
    $fav_icon_path_info = $fav_icon_path->path;
endif;
foreach($records as $record):
    if($record['setting_name']=="current_theme"):
        $current_theme = $record['setting_value'];
    endif;
    if($record['setting_name']=="website_name"):
        $website_name = $record['setting_value'];
    endif;
    if($record['setting_name']=="website_short_name"):
        $website_short_name = $record['setting_value'];
    endif;
    if($record['setting_name']=="website_logo"):
        $display_image_id = $record['setting_value'];
    endif;
    if($record['setting_name']=="fav_icon"):
        $fav_icon = $record['setting_value'];
    endif;
    if($record['setting_name']=="website_image_url"):
        $website_image_url = $record['setting_value'];
    endif;
    if($record['setting_name']=="website_home_page"):
        $website_home_page = $record['setting_value'];
    endif;
    if($record['setting_name']=="error_page"):
        $error_page = $record['setting_value'];
    endif;
    if($record['setting_name']=="contact_us_email"):
        $contact_us_email = $record['setting_value'];
    endif;
    if($record['setting_name']=="contact_us_address"):
        $contact_us_address = $record['setting_value'];
    endif;
    if($record['setting_name']=="google_map_key"):
        $google_map_key = $record['setting_value'];
    endif;
    if($record['setting_name']=="date_format"):
        $date_format = $record['setting_value'];
    endif;
    if($record['setting_name']=="time_format"):
        $time_format = $record['setting_value'];
    endif;
    if($record['setting_name']=="breadcrumbs"):
        $breadcrumbs = $record['setting_value'];
    endif;
    if($record['setting_name']=="page_size"):
        $page_size = $record['setting_value'];
    endif;
    if($record['setting_name']=="smtp_host"):
        $smtp_host = $record['setting_value'];
    endif;
    if($record['setting_name']=="smtp_port"):
        $smtp_port = $record['setting_value'];
    endif;
    if($record['setting_name']=="seo_meta_tags"):
        $seo_meta_tags = $record['setting_value'];
    endif;
    if($record['setting_name']=="smtp_connection"):
        $smtp_connection = $record['setting_value'];
    endif;
    if($record['setting_name']=="smtp_username"):
        $smtp_username = $record['setting_value'];
    endif;
    if($record['setting_name']=="smtp_password"):
        $smtp_password = $record['setting_value'];
    endif;
    if($record['setting_name']=="smtp_sender_email"):
        $smtp_sender_email = $record['setting_value'];
    endif;
    if($record['setting_name']=="smtp_sender_name"):
        $smtp_sender_name = $record['setting_value'];
    endif;
    if($record['setting_name']=="smtp_use_smtp"):
        $smtp_use_smtp = $record['setting_value'];
    endif;
    if($record['setting_name']=="auto_approve_comments"):
        $auto_approve_comments = $record['setting_value'];
    endif;
    if($record['setting_name']=="current_theme_header"):
        $current_theme_header = $record['setting_value'];
    endif;
    if($record['setting_name']=="current_theme_footer"):
        $current_theme_footer = $record['setting_value'];
    endif;
    if($record['setting_name']=="container_display_type"):
        $container_display_type = $record['setting_value'];
    endif;
    if($record['setting_name']=="home_url"):
        $home_url = $record['setting_value'];
    endif;
    if($record['setting_name']=="twig_template"):
        $twig_template = $record['setting_value'];
    endif;
    if($record['setting_name']=="registration_role"):
        $registration_role = $record['setting_value'];
    endif;
    if($record['setting_name']=="registration_page"):
        $registration_page = $record['setting_value'];
    endif;
    if($record['setting_name']=="auto_approve_post"):
        $auto_approve_post = $record['setting_value'];
    endif;
    if($record['setting_name']=="maintenance_mode"):
        $maintenance_mode = $record['setting_value'];
    endif;
    if($record['setting_name']=="maintenance_page"):
        $maintenance_page = $record['setting_value'];
    endif;
    if($record['setting_name']=="captcha"):
        $captcha = $record['setting_value'];
    endif;
    if($record['setting_name']=="captcha_private"):
        $captcha_private = $record['setting_value'];
    endif;
    if($record['setting_name']=="captcha_public"):
        $captcha_public = $record['setting_value'];
    endif;
    if($record['setting_name']=="minify_javascript"):
        $minify_javascript = $record['setting_value'];
    endif;
    if($record['setting_name']=="minify_css"):
        $minify_css = $record['setting_value'];
    endif;
    if($record['setting_name']=="minify_twig"):
        $minify_twig = $record['setting_value'];
    endif;
    if($record['setting_name']=="use_custom_backend_menus"):
        $use_custom_backend_menus = $record['setting_value'];
    endif;
endforeach;

    $custom_name="";
    $pos = strpos($custom_rs['setting_name'], "custom_");
        if ($pos !== false) {
            $custom_name = substr_replace($custom_rs['setting_name'], "", $pos, strlen("custom_"));
        }

?>
<style>
    .modal-dialog{
    position: relative;
    display: table; /* This is important */ 
    overflow-y: auto;    
    overflow-x: auto;
    width: auto;
    min-width: 300px;   
} 
</style>
<div class="container-fluid">
<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#system">System Settings</a></li>
  <li><a data-toggle="tab" href="#custom" id="manage_custom">Custom Settings</a></li>
  
</ul>
 
<div class="tab-content">
<div id="system" class="tab-pane fade in active">
     <form action="index.php?r=events/index" method="post" id="frm1">
    <table class="table">
        <tr><td colspan="2"><h4>General Website Info</h4></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: website_name">Site Title</a></a><td><input type='text' class='form-control' name="website_name" value="<?=$website_name?>"></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: website_short_name">Tag line</a><td><input type='text' class='form-control' name="website_short_name" value="<?=$website_short_name?>"></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: website_logo">Website Logo</a><td><img id='my_display_image' src='<?=Yii::getAlias("@image_dir")?>/<?=$display_image_path_info?>' height='100px' align='top' width='200px' style='border:1px solid #233388' HSPACE='20' VSPACE='20'/> 
                <?php
                if($display_image_id==""):
                ?>
                <a href='#' class='media' id='set_feature'>Set Logo</a>
                <?php
                else:
                ?>
                <a href='#' class='unsetmedia' id='unset_feature'>Unset Logo</a>
                <?php
                endif;
                ?>
                <input type="hidden" name="website_logo" id="display_image_id" value="<?=$display_image_id?>"/></td>
            
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: website_home_page">Website Home Page</a><td><?=\yii\helpers\Html::dropDownList("website_home_page",$website_home_page,$pages,['class'=>'form-control'])?></td> 
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: home_url">Website URL</a> <td><input type='text' class='form-control' name="home_url" value="<?=$home_url?>"> </td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: fav_icon">Website Favicon</a><td><img id='my_fav_icon' src='<?=Yii::getAlias("@image_dir")?>/<?=$fav_icon_path_info?>' height='100px' align='top' width='200px' style='border:1px solid #233388' HSPACE='20' VSPACE='20'/> 
                <?php
                if($fav_icon==""):
                ?>
                <a href='#' class='icon' id='set_icon'>Set Icon</a>
                <?php
                else:
                ?>
                <a href='#' class='unseticon' id='unset_icon'>Unset Icon</a>
                <?php
                endif;
                ?>
                <input type="hidden" name="fav_icon" id="fav_icon" value="<?=$fav_icon?>"/></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: error_page">Error 404 Page</a><td><?=\yii\helpers\Html::dropDownList("error_page",$error_page,$error_pages,['class'=>'form-control'])?></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: maintenance_mode">Turn Maintenance Mode On / Page</a><td><?=\yii\helpers\Html::dropDownList("maintenance_mode",$maintenance_mode,['No'=>'No','Yes'=>'Yes'],['class'=>'form-control'])?><br/>
                <?=\yii\helpers\Html::dropDownList("maintenance_page",$maintenance_page,$maintenance_pages,['class'=>'form-control'])?>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: website_image_url">Image Directory </a><td><input type='text' class='form-control' name="website_image_url" value="<?=$website_image_url?>"> </td>
        <tr><td colspan="2"><h4>Contact Us Settings</h4></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: contact_us_email">Contact Us Email </a><td><input type='text' class='form-control' name="contact_us_email" value="<?=$contact_us_email?>"></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: contact_us_address">Contact Us Address </a><td><textarea class='form-control' rows="6" cols="40" name="contact_us_address"><?=$contact_us_address?></textarea></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: google_map_key">Google Map API Key </a><td><input type='text' class='form-control' name="google_map_key" value="<?=$google_map_key?>"></td>
        <tr><td colspan="2"><h4>Appearance </h4></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: current_theme">Current Theme</a><td><?=\yii\helpers\Html::dropDownList("current_theme",$current_theme,$themes,['class'=>'form-control'])?></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: current_theme_header">Use CMS Theme Header</a><td><?=\yii\helpers\Html::dropDownList("current_theme_header",$current_theme_header,['No'=>'No','Yes'=>'Yes'],['class'=>'form-control'])?>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: current_theme_footer">Use CMS Theme Footer</a><td><?=\yii\helpers\Html::dropDownList("current_theme_footer",$current_theme_footer,['No'=>'No','Yes'=>'Yes'],['class'=>'form-control'])?>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: twig_template">Use Twig Template</a><td><?=\yii\helpers\Html::dropDownList("twig_template",$twig_template,['No'=>'No','Yes'=>'Yes'],['class'=>'form-control'])?>       
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: container_display_type">Container Display</a><td><?= \yii\helpers\Html::dropDownList("container_display_type",$container_display_type,['box'=>'Box Display','fluid'=>'Wide Display'],['class'=>'form-control'])?>       
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: breadcrumbs">Turn on breadcrumbs</a><td><?=\yii\helpers\Html::dropDownList("breadcrumbs",$breadcrumbs,['off'=>'No','on'=>'Yes'],['class'=>'form-control'])?></td>
        <tr><td colspan="2"><h4>SEO </h4></td>  
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: seo_meta_tags">Meta Tags Global </a><td><textarea class='form-control' rows="6" cols="40" name="seo_meta_tags"><?=$seo_meta_tags?></textarea></td>
        <tr><td colspan="2"><h4>Backend </h4></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: use_custom_backend_menus">Custom Backend Menus</a><td><?=\yii\helpers\Html::dropDownList("use_custom_backend_menus",$use_custom_backend_menus,['off'=>'No','on'=>'Yes'],['class'=>'form-control'])?></td>   
        <tr><td colspan="2"><h4>Blogs </h4></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: page_size">No of items per page</a><td><input type='text' class='form-control' name="page_size" value="<?=$page_size?>"></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: auto_approve_comments">Auto Approve Comments</a><td><?=\yii\helpers\Html::dropDownList("auto_approve_comments",$auto_approve_comments,['off'=>'No','on'=>'Yes'],['class'=>'form-control'])?></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: auto_approve_post">Auto Publish User Post</a><td><?=\yii\helpers\Html::dropDownList("auto_approve_post",$auto_approve_post,['off'=>'No','on'=>'Yes'],['class'=>'form-control'])?></td>   
        <tr><td colspan="2"><h4>User Registration </h4></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: registration_role">Default Role on Registration</a><td><?= \yii\helpers\Html::dropDownList("registration_role",$registration_role,$registration_roles,['class'=>'form-control'])?>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: registration_page">Default Page on Registration</a><td><?= \yii\helpers\Html::dropDownList("registration_page",$registration_page,$pages,['class'=>'form-control'])?>
        <tr><td colspan="2"><h4>Date and Time </h4></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: date_format">Date Format</a><td><?= \yii\helpers\Html::dropDownList("date_format",$date_format,['F j, Y'=>'December 28, 2017','Y-m-d'=>'2017-12-28','m/d/Y'=>'12/28/2017'],['class'=>'form-control'])?>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: time_format">Time Format</a><td><?= \yii\helpers\Html::dropDownList("time_format",$time_format,['h:m a'=>'5:16 am','h:m A'=>'5:16 AM','H:m'=>'05:16'],['class'=>'form-control'])?>
        <tr><td colspan="2"><h4>SMTP Integration</h4>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: smtp_host">SMTP Host</a><td><input type='text' class='form-control' name="smtp_host" value="<?=$smtp_host?>"></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: smtp_port">SMTP Port</a><td><input type='text' class='form-control' name="smtp_port" value="<?=$smtp_port?>"></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: smtp_use_smtp">Use SMTP Port</a><td><?= \yii\helpers\Html::dropDownList("smtp_use_smtp",$smtp_use_smtp,['Yes'=>'Yes','No'=>'No'],['class'=>'form-control'])?>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: smtp_connection">Secure connection Prefix</a><td><?= \yii\helpers\Html::dropDownList("smtp_connection",$smtp_connection,[''=>'','ssl'=>'SSL','tls'=>'TLS'],['class'=>'form-control'])?>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: smtp_username">SMTP username</a><td><input type='text' class='form-control' name="smtp_username" value="<?=$smtp_username?>"></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: smtp_password">SMTP password</a><td><input type='password' class='form-control' name="smtp_password" value="<?=$smtp_password?>"></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: smtp_sender_email">Sender email</a><td><input type='text' class='form-control' name="smtp_sender_email" value="<?=$smtp_sender_email?>"></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: smtp_sender_name">Sender name</a><td><input type='text' class='form-control' name="smtp_sender_name" value="<?=$smtp_sender_name?>"></td>
        <tr><td colspan="2"><h4>reCAPTCHA Setting </h4></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: captcha">Enable reCAPTCHA</a><td><?=\yii\helpers\Html::dropDownList("captcha",$captcha,['off'=>'No','on'=>'Yes'],['class'=>'form-control'])?></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: captcha_public">Site Key</a><td><input type='text' class='form-control' name="captcha_public" value="<?=$captcha_public?>"></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: captcha_private">Secret Key</a><td><input type='text' class='form-control' name="captcha_private" value="<?=$captcha_private?>"></td>
        <tr><td colspan="2"><h4>Content Minification </h4></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: minify_javascript">Minify Javascript</a><td><?=\yii\helpers\Html::dropDownList("minify_javascript",$minify_javascript,['off'=>'No','on'=>'Yes'],['class'=>'form-control'])?></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: minify_css">Minify CSS</a><td><?=\yii\helpers\Html::dropDownList("minify_css",$minify_css,['off'=>'No','on'=>'Yes'],['class'=>'form-control'])?></td>
        <tr><td><a href="#" onclick="return false;" data-toggle="popover" title="Info" data-content="Setting name: minify_twig">Minify Twig</a><td><?=\yii\helpers\Html::dropDownList("minify_twig",$minify_twig,['off'=>'No','on'=>'Yes'],['class'=>'form-control'])?></td>
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button>  <input type="hidden" name="processor" value="true" /><input type="hidden" name="id" value="<?=$id?>" />
            
            </td>
        
        
    </table>
    </form>
     
     <iframe id="form_target" name="form_target" style="display:none"></iframe>
	<form id="my_form" action="/upload/" target="form_target" method="post" enctype="multipart/form-data" style="width:0px;height:0;overflow:hidden">
	    <input name="image" type="file" onchange="$('#my_form').submit();this.value='';">
	</form>
</div>
    
    <div id="custom" class="tab-pane fade">
        <form action="index.php?r=settings/index" method="post" id="frmCustom">
    <table class="table">
        <tr><td>Setting Name<td><input name="setting_name" id="setting_name" value="<?=$custom_name?>" class="form-control" type="text" />
        <tr><td>Setting Value<td><input name="setting_value" id="setting_value" value="<?=$custom_rs['setting_value']?>" class="form-control" type="text" />
        <tr><td>Description<td><textarea class="form-control" name="description"><?=$custom_rs['description']?></textarea>
        
        <tr><td colspan="2"><button type="submit" id="btnSubmitCustom" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" id="custom_id" name="id" value="<?=$custom_id?>" />
            
            </td>
        
        
    </table>
    </form>
        <div class="box">
            <div class="box-body">
                <table id="datalisting" class="table table-bordered table-striped">
                    <thead><tr><th>Name<th>Value<th>Actions</thead>
                    <tbody>
                        <?php
                            foreach ($custom_records as $user) :
                        ?>
                        <tr><td><?=$user['setting_name']?></td><td><?=$user['setting_value']?><td> <a href='?actions=edit&custom_id=<?=$user['id']?>&r=settings/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_custom' id='<?=$user['id']?>' event_name='<?=$user['setting_name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a>  </td>
                        <?php
                        endforeach;
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Insert Media</h4>
      </div>
      <div class="modal-body">
          <div id="yumpee_media_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="btnInsertMedia">Insert Media</button> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
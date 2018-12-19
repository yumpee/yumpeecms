<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use frontend\components\ThemeManager;
use frontend\components\BlockComponent;
use frontend\components\ContentBuilder;
use frontend\components\Minify;
use frontend\models\Pages;
use backend\models\Settings;
use frontend\models\Themes;
use frontend\models\CSS;
use backend\models\Menus;
use backend\models\Users;
use backend\models\Media;
use backend\models\Roles;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;


AppAsset::register($this);
//we load our css and javascript here
$themes = new Themes();
$my_theme_object=Themes::find()->where(['id'=>$themes->currentTheme])->one();
$settings = new Settings();

if($my_theme_object==null):
    echo "No theme has been set for this site. Please contact the web administrator";
    exit();
endif;

//get the theme information
$javascript = $my_theme_object->javascript;
$folder = $my_theme_object->folder;
$stylesheet = $my_theme_object->stylesheet;
$current_theme_header = $my_theme_object->header;
$current_theme_footer = $my_theme_object->footer;
//protect certain content of the page depending if a user is logged in or not
                    if((Yii::$app->user->isGuest)):
                        $hider = array("{yumpee_hide_on_login}", "{/yumpee_hide_on_login}");
                        $current_theme_header = str_replace($hider,"",$current_theme_header);
                        $current_theme_header = ContentBuilder::getScreenContent($current_theme_header,"{yumpee_login_to_view}",TRUE);  
                        $current_theme_footer = str_replace($hider,"",$current_theme_footer);
                        $current_theme_footer = ContentBuilder::getScreenContent($current_theme_footer,"{yumpee_login_to_view}",TRUE);  
                    endif;
                    if((!Yii::$app->user->isGuest)):
                        $hider = array("{yumpee_login_to_view}", "{/yumpee_login_to_view}");
                        $current_theme_header = str_replace($hider,"",$current_theme_header);
                        $current_theme_header=ContentBuilder::getScreenContent($current_theme_header,"{yumpee_hide_on_login}",TRUE); 
                        $current_theme_footer = str_replace($hider,"",$current_theme_footer);
                        $current_theme_footer=ContentBuilder::getScreenContent($current_theme_footer,"{yumpee_hide_on_login}",TRUE); 
                    endif;
                      
                     
                    //


$minify = new Minify();
if(ContentBuilder::getSetting("minify_css")=="on"):
    $custom_styles = $minify->minify_css($my_theme_object->custom_styles);
else:
    $custom_styles = $my_theme_object->custom_styles;
endif;

//load stylesheets
$style_sheet_array = explode(";",$stylesheet);
for($i=0;$i<count($style_sheet_array);$i++):
    $this->registerCssFile("@themes/".$folder."/".$style_sheet_array[$i], [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
], '');
endfor;

//load javascript files
$meta="";
$javascript_array = explode(";",$javascript);
for($i=0;$i<count($javascript_array);$i++):
    $this->registerJsFile("@themes/".$folder."/".$javascript_array[$i],
    ['depends' => [\yii\web\JqueryAsset::className()]]);
endfor;

	
	
$current_url=ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
$current_page = Pages::find()->where(['url'=>$current_url])->one();
if($current_page==null):
    
    $current_url=ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl(),2);
    $current_page = Pages::find()->where(['url'=>$current_url])->one();
endif;

//echo $current_url;
//if the current page is the home page, then we skip this
if(ContentBuilder::getSetting("website_home_page")!=$current_page['id']){
	$current_url = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
}



$page_styles="";
if($current_page!=null):
    $meta = $current_page['meta_description'];
    $css_content = CSS::find()->where(['id'=>$current_page->css])->one();
    if($css_content!=null):
        if(ContentBuilder::getSetting("minify_css")=="on"):
            $page_styles = "<style>".$minify->minify_css($css_content->css)."</style>";
        else:
            $page_styles = "<style>".$css_content->css."</style>";
        endif;
    endif;
endif;
if($meta==""):
    $meta=ContentBuilder::getSetting("seo_meta_tags");
endif;
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="generator" content="YumpeeCMS 1.0.9" />    
    <?= Html::csrfMetaTags() ?>
    <?=$meta?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?php
    //let's handle favicon here
    $icon = Media::find()->where(['id'=>ContentBuilder::getSetting("fav_icon")])->one();
    if($icon!=null):
    ?>
    <link rel="icon" href="<?=Yii::getAlias("@image_dir")?>/<?=$icon->path?>" type="image/x-icon">
    <?php
        else:
    ?>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <?php    
    endif;
	
	
    ?>
    
    
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    
<!--GOOGLE FONTS-->

<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

<style>
    <?php echo $custom_styles?>
</style>
<?php echo $page_styles?>

<body>
<?php
if(ContentBuilder::getSetting("container_display_type")=="fluid"):
        $container_display_type="class='container-fluid'";
        
    else:
        $container_display_type="";
        echo "<div class='container'>";
endif;
?>

    
<?php $this->beginBody() ?>
        <?= Breadcrumbs::widget([
            'homeLink' => [ 
                      'label' => Yii::t('yii', 'Home'),
                      'url' => Yii::$app->homeUrl,
                 ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
    
 
<div id="yumpee_block_top_header" align="center">
</div>
<?php
//Fetch here the relevant information for our menus and then pass to the theme header
$header_menu_logo =ContentBuilder::getImage($settings->logo,"logo");   
$header_baseURL = Yii::$app->request->getBaseUrl();
$footer_menus = new Pages();


if($current_page!=null):
    $header_menus = Menus::getProfileMenus($current_page->menu_profile);

    if($header_menus==null):
        //we need to ensure that registration and login menus do not show when the user is logged in
        if (Yii::$app->user->isGuest) {
            $header_menus = Pages::find()->where(['show_in_menu'=>'1'])->andWhere('require_login<>"Y"')->orderBy('sort_order')->all();
            $footer_menus = Pages::find()->where(['show_in_footer_menu'=>'1'])->andWhere('require_login<>"Y"')->orderBy('sort_order')->all();
        }else{
            $header_menus = Pages::find()->where(['show_in_menu'=>'1'])->andWhere('hideon_login<>"Y"')->orderBy('sort_order')->all();
            $footer_menus = Pages::find()->where(['show_in_footer_menu'=>'1'])->andWhere('hideon_login<>"Y"')->orderBy('sort_order')->all();
        }
    endif;
 else:
        if (Yii::$app->user->isGuest) {
            $header_menus = Pages::find()->where(['show_in_menu'=>'1'])->andWhere('require_login<>"Y"')->orderBy('sort_order')->all();
            $footer_menus = Pages::find()->where(['show_in_footer_menu'=>'1'])->andWhere('require_login<>"Y"')->orderBy('sort_order')->all();
        }else{
            $header_menus = Pages::find()->where(['show_in_menu'=>'1'])->andWhere('hideon_login<>"Y"')->orderBy('sort_order')->all();
            $footer_menus = Pages::find()->where(['show_in_footer_menu'=>'1'])->andWhere('hideon_login<>"Y"')->orderBy('sort_order')->all();
        }
 endif;
$header_request_url = Yii::$app->request->url;
$header_title = $this->title;

//End fetch for theme header
?>
<?php $this->beginContent(ThemeManager::getHeader('@app/views/layouts/header.php'),['header_menu_logo'=>$header_menu_logo,'header_content'=>$current_theme_header,'header_theme_setting'=>ContentBuilder::getSetting("current_theme_header"),'header_baseURL'=>$header_baseURL,'header_menus'=>$header_menus,'header_request_url'=>$header_request_url,'header_title'=>$header_title,'settings'=>$settings]); ?>

<?php $this->endContent(); ?>
 
    <div id="yumpee_block_top_content"></div>
        
<?= $content ?>
 
<div id="yumpee_block_bottom_content"></div>


<?php $this->beginContent(ThemeManager::getFooter('@app/views/layouts/footer.php'),['footer_content'=>$current_theme_footer,'footer_theme_setting'=>ContentBuilder::getSetting("current_theme_footer"),'settings'=>$settings,'header_baseURL'=>$header_baseURL,'footer_menus'=>$footer_menus]); ?>
  
<?php $this->endContent(); ?>


<?php $this->endBody() ?>


</div>

<?php
    
  $cb = new ContentBuilder();
  $right_widget_object = $cb->getWidgets("side");
  $bottom_widget_object = $cb->getWidgets("bottom");
  $custom_position_object = $cb->getCustomWidgets();
  $blockURL = \Yii::$app->getUrlManager()->createUrl('block/ajax');
  
  
  
  //we can handle the blocks from here
    $yumpee_block_top_header="";
    $yumpee_block_bottom_header="";
    $yumpee_block_top_content="";
    $yumpee_block_bottom_content="";
    $yumpee_block_top_footer="";
    $yumpee_block_bottom_footer="";
    $yumpee_block_top_left="";
    $yumpee_block_bottom_left="";
    $yumpee_block_top_right="";
    $yumpee_block_bottom_right="";
    
    $block_object = $cb->getBlocks();
    if($block_object!=null):
    $block_object_arr = $block_object->blocks;
    foreach($block_object_arr as $block_record):
        $block_content =  $block_record->title;
        switch($block_record->position):
            case "before_left":
                $yumpee_block_top_left.=addslashes($block_record->content);
            break;
            case "after_left":
                $yumpee_block_bottom_left.=addslashes($block_record->content);
            break;
            case "before_right":
                $yumpee_block_top_right.=addslashes($block_record->content);
            break;
            case "after_right":
                $yumpee_block_bottom_right.=addslashes($block_record->content);
            break;
            case "before_header":
                $yumpee_block_top_header.=addslashes($block_record->content);
            break;
            case "after_header":
                $yumpee_block_bottom_header.=addslashes($block_record->content);
            break;
            case "before_content":
                $yumpee_block_top_content.=addslashes($block_record->content);
            break;
            case "after_content":
                $yumpee_block_bottom_content.=addslashes($block_record->content);
             
            break;
            case "before_footer":
                $yumpee_block_top_footer.=addslashes($block_record->content);
                
            break;
            case "after_footer":
                $yumpee_block_bottom_footer.=addslashes($block_record->content);
                
                
            break;
            
        endswitch;
      
    endforeach;
    $this->registerJs( <<< EOT_JS
            $("#yumpee_block_top_header").html("{$yumpee_block_top_header}");
            $("#yumpee_block_bottom_header").html("{$yumpee_block_bottom_header}");
            $("#yumpee_block_top_content").html("{$yumpee_block_top_content}");
            $("#yumpee_block_bottom_content").html("{$yumpee_block_bottom_content}");
            $("#yumpee_block_top_footer").html("{$yumpee_block_top_footer}");
            $("#yumpee_block_bottom_footer").html("{$yumpee_block_bottom_footer}");
            $("#yumpee_block_top_left").html("{$yumpee_block_top_left}");
            $("#yumpee_block_bottom_left").html("{$yumpee_block_bottom_left}");
            $("#yumpee_block_top_right").html("{$yumpee_block_top_right}");
            $("#yumpee_block_bottom_right").html("{$yumpee_block_bottom_right}");
EOT_JS
    );
    endif;
    

    //Processing the side and bottom widgets
    $widgetURL = \Yii::$app->getUrlManager()->createUrl('widget/ajax');
    
    
    foreach($right_widget_object as $sidebar):
    //process the widgets on the side
    $this->registerJs( <<< EOT_JS
            $.get(
                '{$widgetURL}',{widget:'{$sidebar['widget']}',page_id:'{$current_url}'},
                function(data) {                    
                    $("#yumpee_sidebar_widgets").html($("#yumpee_sidebar_widgets").html() + "<br>" + data);
                }    
            )
            
EOT_JS
    );
    endforeach;
    
    foreach($bottom_widget_object as $bottombar):
    //process the bottom widgets
    $this->registerJs( <<< EOT_JS
            $.get(
                '{$widgetURL}',{widget:'{$bottombar['widget']}',page_id:'{$current_url}'},
                function(data) {                    
                    $("#yumpee_bottombar_widgets").html($("#yumpee_bottombar_widgets").html() + "<br>" + data);
                }    
            )
            
EOT_JS
    );
    endforeach;
    
    foreach($custom_position_object as $posbar):
    //process the bottom widgets
    $this->registerJs( <<< EOT_JS
            
            $.get(
                '{$widgetURL}',{widget:'{$posbar['widget']}',page_id:'{$current_url}'},
                function(data) {    
                    
                    $("#{$posbar['position']}").html($("#{$posbar['position']}").html() + "<br>" + data);
                }    
            )
            
EOT_JS
    );
    endforeach;
    
    
                
    //end of block contents insertion into the page
$subscriptionURL = \Yii::$app->getUrlManager()->createUrl('ajaxsubscription/subscription'); 
$blogFeedbackURL= \Yii::$app->getUrlManager()->createUrl('ajaxblogfeedback/feedback');
$contactURL= \Yii::$app->getUrlManager()->createUrl('ajaxcontact/feedback');
$searchURL= \Yii::$app->getUrlManager()->createUrl('ajaxsearch/search');
$formWidgetURL = \Yii::$app->getUrlManager()->createUrl('custom-form-widget/index');


$this->registerJs( <<< EOT_JS
   //////////////////////////////////////////WE PROCESS ALL THE STANDARD FORM CLICKS HERE////////////////////////////////////////////////////////
   //blog Subscription
   $(document).on('click', '#btnBlogSubscription',
       function(ev) {  
        $.get(
            '{$subscriptionURL}',$( "#frmBlogSubscription" ).serialize(),
            function(data) {
                alert(data);
            }
        )
       ev.preventDefault(); 
  }); 
  
  //processing the feedback form

   $(document).on('click', '#btnBlogFeedback',
       function(ev) {  
        $.get(
            '{$blogFeedbackURL}',$( "#frmBlogFeedback" ).serialize(),
            function(data) {
                alert(data);
            }
        )
        ev.preventDefault();
  }); 
       
//process the contact form
$(document).on('click', '#btnContact',
       function(ev) {  
        $.get(
            '{$contactURL}',$( "#frmContact" ).serialize(),
            function(data) {
                alert(data);
            }
        )
        ev.preventDefault();
  });
 
 //handle the search button widget
 $(document).on('click', '#btnSearchBlog',
       function(ev) { 
        $.get(
            '{$searchURL}',$("#frmSearchBlog").serialize(),
            function(data) {
                //we fetch the search URL and append the search content to it 
                location.href=data + "/" + $("#yumpee_search_text").val();
            }
        )
        ev.preventDefault();
  });         
///////////////////////////////////////////////////////END OF BUTTON CLICK EVENTS HERE////////////////////////////////////////////////////////////////////////////////////// 
   
///////////////////////////////////////////////////////HANDLE SLIDERS START//////////////////////////////////////////////////////////////////////////////
//check to see if there is a slider on this page
var myIndex = 0;
    if($('#yumpee_slider').length >0){     
     carousel();       
    }
            
function carousel() {
    var i;
    var x = document.getElementsByClassName("yumpee_slider");
    for (i = 0; i < x.length; i++) {
       x[i].style.width="100%";
       x[i].style.display = "none";  
    }
    myIndex++;
    if (myIndex > x.length) {myIndex = 1}    
    x[myIndex-1].style.display = "block";  
    setTimeout(carousel, 4000); // Change image every 2 seconds
}         
            
//////////////////////////////////////////////////////////HANDLE SLIDERS END///////////////////////////////////////////////////////////////////////////////
            
///////////////////////////////////////////////////////////HANDLE CUSTOM BLOCKS///////////////////////////////////////////////////////////////////////////
   var block_ref;
                
   if($("[class^=yumpee_custom_block]").length >0){     
          $( "[class^=yumpee_custom_block]" ).each(function() {
            var block_details = $(this)[0].className;
            block = block_details.split(":");
            block_ref = $(this);
            loadCustomBlock(block_ref,block);
        }); 
 }  
 function loadCustomBlock(block_ref,block){
                $.get(
                '{$blockURL}',{id:block[1]},
                function(data) { 
                    block_ref.html(data);
                    //make widget calls for this block incase the original call was asynchronous and has n't happened yet
                    var widget_ref;
                    if($("[class^=yumpee_custom_widget]").length >0){     
                    $( "[class^=yumpee_custom_widget]" ).each(function() {
                    var widget_details = $(this)[0].className;
                    widget = widget_details.split(":");
                    widget_ref = $(this);
                    loadCustomWidget(widget_ref,widget);
                    }); 
                    } 
                }  
            )
 }
                
///////////////////////////////////////////////////////////CUSTOM BLOCKS END////////////////////////////////////////////////////////////////////////          

           
///////////////////////////////////////////////////////////HANDLE CUSTOM WIDGETS///////////////////////////////////////////////////////////////////////////
   
   var widget_ref;
   if($("[class^=yumpee_custom_widget]").length >0){     
          $( "[class^=yumpee_custom_widget]" ).each(function() {
            var widget_details = $(this)[0].className;
            widget = widget_details.split(":");
            widget_ref = $(this);
            loadCustomWidget(widget_ref,widget);
        }); 
 } 
 
 function loadCustomWidget(widget_ref,widget){
                if(widget[1]=="widget_recaptcha"){
                    return;
                }
                if(widget.length > 3){
                    filter=widget[3];
                }else{
                    filter="0";
                }
                $.get(
                '{$widgetURL}',{widget:widget[1],limit:widget[2],filter:filter,page_id:'{$current_url}'},
                function(data) { 
                   widget_ref.html(data);
                }  
            )
 }
 
 
                
///////////////////////////////////////////////////////////CUSTOM WIDGETS END/////////////////////////////////////////////////////////////////////////////

                
                
                

///////////////////////////////////////////////////////////HANDLE CUSTOM FORM WIDGETS///////////////////////////////////////////////////////////////////////////
   
   var widget_ref;
   if($("[class^=yumpee_custom_form_widget]").length >0){     
          $( "[class^=yumpee_custom_form_widget]" ).each(function() {
            var widget_details = $(this)[0].className;
            widget = widget_details.split(":");
            widget_ref = $(this);
            loadCustomFormWidget(widget_ref,widget);
        }); 
 } 
 
 function loadCustomFormWidget(widget_ref,widget){
                if(widget.length > 3){
                    filter=widget[3];
                }else{
                    filter="0";
                }
                
                $.get(
                '{$formWidgetURL}',{widget:widget[1],limit:widget[2],filter:filter,page_id:'{$current_url}'},
                function(data) {                    
                   
                    widget_ref.html(data);
                    
                   
                   
                }  
            )
 }
 
 
                
///////////////////////////////////////////////////////////CUSTOM FORM WIDGETS END/////////////////////////////////////////////////////////////////////////////

            
EOT_JS
    );
    ?>

</body>
</html>
<?php
if(ContentBuilder::getSetting("captcha")=="on"):
?>
<script>
    var onReCaptchaLoad = function() {
        
     var widget_ref;
   if($("[class^=yumpee_custom_widget]").length >0){  
        
          $( "[class^=yumpee_custom_widget]" ).each(function() {            
            var widget_details = $(this)[0].className;
            widget = widget_details.split(":");
            widget_ref = $(this);  
            if(widget[1]=="widget_recaptcha"){
             
            var captchaWidgetId = grecaptcha.render( widget_ref.attr("id"), {
            'sitekey' : '<?=ContentBuilder::getSetting("captcha_public")?>',  // required
            'theme' : 'light',  // optional
            'callback': 'verifyCallback'  // optional
            });
            //by default we disable the submission button if a recaptcha is enabled for that page
            $("#btnSubmit").css("display","none");
            }
        }); 
 } 
    };
    var verifyCallback = function( response ) {
        console.log( 'g-recaptcha-response: ' + response );
        yumpee_captcha_validate(response);
    };
    
</script>
<script src="https://www.google.com/recaptcha/api.js?render=explicit&onload=onReCaptchaLoad"></script>
<?php
endif;
?>
<script>
    <!--xframe clickjacking buster-->
if (top.location != location) {
  top.location = self.location;
}
</script>
<?php $this->endPage() ?>



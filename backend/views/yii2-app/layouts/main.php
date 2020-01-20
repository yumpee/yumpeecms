<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
$skin="skin-blue";
if(Yii::$app->user->identity<>null):
switch(Yii::$app->user->identity->extension):
  case "1":
      $skin="skin-red-light";
  break;
  case "2":
      $skin="skin-yellow-light";
  break;
   case "3":
      $skin="skin-green-light";
  break;
  case "4":
      $skin="skin-blue-light";
  break;
case "5":
      $skin="skin-purple-light";
  break;
  case "6":
      $skin="skin-black-light";
  break;
case "7":
      $skin="skin-red";
  break;
  case "8":
      $skin="skin-yellow";
  break;
case "9":
      $skin="skin-green";
  break;
  case "10":
      $skin="skin-blue";
  break;
case "11":
      $skin="skin-purple";
  break;
  case "12":
      $skin="skin-black";
  break;
endswitch;
endif;

$this->title='Yumpee CMS';
if (Yii::$app->controller->action->id === 'login') { 
/**
 * Do not use this code in your template. Remove it. 
 * Instead, use the code  $this->layout = '//main-login'; in your controller.
 */
    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {

    if (class_exists('backend\assets\AppAsset')) {
        backend\assets\AppAsset::register($this);
    } else {
        app\assets\AppAsset::register($this);
    }

    dmstr\web\AdminLteAsset::register($this);

    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
             
    </head>
    <body class="hold-transition <?=$skin?> sidebar-mini">
    <?php $this->beginBody() ?>
        <?php
        if(Yii::$app->request->get('exempt_the_headers_in_yumpee')=="true"):
                echo "<div class='wrapper'>";
        
                echo $this->render(
            'content.php',
            ['content' => $content, 'directoryAsset' => $directoryAsset]);
                echo "</div>";
            else:
        ?>
    <div class="wrapper">
        
        <?= $this->render(
            'header.php',
            ['directoryAsset' => $directoryAsset]
        ) ?>

        <?= $this->render(
            'left.php',
            ['directoryAsset' => $directoryAsset]
        )
        ?>

        <?= $this->render(
            'content.php',
            ['content' => $content, 'directoryAsset' => $directoryAsset]
        ) ?>
        
    </div>
        <?php
        endif;
        ?>

    <?php $this->endBody() ?>
    <?php
                $version="1.5";
                $a = new \frontend\components\ContentBuilder();
                if(method_exists($a,'getVersion')):
                    $version=\frontend\components\ContentBuilder::APPLICATION_VERSION;
                endif;
                
                ?>
    <script src="https://www.yumpeecms.com/yp-admin/js/datatables/jquery.dataTables.min.js"></script>
    <script>
    //we check for updates to the system from our site
    //$(document).ready(function(){
        $.get( "https://www.yumpeecms.com/api/forms/frmUpgrade" 
            )
        .done(
            function(returndata){      
                var application_version = "<?=$version?>";                
                var version="";
                var description="";
                var title="";
                var records = (typeof returndata == "object" ? returndata : JSON.parse(returndata));                
                var b = records['data'][0]['data'];
                for(i=0;i< b.length;i++){              
                    if(b[i]['param']=="version"){    
                        version = b[i]['param_val'];
                    }
                    if(b[i]['param']=="contents"){   
                        description = b[i]['param_val'];
                    }
                    if(b[i]['param']=="content_title"){   
                        title = b[i]['param_val'];
                    }
                }
                //alert(version);
                if(version != application_version){
                    $("#yumpee_new_updates_available_remote").html("<div class='alert alert-info col-md-offset-2 col-md-4'><strong>Info!</strong> " + title + ". <a href='#' data-toggle='modal' data-target='#yumpee_bbostrap_modal_updates'>Learn More</a></div>");
                    $("#yumpee_bbostrap_modal_updates_messages").html(description);
                }	
            }
        );
      
    //})
   </script>
 <div id="yumpee_bbostrap_modal_updates" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">YumpeeCMS Notification</h4>
      </div>
      <div class="modal-body" id="yumpee_bbostrap_modal_updates_messages">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
    </body>
    </html>
    <?php $this->endPage() ?>
<?php } ?>
<?php
if(Yii::$app->request->get("r")=="menus/index"):
?>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>    
<?php
exit;
endif;
?>

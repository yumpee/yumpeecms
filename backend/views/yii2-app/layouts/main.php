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
    <script src="http://www.yumpeecms.com/yp-admin/js/datatables/jquery.dataTables.min.js"></script>
    </body>
    </html>
    <?php $this->endPage() ?>
<?php } ?>

<?php
use yii\helpers\Html;
use backend\models\Settings;
use backend\models\Feedback;

$my_header_home_url = Settings::find()->where(['setting_name'=>'home_url'])->one();
$contact_count = Feedback::find()->where(['feedback_type'=>'contact'])->andWhere('status="N"')->count();
/* @var $this \yii\web\View */
/* @var $content string */

Yii::$app->name='YumpeeCMS';
//if not logged in then shouldn't have access to here
if(Yii::$app->user->identity==null):
    return ['site/index'];
endif;
$display_image_path="";
if(isset(Yii::$app->user->identity->displayImage->path)):
    $display_image_path=Yii::$app->user->identity->displayImage->path;
endif;
?>
<link href="https://www.yumpeecms.com/yp-admin/css/jquery.dataTables.min.css" rel="stylesheet">
<header class="main-header">

    <?= Html::a('<span class="logo-mini">Yumpee</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>
    
    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <center><span id="yumpee_new_updates_available_remote"></span></center>
        <div class="navbar-custom-menu">
            
            <ul class="nav navbar-nav">                
                <li><input type="button" class="btn btn-default" value="Visit Website" onClick="javascript:window.open('<?=$my_header_home_url['setting_value']?>?yumpee_template_preview=on&theme_id=0','_blank')" />
                <!-- Messages: style can be found in dropdown.less-->
                
                <!-- Tasks: style can be found in dropdown.less -->
                <li class="dropdown tasks-menu">
                    <a href="?r=install/index" class="dropdown-toggle"  title="Install Templates">
                        <i class="fa fa-download"></i>
                        
                    </a>
                </li>
                <li class="dropdown tasks-menu">
                    <a href="?r=package/index" class="dropdown-toggle"  title="Export Package">
                        <i class="fa fa-upload"></i>
                    </a>
                </li>
                <li class="dropdown tasks-menu" style="display:none">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Install Templates">
                        <i class="fa fa-bullhorn"></i>
                        <span class="label label-danger">9</span>
                    </a>
                    <ul class="dropdown-menu" id="yumpee_global_announce">                        
                        <li class="header">You have 9 tasks</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                <li><!-- Task item -->
                                    <a href="#">
                                        <h3>
                                            Design some buttons
                                            <small class="pull-right">20%</small>
                                        </h3>
                                        <div class="progress xs">
                                            <div class="progress-bar progress-bar-aqua" style="width: 20%"
                                                 role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                 aria-valuemax="100">
                                                <span class="sr-only">20% Complete</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <!-- end task item -->
                                <li><!-- Task item -->
                                    <a href="#">
                                        <h3>
                                            Create a nice theme
                                            <small class="pull-right">40%</small>
                                        </h3>
                                        <div class="progress xs">
                                            <div class="progress-bar progress-bar-green" style="width: 40%"
                                                 role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                 aria-valuemax="100">
                                                <span class="sr-only">40% Complete</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <!-- end task item -->
                                <li><!-- Task item -->
                                    <a href="#">
                                        <h3>
                                            Some task I need to do
                                            <small class="pull-right">60%</small>
                                        </h3>
                                        <div class="progress xs">
                                            <div class="progress-bar progress-bar-red" style="width: 60%"
                                                 role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                 aria-valuemax="100">
                                                <span class="sr-only">60% Complete</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <!-- end task item -->
                                <li><!-- Task item -->
                                    <a href="#">
                                        <h3>
                                            Make beautiful transitions
                                            <small class="pull-right">80%</small>
                                        </h3>
                                        <div class="progress xs">
                                            <div class="progress-bar progress-bar-yellow" style="width: 80%"
                                                 role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                 aria-valuemax="100">
                                                <span class="sr-only">80% Complete</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <!-- end task item -->
                            </ul>
                        </li>
                        <li class="footer">
                            <a href="#">View all tasks</a>
                        </li>
                    </ul>
                </li>
                 <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-warning"><?=$contact_count?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">You have <?=$contact_count?> notification(s)</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">                                
                                <li>
                                    <a href="#">
                                        <i class="fa fa-user text-red"></i> Click link below to view 
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="footer"><a href="?r=feedback%2Findex">View all</a></li>
                    </ul>
                </li>
                <!-- User Account: style can be found in dropdown.less -->

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?php
                        if($display_image_path<>""):?>
                            <img src="<?=Yii::getAlias("@image_dir")?>/<?=$display_image_path?>" class="user-image" alt=""/>
                        <?php endif;?>
                        <span class="hidden-xs"><?=Yii::$app->user->identity->first_name." ".Yii::$app->user->identity->last_name?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?=Yii::getAlias("@image_dir")?>/<?=$display_image_path?>" class="img-circle"
                                 alt="User Image"/>

                            <p>
                                <?=Yii::$app->user->identity->first_name." ".Yii::$app->user->identity->last_name?> - <?=Yii::$app->user->identity->title?>
                                <small>Member since Nov. 2012</small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="col-xs-4 text-center">
                                <a href="#">Followers</a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="#">Sales</a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="#">Friends</a>
                            </div>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="?r=users/profile" class="btn btn-default btn-flat">Profile</a>
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    'Sign out',
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>

                <!-- User Account: style can be found in dropdown.less -->
                <li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>


<?php
if(Yii::$app->user->identity==null):
    $link= \yii\helpers\Url::toRoute('site/index');
    echo "<font color='white'>Session expired...please <a href='$link'>re-login</a></font>";
    return ['site/index'];
    exit;
endif;

$display_image_path="";
if(isset(Yii::$app->user->identity->displayImage->path)):
    $display_image_path=Yii::$app->user->identity->displayImage->path;
endif;
$role_obj = backend\models\BackEndMenuRole::find()->select('menu_id')->where(['role_id'=>Yii::$app->user->identity->role_id])->column();
$class_submenus_obj = backend\models\BackEndMenus::find()->where(['IN','id',$role_obj])->orderBy('parent_id,priority')->all();
$visible = Yii::$app->user->getIdentity()!=null;
$class_submenus_list=[];
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?=Yii::getAlias("@image_dir")?>/<?=$display_image_path?>" class="img-circle" alt=""/>
            </div>
            <div class="pull-left info">
                <p><?=Yii::$app->user->identity->first_name?></p>

                
            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->
        <?php        
        $first_level=[];
        foreach($class_submenus_obj as $first_tree):
            if($first_tree['parent']==null):
                $second_level=[];
                foreach($first_tree['assignedChild'] as $second_tree):
                    if($second_tree['assignedChild']==null):
                            if(strpos($second_tree['url'],'&')!==false):
                                        $url=Yii::$app->homeUrl.$second_tree['url'];
                                    else:
                                        $url=[$second_tree['url']];
                            endif;
                            $list_tree = ['label' => $second_tree['label'], 'icon'=>$second_tree['icon'],'url' => $url,'visible'=>Yii::$app->user->getIdentity()!=null];
                            array_push($second_level,$list_tree);
                    else:
                            $third_level=[];
                            foreach($second_tree['assignedChild'] as $third_tree):
                                if(strpos($third_tree['url'],'&')!==false):
                                        $url=Yii::$app->homeUrl.$third_tree['url'];
                                    else:
                                        $url=[$third_tree['url']];
                                endif;
                                $list_tree = ['label' => $third_tree['label'], 'icon'=>$third_tree['icon'],'url' => $url,'visible'=>Yii::$app->user->getIdentity()!=null];
                                array_push($third_level,$list_tree);
                            endforeach;
                            if(strpos($second_tree['url'],'&')!==false):
                                        $url=Yii::$app->homeUrl.$second_tree['url'];
                                    else:
                                        $url=[$second_tree['url']];
                            endif;
                            $list_tree = ['label' => $second_tree['label'], 'icon'=>$second_tree['icon'],'url' => $url,'visible'=>Yii::$app->user->getIdentity()!=null,'items'=>$third_level];
                            array_push($second_level,$list_tree);
                    endif;
                    
                endforeach;
                if($first_tree['assignedChild']!=null):
                    $second_array=array('items'=>$second_level);
                    $list_tree = ['label' => $first_tree['label'], 'icon'=>$first_tree['icon'],'url' => '#','visible'=>Yii::$app->user->getIdentity()!=null,'items'=>$second_level];
                    array_push($first_level,$list_tree);
                endif;
            endif;
        endforeach;
        $list_tree=[];
        if(Yii::$app->user->identity->username=="admin"):
            $list_tree=['label' => 'Manage Admin Menus', 'icon'=>'fa fa-key','url' => ['/backend/index'],'visible'=>Yii::$app->user->getIdentity()!=null,];
        endif;
        array_push($first_level,$list_tree);
        $user_menu = array('options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],'items'=>$first_level);
        
        
        $main_menu = [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => 'Home', 'icon'=>'fa fa-home','url' => ['/site/index']],
                    ['label' => 'Market Place', 'icon'=>'fa fa-shopping-cart','url' => '#','visible'=>Yii::$app->user->getIdentity()!=null,
                     'items'=>[
                            ['label' => 'Industry', 'icon'=>'fa fa-building','url' => ['/categories/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                            ['label' => 'Skills','icon' => 'fa fa-institution', 'url' => ['/skills/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                            ['label' => 'Job Type','icon' => 'fa fa-level-up', 'url' => ['/job/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                            ['label' => 'Pay Type','icon' => 'fa fa-money', 'url' => ['/pay/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                         ]
                    ],
                    ['label' => 'Blogs', 'icon'=>'fa fa-file','url' => '#','visible'=>Yii::$app->user->getIdentity()!=null,
                     'items'=>[
                         ['label' => 'Articles', 'icon' => 'fa fa-file-text', 'url' => ['/articles/index'],],
                         ['label' => 'Category', 'icon' => 'fa fa-folder', 'url' => ['/articles/category'],],
                         ['label' => 'Tags', 'icon' => 'fa fa-tags', 'url' => ['/tags/index'],],
                         ['label' => 'Media', 'icon' => 'fa fa-file-image-o', 'url' => ['/media/index'],],
                         ['label' => 'Comments', 'icon' => 'fa fa-comments', 'url' => ['/comment/index'],],
                         ['label' => 'Testimonials', 'icon' => 'fa fa-quote-left', 'url' => ['/testimonials/index'],],
                         ['label' => 'Subscription', 'icon'=>'fa fa-users','url' => ['/subscriptions/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                     ]  
                
                    ],
                    ['label' => 'Web', 'icon'=>'fa fa-globe','url' => '#','visible'=>Yii::$app->user->getIdentity()!=null,
                     'items'=>[
                        ['label' => 'Blocks', 'icon'=>'fa fa-th-large','url' => ['/blocks/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Widgets', 'icon'=>'fa fa-th','url' => ['/widgets/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Pages', 'icon'=>'fa fa-file','url' => ['/pages/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'CSS Profiles', 'icon'=>'fa fa-code','url' => ['/css/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Gallery', 'icon'=>'fa fa-image','url' => ['/gallery/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Sliders', 'icon'=>'fa fa-sliders','url' => ['/slider/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Rating Profiles', 'icon'=>'fa fa-star','url' => ['/rating/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Menus', 'icon'=>'fa fa-th-list','url' => ['/menus/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Templates', 'icon'=>'fa fa-globe','url' => ['/templates/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Forms', 'icon'=>'fa fa-minus-square-o','url' => ['/forms/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Translation', 'icon'=>'fa fa-language','url' => ['/translation/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        
                     ]
                    ], 
                    ['label' => 'System', 'icon'=>'fa fa-cogs','url' => '#','visible'=>Yii::$app->user->getIdentity()!=null,
                     'items'=>[
                        ['label' => 'Users', 'icon'=>'fa fa-user','url' => ['/users/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Themes', 'icon'=>'fa fa-adjust','url' => ['/themes/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Settings', 'icon'=>'fa fa-cog','url' => ['/settings/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Class Setup', 'icon'=>'fa fa-building','url' => ['/setup/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Domains', 'icon'=>'fa fa-globe','url' => ['/domains/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Languages', 'icon'=>'fa fa-language','url' => ['/language/index'],'visible'=>Yii::$app->user->getIdentity()!=null],   
                     ]
                    ],
                    ['label' => 'Extensions', 'icon'=>'fa fa-code','url' => '#','visible'=>Yii::$app->user->getIdentity()!=null,
                     'items'=>[
                        ['label' => 'Widgets', 'icon'=>'fa fa-th','url' => ['/widgets/extensions'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Views', 'icon'=>'fa fa-adjust','url' => ['/themes/extensions'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Forms', 'icon'=>'fa fa-minus-square-o','url' => '#','visible'=>Yii::$app->user->getIdentity()!=null,
                          'items'=>[
                                ['label' => 'Post', 'icon'=>'fa fa-plus-circle','url' => ['/forms/extensions'],'visible'=>Yii::$app->user->getIdentity()!=null],
                                ['label' => 'Summary View', 'icon'=>'fa fa-eye','url' => ['/forms/views'],'visible'=>Yii::$app->user->getIdentity()!=null],
                                ['label' => 'Details View', 'icon'=>'fa fa-list','url' => ['/forms/fdetails'],'visible'=>Yii::$app->user->getIdentity()!=null],
                                ['label' => 'Widgets', 'icon'=>'fa fa-windows','url' => ['/forms/fwidgets'],'visible'=>Yii::$app->user->getIdentity()!=null],
                          ]
                        ],
                        
                        ['label' => 'Import', 'icon'=>'fa fa-file','url' => ['/themes/import'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Relationships', 'icon'=>'fa fa-sitemap','url' => ['/relationships/index'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Theme Settings', 'icon'=>'fa fa-cog','url' => ['/themes/settings'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        
                    ],
                    ],
                    ['label' => 'Reports', 'icon'=>'fa fa-bar-chart','url' => '#','visible'=>Yii::$app->user->getIdentity()!=null,
                     'items'=>[
                        ['label' => 'Setup', 'icon'=>'fa fa-cog','url' => ['/reports/setup'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Report List', 'icon'=>'fa fa-list','url' => ['/reports/list'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'System Logs', 'icon'=>'fa fa-history','url' => ['/reports/logs'],'visible'=>Yii::$app->user->getIdentity()!=null,
                          
                        ],                        
                    ],
                    ],
                    ['label' => 'Web Services', 'icon'=>'fa fa-plug','url' => '#','visible'=>Yii::$app->user->getIdentity()!=null,
                     'items'=>[
                         ['label' => 'Client Profile', 'icon'=>'fa fa-user','url' => '#','visible'=>Yii::$app->user->getIdentity()!=null,
                          'items'=>[
                                ['label' => 'Incoming', 'icon'=>'fa fa-sign-in','url' => ['/services/incoming'],'visible'=>Yii::$app->user->getIdentity()!=null],
                                ['label' => 'Outgoing', 'icon'=>'fa fa-sign-out','url' => ['/services/outgoing'],'visible'=>Yii::$app->user->getIdentity()!=null],
                          ]
                        ],
                        ['label' => 'Resource Profile', 'icon'=>'fa fa-database','url' => ['/services/resource'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Emulator', 'icon'=>'fa fa-rss','url' => ['/services/emulator'],'visible'=>Yii::$app->user->getIdentity()!=null],
                        ['label' => 'Logs', 'icon'=>'fa fa-history','url' => ['/services/logs'],'visible'=>Yii::$app->user->getIdentity()!=null],
                    ],
                    ],
                    ['label' => 'Manage Admin Menus', 'icon'=>'fa fa-key','url' => ['/backend/index'],'visible'=>Yii::$app->user->getIdentity()!=null,
                        
                    ],
                    //'items'=>$class_submenus_list,
                    
                    
                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    
                ],
            ];
        ?>
        <?php
        if(\frontend\components\ContentBuilder::getSetting("use_custom_backend_menus")=="on"):
        ?>
        <?= dmstr\widgets\Menu::widget($user_menu
            
        ) ?>
        <?php
        else:
        ?>
        <?= dmstr\widgets\Menu::widget($main_menu
            
        ) ?>
        <?php
        endif;
        ?>
       
        
    </section>

</aside>

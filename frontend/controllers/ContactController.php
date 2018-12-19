<?php
namespace frontend\controllers;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use frontend\components\ContentBuilder;
use frontend\models\Pages;
use frontend\models\Twig;
use frontend\models\Templates;
use backend\models\Settings;

class ContactController extends Controller{
    
    public function actionIndex(){
       $page =[];
       $page_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
       $article = Pages::find()->where(['url'=>$page_url])->one();
                    if(isset($article->displayImage)):
                        $header_image= ContentBuilder::getImage($article->displayImage->id,"details");
                    else:
                        $header_image="";
                    endif;
                    if($article==null):
                            return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/standard/error',['page'=>$article,'header_image'=>$header_image]);
                    endif;  
                    if(ContentBuilder::getSetting("breadcrumbs")=="on"):
                        $this->view->params['breadcrumbs'][] = $article->menu_title;
                    endif;
                    
                    $renderer="contact/index";
                    $template = Templates::find()->where(['id'=>$article->template])->one();
                    if(!empty($template->parent_id)):
                        $renderer = $template->renderer;
                    endif;
                    //render through twig if available
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content = $twig->render($codebase['filename'], ['page'=>$article,'header_image'=>$header_image]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
					$settings = new Settings();
                    if($article->layout=="column1"):
                            return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$renderer,['page'=>$article,'header_image'=>$header_image,'settings'=>$settings]);
                    endif;
                    if($article->layout=="column2"):
                            return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$renderer.'-2',['page'=>$article,'header_image'=>$header_image,'settings'=>$settings]);
                    endif;
                    if($article->layout=="column3"):
                            return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$renderer.'-3',['page'=>$article,'header_image'=>$header_image,'settings'=>$settings]);
                    endif;
                    return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$renderer,['page'=>$article,'header_image'=>$header_image,'settings'=>$settings]);
    }
    
    public function actionWidget(){
        
        return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/contact/index',$page);
    }
}
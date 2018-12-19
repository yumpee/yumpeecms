<?php

/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use frontend\components\ContentBuilder;
use backend\models\Articles;
use backend\models\ArticlesCategories;
use frontend\models\Pages;
use frontend\models\Templates;
use backend\models\Users;
use frontend\models\Twig;
use backend\models\Roles;
use backend\models\MenuPage;

class StandardController extends Controller{
    
   public function actionIndex(){
       
       $page =[];
       
       $page_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
       if (strpos($page_url, '?') !== false):
                list($page_url,$search)= explode("?",$page_url);
       endif;
       if(Yii::$app->request->get('page_id')!=null):
           $article = Pages::find()->where(['id'=>Yii::$app->request->get('page_id')])->one();
       else:
            $article = Pages::find()->where(['url'=>$page_url])->one();
       endif;
       
       //if it requires log in and we are not logged in then redirect to login page
       if(($article['require_login']=="Y")&&(Yii::$app->user->isGuest)):       
            $page =[];
            $form['callback'] = $page_url;
            $page_url =  ContentBuilder::getURLByRoute("accounts/login");
            $article = Pages::find()->where(['url'=>$page_url])->one();          
            $form['login_url'] = Yii::$app->request->getBaseUrl()."/".$article['url'];
            $form['message'] = "You have to login to view this page";
            $form['param'] = Yii::$app->request->csrfParam;
            $form['token'] = Yii::$app->request->csrfToken;
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>'accounts/login','renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'], ['form'=>$form,'page'=>$article]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
                         
                    return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/account/login',['form'=>$form,'page'=>$article]);
                    
       endif;
       
       //we check to see if this page is restricted to be for users within a particular role
       if($article['menu_profile']!=null && $article['require_login']=="Y"):
           $user_arr = Users::find()->where(['username'=>Yii::$app->user->identity->username])->one();
           $menu_id = $user_arr->role->menu_id; //this is actually the profile ID. We need to now check to be sure that this page should be viewed by this profile ID since its a restricted page
           $access = MenuPage::find()->where(['menu_id'=>$article['id']])->andWhere('profile="'.$menu_id.'"')->one();
                      
           if($access==null):
               throw new \yii\web\HttpException(403, 'You do not have right privileges to view to this page. Consult with your administrator.');
           endif;
       endif;
       
       //end require login
                    //protect certain content of the page depending if a user is logged in or not
                    if((Yii::$app->user->isGuest)):                        
                        $hider = array("{yumpee_hide_on_login}", "{/yumpee_hide_on_login}");
                        $description = str_replace($hider,"",$article->description);
                        $article->setAttribute("description",ContentBuilder::getScreenContent($description,"{yumpee_login_to_view}",TRUE));                        
                    endif;
                    if((!Yii::$app->user->isGuest)):                        
                        $hider = array("{yumpee_login_to_view}", "{/yumpee_login_to_view}");
                        $description = str_replace($hider,"",$article->description);
                        $article->setAttribute("description",ContentBuilder::getScreenContent($description,"{yumpee_hide_on_login}",TRUE)); 
                    endif;
                    //
                    
                    
                    if(isset($article->displayImage)):
                        $header_image= ContentBuilder::getImage($article->displayImage->id,"details");
                    else:
                        $header_image="";
                    endif;
                    if($article==null):
                            //check to see if there is a default error 404 page
                            $error404 = ContentBuilder::getSetting("error_page");
                            if($error404!=""):
                                $article = Pages::find()->where(['id'=>$error404])->one();
                                if($article==null):
                                    return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/standard/error',['page'=>$article,'header_image'=>$header_image]);
                                endif;
                            else:
                                return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/standard/error',['page'=>$article,'header_image'=>$header_image]);
                            endif;
                            
                    endif;
                    
                    if(ContentBuilder::getSetting("breadcrumbs")=="on"):
                        $this->view->params['breadcrumbs'][] = $article->menu_title;
                    endif;
                    
                    $renderer="standard/index";
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
                            $content= $twig->render($codebase['filename'], ['page'=>$article,'header_image'=>$header_image]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
                    if($article->layout=="column1"):
                            return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$renderer,['page'=>$article,'header_image'=>$header_image]);
                    endif;
                    if($article->layout=="column2"):
                            return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$renderer.'-2',['page'=>$article,'header_image'=>$header_image]);
                    endif;
                    if($article->layout=="column3"):
                            return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$renderer.'-3',['page'=>$article,'header_image'=>$header_image]);
                    endif;
                    return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$renderer,['page'=>$article,'header_image'=>$header_image]);
   } 
    
}
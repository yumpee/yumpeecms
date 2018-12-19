<?php
namespace frontend\controllers;
/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use frontend\components\ContentBuilder;
use frontend\models\Twig;
use frontend\models\Templates;
use backend\models\Users;
use backend\models\Pages;
use backend\models\Articles;

class RolesController extends Controller{
    
    public function actionIndex(){
     $page =[];
     $news_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
     
     //we check to see what page is being called
            if (strpos($news_url, '?') !== false):
                list($news_url,$search)= explode("?",$news_url);
            endif;
    if(ContentBuilder::getTemplateRouteByURL($news_url)!="roles/index"):
        //if the details page for the user is requested this is what is processed
         
          $article = Articles::find()->where(['usrname'=>$news_url])->all();
          $user_profile = Users::find()->where(['username'=>$news_url])->one();
          $page_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl(),2);
          
          $page = Pages::find()->where(['url'=>$page_url])->one();
          //if it requires log in and we are not logged in then redirect to login page
                            if(($page['require_login']=="Y")&&(Yii::$app->user->isGuest)):       
                                                                   
                                    $page_url =  ContentBuilder::getURLByRoute("roles/index");
                                    $form['callback'] = $page_url."/".$news_url;
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
                                                $content= $twig->render($codebase['filename'], ['form'=>$form,'page'=>$page]);
                                                return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                                            endif;
                                    endif;                         
                                    return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/account/login',['form'=>$form,'page'=>$page]);                    
                            endif;       
                            //end require login
                    //protect certain content of the page depending if a user is logged in or not
                    if((Yii::$app->user->isGuest)):
                        $hider = array("{yumpee_hide_on_login}", "{/yumpee_hide_on_login}");
                        $description = str_replace($hider,"",$page->description);
                        $page->setAttribute("description",ContentBuilder::getScreenContent($description,"{yumpee_login_to_view}",TRUE));                        
                    endif;
                    if((!Yii::$app->user->isGuest)):
                        $hider = array("{yumpee_login_to_view}", "{/yumpee_login_to_view}");
                        $description = str_replace($hider,"",$page->description);
                        $page->setAttribute("description",ContentBuilder::getScreenContent($description,"{yumpee_hide_on_login}",TRUE)); 
                    endif;
                    
                    //
                    $preferred_template = Templates::find()->where(['id'=>$page->renderer])->one();                    
                    if($preferred_template!=null):
                        $renderer=$preferred_template->route;
                    else:
                        $renderer="roles/details";
                    endif;
          if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            //render your Twig page
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'], ['page'=>$page,'blogger'=>$user_profile,'articles'=>$article,'app'=>Yii::$app]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
            endif;
          return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/users/details',['page'=>$page,'blogger'=>$user_profile,'articles'=>$article,'app'=>Yii::$app]); 
          
      else:
          $pagination=[];
          $pagination['total_page_count']=0;
          $pagination['active_page']=1;
          $page_size = ContentBuilder::getSetting("page_size");  //we need to know what a page size is
          
          
          $page = Pages::findOne(['url'=>$news_url]);
          if($page['role_id']!=null && $page['role_id']<>""):
            $records = Users::find()->with('details')->where(['role_id'=>$page['role_id']])->orderBy('first_name')->all();
          else:
            $records = Users::find()->with('details')->orderBy('first_name')->all();
          endif;
          //breadcrumbs addition
          if(ContentBuilder::getSetting("breadcrumbs")=="on"):
            $this->view->params['breadcrumbs'][] = $page->menu_title;
          endif;
          
          $renderer="roles/index";
          $view_file="users/index";
          $template = Templates::find()->where(['id'=>$page->template])->one();
          if(!empty($template->parent_id)):
            $renderer = $template->renderer;
            $view_file="users/".$renderer;
           endif;
          if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'], ['page'=>$page,'records'=>$records,'pagination'=>$pagination]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
           endif;
          return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$view_file,['page'=>$page,'records'=>$records,'pagination'=>$pagination]);  
      endif;
    }
    
}
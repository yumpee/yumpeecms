<?php

/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Project Site : http://www.yumpeecms.com


 * YumpeeCMS is a Content Management and Application Development Framework.
 *  Copyright (C) 2018  Audmaster Technologies, Australia
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.

 */

namespace frontend\controllers;
use Yii;
use yii\web\Controller;
use backend\models\Tags;
use backend\models\Users;
use backend\models\Templates;
use backend\models\Articles;
use frontend\components\ContentBuilder;
use frontend\models\Twig;
use backend\models\RatingDetails;
use backend\models\RatingProfileDetails;
/**
 * Description of TagIndex
 *
 * @author Peter
 */
class TagsController extends Controller {
    public function actionIndex(){
     $page =[];
     $pagination=[];
     $pagination['total_page_count']=0;
     $pagination['active_page']=1;
     $page_size = ContentBuilder::getSetting("page_size"); 
     
     $news_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
        if (strpos($news_url, '?') !== false):
                list($news_url,$search)= explode("?",$news_url);
        endif;
            
      $records = Tags::find()->where(['url'=>$news_url])->one();
      
      
      $page['tags_record'] = $records;
      $page['articles'] = $records->articles;
      if($page_size > 0):
            $pagination['total_page_count'] = ceil(count($records->articles) / $page_size) ;
      endif;
      
                if(ContentBuilder::getSetting("breadcrumbs")=="on"):
                        $this->view->params['breadcrumbs'][] = $records->name;
                endif;
      $page['pagination'] = $pagination;
      //render through twig if available
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>'tags/index','renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content = $twig->render($codebase['filename'], ['page'=>$page]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
      return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/tags/index',$page);
           
    }
    public function actionAuthors(){
        $page =[];
        $news_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
        if (strpos($news_url, '?') !== false):
                list($news_url,$search)= explode("?",$news_url);
        endif;
        if(ContentBuilder::getTemplateRouteByURL($news_url)!="tags/authors"):
                $page['records'] = Users::find()->where(['username'=>$news_url])->one();
                //add the breadcrumbs
                if(ContentBuilder::getSetting("breadcrumbs")=="on"):
                        $p = Templates::findOne(['route'=>"tags/authors"]);
                        $this->view->params['breadcrumbs'][] = ['label' => 'Authors', 'url' => Yii::$app->request->getBaseUrl().'/'.$p->url];
                        $this->view->params['breadcrumbs'][] = $page['records']->first_name." ".$page['records']->last_name;
                endif;
                //render through twig if available
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>'authors/details','renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content = $twig->render($codebase['filename'], ['page'=>$page]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
                return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/authors/articles',$page);
            else:
                $pagination=[];
                $pagination['total_page_count']=0;
                $pagination['active_page']=1;
                if(ContentBuilder::getSetting("breadcrumbs")=="on"):
                        $this->view->params['breadcrumbs'][] = 'Authors';
                endif;
                $page['records'] = Users::find()->all();
                $page['pagination'] = $pagination;
                //render through twig if available
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>'tags/authors','renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content = $twig->render($codebase['filename'], ['page'=>$page]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
                return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/authors/index',$page);
        endif;
        
    }
    public function actionArchives(){
        $page =[];
        $pagination=[];
        $pagination['total_page_count']=0;
        $pagination['active_page']=1;
        $page["description"]="";
        $news_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
        if (strpos($news_url, '?') !== false):
                list($news_url,$search)= explode("?",$news_url);
        endif;
        
        if(ContentBuilder::getTemplateRouteByURL($news_url)!="tags/archives"):
            //this handles for when an argument is passed with the archives URL
                $page['title']="Archives"; //will need a way to write this page title. Probably use a different render page
                $records = Articles::find()->where(['archive'=>$news_url])->all();
                if(ContentBuilder::getSetting("breadcrumbs")=="on"):
                    //since its an archive we selecct the first record since it will have the same archive date
                        $this->view->params['breadcrumbs'][] = 'Archives - '.$records[0]->archiveDate;
                endif;
                //render through twig if available
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>'tags/archives','renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content = $twig->render($codebase['filename'], ['page'=>$page,'records'=>$records,'pagination'=>$pagination]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                            
                        endif;
                    endif;
                return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/blog/index',['page'=>$page,'records'=>$records,'pagination'=>$pagination]);
            else:
                return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/standard/error');
            
        endif;
        
    }
    public function actionSearch(){
        $page =[];
        $news_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
        if (strpos($news_url, '?') !== false):
                list($news_url,$search)= explode("?",$news_url);
        endif;
        $pagination=[];
        $pagination['total_page_count']=0;
        $pagination['active_page']=1;
        
        if(ContentBuilder::getTemplateRouteByURL($news_url)!="tags/search"):
            //this handles for when an argument is passed with the archives URL
                $page['title']="Search result for " .$news_url; //will need a way to write this page title. Probably use a different render page
                $records = Articles::find()->where(['like','body_content',$news_url])->all();
                
                $page['description'] = "Search Result for ". $news_url;
                if(ContentBuilder::getSetting("breadcrumbs")=="on"):
                        $this->view->params['breadcrumbs'][] = 'Search - '.$news_url;
                endif;
                return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/blog/index',['page'=>$page,'records'=>$records,'pagination'=>$pagination]);
            else:
                return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/standard/error');
            
        endif;
        
    }
    public function actionRating(){
      //this is used to process AJAX call to rate an article so must validate the csrf
        if (Yii::$app->request->isAjax && Yii::$app->request->post() && !Yii::$app->user->isGuest) {
            $model = RatingDetails::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->attributes = Yii::$app->request->post();
                $model->save();
                return "Rating profile successfully updated";
            else:
                $rating_id = RatingProfileDetails::find()->where(['rating_value'=>Yii::$app->request->post('rating')])->andWhere('profile_id="'.Yii::$app->request->post('rating_profile').'"')->one();
                $forms =  new RatingDetails();
                $forms->setAttribute('id',md5(date("YmdHis")));
                $forms->setAttribute('rating_id',$rating_id->id);
                $forms->setAttribute('target_id',Yii::$app->request->post('target_id'));
                if(!Yii::$app->request->post('target_type')):
                    $forms->setAttribute('target_type','A');
                else:
                    $forms->setAttribute('target_type',Yii::$app->request->post('target_type'));
                endif;
                $forms->setAttribute('rated_by',Yii::$app->user->identity->username);
                $forms->setAttribute('rate_date',date("Y-m-d H:i:s"));
                $forms->save();
                RatingProfileDetails::updateRating(Yii::$app->request->post('rating_profile'),Yii::$app->request->post('target_id'));
                if(Yii::$app->request->post('target_type')=="A"):
                    
                endif;             
                return "Thank you for your rating";
            endif;
        }
        
    }
}

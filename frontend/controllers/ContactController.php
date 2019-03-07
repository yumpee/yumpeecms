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
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use frontend\components\ContentBuilder;
use frontend\models\Pages;
use frontend\models\Twig;
use frontend\models\Templates;
use backend\models\Settings;
use backend\models\Domains;


class ContactController extends Controller{
    public static function allowedDomains()
{
    if(ContentBuilder::getSetting("allow_multiple_domains")=="Yes"):
		return Domains::find()->select('domain_url')->where(['active_stat'=>'Yes'])->column();
	endif;
}

/**
 * @inheritdoc
 */
public function behaviors()
{
    return array_merge(parent::behaviors(), [

        // For cross-domain AJAX request
        'corsFilter'  => [
            'class' => \yii\filters\Cors::className(),
            'cors'  => [
                // restrict access to domains:
                'Origin'                           => static::allowedDomains(),
                'Access-Control-Request-Method'    => ['POST','GET'],
                'Access-Control-Allow-Credentials' => false,
                'Access-Control-Max-Age'           => 3600,                 // Cache (seconds)
            ],
        ],

    ]);
}
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
                    if($article['no_of_views']==null):
                        $views=1;
                    else:
                        $views =   $article['no_of_views'] + 1;                        
                    endif;
                    $article->setAttribute('no_of_views', $views);
                    $article->update(false);
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
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
use frontend\models\Twig;
use frontend\models\Templates;
use frontend\models\Domains;
use frontend\components\FormSubmitAPIBehaviour;
use backend\models\CustomWidget;


class WebserviceController extends Controller{
      
 public function actionIndex(){
     
 }  
 public function actionConnect(){
 /*This function is called as a route from front end requesting to use the Outgoing connections of a web service
  * Parameters can be sent to fine tune out the results of the service is been returned to the client request
  * 
  */     
 $webhook = new \backend\models\WebHook();
 $webhook->setAttribute('end_point',Yii::$app->request->post('end_point'));
 if(Yii::$app->request->post('client_profile')!=null):
     $wc = \backend\models\ServicesOutgoing::find()->where(['name'=>Yii::$app->request->post('client_profile')])->one();
     if($wc==null):
         return "error";
     endif;  
     $webhook->setAttribute('client_profile',$wc->id);     
 endif;    
 $webhook->setAttribute('json_data',Yii::$app->request->post('json_data'));
 $webhook->setAttribute('hook_type',Yii::$app->request->post('hook_type'));
 $webhook->setAttribute('post_type',Yii::$app->request->post('post_type'));
 
 $service = $this->attachBehavior('myhook',new FormSubmitAPIBehaviour());
 $return = $service->connect($webhook,Yii::$app->request->post());
 if(Yii::$app->request->post("return-type")=="json"):
    return $return;
 else:
    if(ContentBuilder::getSetting("twig_template")=="Yes"):
        //we handle the loading of twig template if it is turned on
        $theme_id = ContentBuilder::getSetting("current_theme");
        //$renderer = CustomWidget::find()->where(['name'=>Yii::$app->request->post('response_target')])->one();
        //since we may get the widget we want to use to display the result
        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>Yii::$app->request->post('response_target'),'renderer_type'=>'I'])->one();
        
        if(($codebase!=null)&& ($codebase['code']<>"")):
            $loader = new Twig();
            $twig = new \Twig_Environment($loader);
            $content= $twig->render($codebase['filename'],['app'=>Yii::$app,'webservice'=>json_decode($return)]);
            return $this->renderPartial('@frontend/views/layouts/html',['data'=>$content]);
        else:
            return $return;
        endif;
    endif;
                                    
 endif;  
     
     
 }  
    
}
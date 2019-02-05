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

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\WebHook;


/**
 * Description of WebHookEmailController
 *
 * @author Peter
 */
class WebHookController extends Controller{
    //put your code here
    public function actionIndex(){
        
    }
    public function actionInternal(){
        
        $model = WebHook::find()->where(['form_id'=>Yii::$app->request->post("id")])->andWhere('hook_type="I"')->one();
        if($model!=null):
                
                $model->end_point = Yii::$app->request->post("internal_endpoint");
                $model->json_data = Yii::$app->request->post("internal_json_data");
                $model->save();
            else:
                
                $model = new WebHook();
                $model->form_id = Yii::$app->request->post("id");
                $model->end_point = Yii::$app->request->post("internal_endpoint");
                $model->json_data = Yii::$app->request->post("internal_json_data");
                $model->hook_type="I";
                $model->save();
        endif;
        
        
        return "Internal API saved";
    }
    public function actionExternal(){
        
        $model = WebHook::find()->where(['form_id'=>Yii::$app->request->post("id")])->andWhere('hook_type="E"')->one();
        if($model!=null):
                
                $model->end_point = Yii::$app->request->post("external_endpoint");
                $model->json_data = Yii::$app->request->post("external_json_data");
                $model->client_profile = Yii::$app->request->post("external_profile");
                $model->post_type = Yii::$app->request->post("external_post");
                $model->response_target = Yii::$app->request->post("external_response_target");
                $model->save();
            else:
                
                $model = new WebHook();
                $model->form_id = Yii::$app->request->post("id");
                $model->end_point = Yii::$app->request->post("external_endpoint");
                $model->json_data = Yii::$app->request->post("external_json_data");
                $model->client_profile = Yii::$app->request->post("external_profile");
                $model->post_type = Yii::$app->request->post("external_post");
                $model->response_target = Yii::$app->request->post("external_response_target");
                $model->hook_type="E";
                $model->save();
        endif;
        
        
        return "External API saved";
    }
}

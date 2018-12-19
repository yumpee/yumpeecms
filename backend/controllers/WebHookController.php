<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
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

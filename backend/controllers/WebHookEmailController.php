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
use backend\models\WebHookEmail;


/**
 * Description of WebHookEmailController
 *
 * @author Peter
 */
class WebHookEmailController extends Controller{
    //put your code here
    public function actionIndex(){
        
    }
    public function actionNotify(){
        if(Yii::$app->request->post("notify_send_data")=="on"):
                        $send_data="Y";
                    else:
                        $send_data="N";
                endif;
        $model = WebHookEmail::find()->where(['form_id'=>Yii::$app->request->post("id")])->andWhere('webhook_type="N"')->one();
        if($model!=null):
                
                $model->email = Yii::$app->request->post("notify_email");
                $model->message = Yii::$app->request->post("notify_message");
                $model->subject = Yii::$app->request->post("notify_subject");
                $model->include_data = $send_data;
                $model->save();
            else:
                
                $model = new WebHookEmail();
                $model->form_id = Yii::$app->request->post("id");
                $model->email = Yii::$app->request->post("notify_email");
                $model->message = Yii::$app->request->post("notify_message");
                $model->subject = Yii::$app->request->post("notify_subject");
                $model->include_data = $send_data;
                $model->webhook_type="N";
                $model->save();
        endif;
        
        
        return "Notification saved";
    }
    public function actionForm(){
        if(Yii::$app->request->post("form_send_data")=="on"):
                        $send_data="Y";
                    else:
                        $send_data="N";
                endif;
        $model = WebHookEmail::find()->where(['form_id'=>Yii::$app->request->post("id")])->andWhere('webhook_type="F"')->one();
        if($model!=null):
                
                $model->email = Yii::$app->request->post("form_email");
                $model->message = Yii::$app->request->post("form_message");
                $model->subject = Yii::$app->request->post("form_subject");
                $model->include_data = $send_data;
                $model->save();
            else:
                
                $model = new WebHookEmail();
                $model->form_id = Yii::$app->request->post("id");
                $model->email = Yii::$app->request->post("form_email");
                $model->message = Yii::$app->request->post("form_message");
                $model->subject = Yii::$app->request->post("form_subject");
                $model->include_data = $send_data;
                $model->webhook_type="F";
                $model->save();
        endif;
        
        
        return "Notification saved";
    }
    public function actionResponse(){
        if(Yii::$app->request->post("response_send_data")=="on"):
                        $send_data="Y";
                    else:
                        $send_data="N";
                endif;
        $model = WebHookEmail::find()->where(['form_id'=>Yii::$app->request->post("id")])->andWhere('webhook_type="R"')->one();
        if($model!=null):
                
                $model->email = Yii::$app->request->post("response_email");
                $model->message = Yii::$app->request->post("response_message");
                $model->subject = Yii::$app->request->post("response_subject");
                $model->include_data = $send_data;
                $model->save();
            else:
                
                $model = new WebHookEmail();
                $model->form_id = Yii::$app->request->post("id");
                $model->email = Yii::$app->request->post("response_email");
                $model->message = Yii::$app->request->post("response_message");
                $model->subject = Yii::$app->request->post("response_subject");
                $model->include_data = $send_data;
                $model->webhook_type="R";
                $model->save();
        endif;
        
        
        return "Notification saved";
    }
}

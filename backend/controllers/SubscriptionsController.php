<?php

/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Each line should be prefixed with  * 
 */



namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\Subscriptions;


class SubscriptionsController extends Controller{

    public function actionIndex(){
        $page['id']= Yii::$app->request->get('id',null);
        if($page['id']!=null):
                $page['rs'] = Subscriptions::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = Subscriptions::find()->where(['id' => "0"])->one();
        endif;
        $page['records'] = Subscriptions::find()->orderBy('name')->all();
        return $this->render('index',$page);
    }
    
    public function actionSave(){
            $model = Subscriptions::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->attributes = Yii::$app->request->post();
                $model->save();
                return "Subscription successfully updated";
            else:
                $subscriptions =  new Subscriptions();
                $subscriptions->attributes = Yii::$app->request->post();
                $subscriptions->save();
                return "New subscription created";
            endif;
    }
    public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Subscriptions::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
    }

}

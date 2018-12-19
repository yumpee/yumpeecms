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
use backend\models\Language;


class LanguageController extends Controller{

    public function actionIndex(){
        $page['id']= Yii::$app->request->get('id',null);
        if($page['id']!=null):
                $page['rs'] = Language::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = Language::find()->where(['id' => "0"])->one();
        endif;
        $page['records'] = Language::find()->orderBy('name')->all();
        return $this->render('index',$page);
    }
    
    public function actionSave(){
            $model = Language::findOne(Yii::$app->request->post("id"));
            if($model!=null):                
                $model->setAttribute('name',Yii::$app->request->post('name'));
                $model->setAttribute('code',Yii::$app->request->post('code'));
                $model->setAttribute('description',Yii::$app->request->post('description'));
                $model->save();
                return "Language Profile successfully updated";
            else:
                $model =  new Language();
                $id = md5(date('YmdHis').rand(1000,10000));
                $model->setAttribute('id',$id);
                $model->setAttribute('name',Yii::$app->request->post('name'));
                $model->setAttribute('code',Yii::$app->request->post('code'));
                $model->setAttribute('description',Yii::$app->request->post('description'));
                
                $model->save();
                return "New Language created";
            endif;
    }
    public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Language::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
    }

}

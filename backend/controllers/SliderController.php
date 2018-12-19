<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\Slider;


class SliderController extends Controller{

    public function actionIndex(){
        $page['id']= Yii::$app->request->get('id',null);
        if($page['id']!=null):
                $page['rs'] = Slider::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = Slider::find()->where(['id' => "0"])->one();
        endif;
        $page['records'] = Slider::find()->orderBy('name')->all();
        return $this->render('index',$page);
    }
    
    public function actionSave(){
            $model = Slider::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->attributes = Yii::$app->request->post();
                $model->save();
                Slider::updateSliderImage(Yii::$app->request->post("id"));
                return "Slider successfully updated";
            else:
                $slider =  new Slider();
                $slider->attributes = Yii::$app->request->post();
                $slider->setAttribute('description',Yii::$app->request->post("description"));
                $slider->save();
                return "New Slider created";
            endif;
    }
    public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Slider::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
    }
    public function actionDeleteSlideImage(){
    Slider::deleteSliderImage();
    echo "Record successfully deleted";
}
}


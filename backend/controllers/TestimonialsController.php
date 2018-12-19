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
use backend\models\Testimonials;


class TestimonialsController extends Controller{

    public function actionIndex(){
        $page['id']= Yii::$app->request->get('id',null);
        if($page['id']!=null):
                $page['rs'] = Testimonials::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = Testimonials::find()->where(['id' => "0"])->one();
        endif;
        $page['records'] = Testimonials::find()->orderBy('name')->all();
        return $this->render('index',$page);
    }
    
    public function actionSave(){
            $model = Testimonials::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->attributes = Yii::$app->request->post();
                $model->save();
                return "Testimonial successfully updated";
            else:
                $testimonials =  new Testimonials();
                $testimonials->attributes = Yii::$app->request->post();
                $testimonials->save();
                return "New testimonial created";
            endif;
    }
    public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Testimonials::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
    }

}

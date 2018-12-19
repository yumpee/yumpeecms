<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\CSS;


class CssController extends Controller{

    public function actionIndex(){
        $page['id']= Yii::$app->request->get('id',null);
        if($page['id']!=null):
                $page['rs'] = CSS::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = CSS::find()->where(['id' => "0"])->one();
        endif;
        $page['records'] = CSS::find()->orderBy('name')->all();
        return $this->render('index',$page);
    }
    
    public function actionSave(){
            $model = CSS::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->attributes = Yii::$app->request->post();
                $model->save();
                return "CSS Profile successfully updated";
            else:
                $model =  new CSS();
                $model->attributes = Yii::$app->request->post();
                $model->save();
                return "New CSS created";
            endif;
    }
    public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = CSS::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
    }

}

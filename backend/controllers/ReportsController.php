<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\controllers;

/**
 * Description of ReportController
 *
 * @author Peter
 */
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use backend\models\Reports;

class ReportsController extends Controller {
    //put your code here
    public function actionIndex(){
        
        
    }
    public function actionSetup(){
        $page=[];
        $page=[];
        $page['id']= Yii::$app->request->get('id',null);
        if($page['id']!=null):
                $page['rs'] = Reports::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = new Reports();
        endif;
        $page['records'] = Reports::find()->orderBy('name')->all();
        return $this->render('setup',$page);
    }
    public function actionList(){
        $page=[];
        $page['records'] = Reports::find()->orderBy('name')->all();
        return $this->render('list',$page);
    }
    public function actionLogs(){
        $page=[];
        $page['frontend'] = file_get_contents("../../frontend/runtime/logs/app.log");        
        return $this->render('logs',$page);
    }
    public function actionSetupSave(){
        $model = Reports::findOne(Yii::$app->request->post("id"));
            if($model!=null):                
                $model->setAttribute('name',Yii::$app->request->post('name'));
                $model->setAttribute('alias',Yii::$app->request->post('alias'));
                $model->setAttribute('description',Yii::$app->request->post('description'));                
                $model->save();
                return "Report successfully updated";
            else:
                $model =  new Reports();
                $model->setAttribute('id',md5(date('YmHid').rand(1000,10000)));
                $model->setAttribute('name',Yii::$app->request->post('name'));
                $model->setAttribute('alias',Yii::$app->request->post('alias'));
                $model->setAttribute('description',Yii::$app->request->post('description'));
                $model->save();
                return "New report created";
            endif;
    }
    public function actionSetupDelete(){
        Reports::deleteAll(['id'=>Yii::$app->request->get('id')]);
    }
}

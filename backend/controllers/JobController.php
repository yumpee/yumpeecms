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
use backend\models\Job;


class JobController extends Controller{

public function actionIndex()
    {
    $page=[]; 
    $page['name']="";
    $page['id']="";
    
    $page['id'] = Yii::$app->request->get('id',null);
        if($page['id']!=null){            
            $rs = Job::find()->where(['id' => $page['id']])->all();
            $page['edit']=true;
            if(count($rs)>0):
                $page['name']=$rs[0]['name'];
                
            endif;
        }
    
    $page['records'] = Job::find()->all();
    return $this->render('index',$page); 
   }
   
public function actionSave(){
    if(Yii::$app->request->post("processor")=="true"){
            echo Job::saveGroup();                        
    }
}
public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Job::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
}
}
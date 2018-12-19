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
use common\models\LoginForm;
use backend\models\Tags;
use backend\models\TagTypes;
use fedemotta\datatables\DataTables;

class TagsController extends Controller{

public function actionIndex()
    {
        //EventsCategories::saveEventsCategory();
        $b = new Tags();        
        $page=[]; 
        $page['rs']=[];
        
        $page['id'] = Yii::$app->request->get('id',null);
        
        if($page['id']!=null){            
            $page['rs'] = Tags::find()->where(['id' => $page['id']])->one();
            $page['edit']=true;
            //echo $page['recordset']['name'];
        }else{
            $page['rs'] = Tags::find()->where(['id' => '0'])->one();
        }
        if(isset($page['rs']['published'])){
            if($page['rs']['published']=='1'){
                $page['published'] = \yii\helpers\Html::checkbox("published",true);
            }else{
                $page['published'] = \yii\helpers\Html::checkbox("published",false);
            }
        }else{
            $page['published'] = \yii\helpers\Html::checkbox("published");
        }
        
        $page['records'] = Tags::getTags();        
        
        return $this->render('index',$page);        
    }

public function actionSave(){
    //insert and update
    if(Yii::$app->request->post("processor")=="true"){            
            echo Tags::saveTags();                        
    }
}
public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));   
    $a = Tags::findOne($id);
    $a->delete();
    return "Record successfully deleted";
}
public function actionTypes(){
    $page=[];
    $page['name']="";
    $page['id'] = Yii::$app->request->get('id');
    $page['selected_tags']=[];
    if($page['id']){            
            $rs = TagTypes::find()->where(['id' => $page['id']])->one();
            $page['name'] = $rs['name'];
            $page['edit']=true;
    }else{
            $rs = TagTypes::find()->where(['id' => '0'])->one();
    }
    $page['selected_tags'] = TagTypes::getSelectedTags();
    $page['records']=Tags::getTagTypes();
    
    
    return $this->render('tag-types',$page);
}
public function actionSearchTags(){
    //this is called via ajax from the view and returns the list of tags    
    $result="";
    $a = Tags::getTags();
    foreach($a as $c){
        $result.= "<option value='".$c['id']."'>".$c['name']."</option>";
    }
    return $result;
}
public function actionSaveTag(){
    if(Yii::$app->request->post("processor")=="true"){
        echo Tags::saveTagType();
    }
}
public function actionDeleteTag(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = TagTypes::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
}
}

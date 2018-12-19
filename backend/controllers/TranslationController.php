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
use backend\models\Translation;
use backend\models\Language;
use backend\models\TranslationCategory;
use fedemotta\datatables\DataTables;

class TranslationController extends Controller
{
public function actionIndex(){
    //EventsCategories::saveEventsCategory();
        $b = new Translation();        
        $page=[]; 
        $page['rs']=[];
        
        $page['id'] = Yii::$app->request->get('id',null);
        $page['cat_id'] = Yii::$app->request->get('cat_id',null);
        
        if($page['id']!=null){            
            $page['rs'] = Translation::find()->where(['id' => $page['id']])->one();
            $page['edit']=true;
            $page['translation'] = Translation::getTranslation($page['id']);
        }else{
            $page['rs'] = Translation::find()->where(['id' => '0'])->one();
            $page['translation'] = Translation::getTranslation('0');
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
        if($page['cat_id']==null):
                $page['rscat'] = new TranslationCategory();
            else:
                $page['rscat'] = TranslationCategory::find()->where(['id'=>$page['cat_id']])->one();
        endif;
        
        $page['records'] = Translation::find()->all(); 
        $language = Language::find()->orderBy('name')->all();
        $page['language'] = \yii\helpers\ArrayHelper::map($language,'code','name');
        $category = TranslationCategory::find()->orderBy('alias')->all();        
        $page['category'] = \yii\helpers\ArrayHelper::map($category,'name','alias');
        $page['category_list'] = $category;
        return $this->render('index',$page);        
}
public function actionSave(){
    //insert and update
    
    if(Yii::$app->request->post("processor")=="true"){             
            echo Translation::saveTranslation();                        
    }
}

public function actionSaveCategory(){
    $model = TranslationCategory::findOne(Yii::$app->request->post("category_id"));
            if($model!=null):
                $model->setAttribute('name',Yii::$app->request->post('name'));
                $model->setAttribute('alias',Yii::$app->request->post('alias'));
                $model->setAttribute('description',Yii::$app->request->post('description'));
                $model->save();
                return "Category successfully updated";
            else:
                $model =  new TranslationCategory();
                $id = md5(date('YmdHis').rand(1000,10000));
                $model->setAttribute('id',$id);
                $model->setAttribute('name',Yii::$app->request->post('name'));
                $model->setAttribute('alias',Yii::$app->request->post('alias'));
                $model->setAttribute('description',Yii::$app->request->post('description'));
                $model->save();
                return "New category created";
            endif;
}
public function deleteCategory(){
    TranslationCategory::deleteAll(['id'=>Yii::$app->request->post("id")]);
}
}
<?php

/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Project Site : http://www.yumpeecms.com


 * YumpeeCMS is a Content Management and Application Development Framework.
 *  Copyright (C) 2018  Audmaster Technologies, Australia
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.

 */



namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\Subscriptions;
use backend\models\SubscriptionCategory;


class SubscriptionsController extends Controller{

    public function actionIndex(){
        $page['id']= Yii::$app->request->get('id',null);
        $page['cat_id']= Yii::$app->request->get('cat_id',null);
        if($page['id']!=null):
                $page['rs'] = Subscriptions::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = Subscriptions::find()->where(['id' => "0"])->one();
        endif;
        if($page['cat_id']!=null):
                $page['rs_category'] = SubscriptionCategory::find()->where(['id' => $page['cat_id']])->one();
            else:
                $page['rs_category'] = SubscriptionCategory::find()->where(['id' => "0"])->one();
        endif;
        $page['records'] = Subscriptions::find()->orderBy('name')->all();
        $page['category'] = SubscriptionCategory::find()->orderBy('name')->all();        
        $category_map =  yii\helpers\ArrayHelper::map($page['category'], 'id', 'name');
        $page['category_dropdown'] = \yii\helpers\Html::dropDownList("category_id",$page['rs']['category_id'],$category_map,['prompt'=>'Select a category','class'=>'form-control']);
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
    public function actionSaveCategory(){
            $model = SubscriptionCategory::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->setAttribute("name",Yii::$app->request->post("name"));
                $model->setAttribute("description",Yii::$app->request->post("description"));
                $model->save();
                return "Subscription category successfully updated";
            else:
                $subscriptions =  new SubscriptionCategory();
                $id = md5(date('YHmis').rand(1000,100000));
                $subscriptions->setAttribute("id",$id);
                $subscriptions->setAttribute("name",Yii::$app->request->post("name"));
                $subscriptions->setAttribute("description",Yii::$app->request->post("description"));
                $subscriptions->save();
                return "New subscription category created";
            endif;
    }
    public function actionDeleteCategory(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = SubscriptionCategory::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
    }
}

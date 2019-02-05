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

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
use backend\models\Slider;
use backend\models\BackEndMenus;
use backend\models\BackEndMenuRole;
use backend\models\Settings;

class SliderController extends Controller{
public function behaviors()
{
    if(Settings::find()->where(['setting_name'=>'use_custom_backend_menus'])->one()->setting_value=="on" && !Yii::$app->user->isGuest):
    $can_access=1;
    $route = "/".Yii::$app->request->get("r");
    //check to see if route exists in our system
    $menu_rec = BackEndMenus::find()->where(['url'=>$route])->one();
    if($menu_rec!=null):
        //we now check that the current role has rights to use it
        $role_access = BackEndMenuRole::find()->where(['menu_id'=>$menu_rec->id,'role_id'=>Yii::$app->user->identity->role_id])->one();
        if(!$role_access):
            //let's take a step further if there is a custom module
            $can_access=0;            
        endif;
    endif;
    if($can_access < 1):
        echo "You do not have permission to view this page";
        exit;
    endif;
    endif;
    
    return [
        'access' => [
            'class' => \yii\filters\AccessControl::className(),
            'only' => ['create', 'update'],
            'rules' => [
                // deny all POST requests
                [
                    'allow' => false,
                    'verbs' => ['POST']
                ],
                // allow authenticated users
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
                // everything else is denied
            ],
        ],
    ];
}  
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


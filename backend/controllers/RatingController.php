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
use backend\models\RatingProfile;
use backend\models\RatingProfileDetails;
use backend\models\BackEndMenus;
use backend\models\BackEndMenuRole;
use backend\models\Settings;

class RatingController extends Controller{
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
        $page['details_id']= Yii::$app->request->get('details_id',null);
        if($page['id']!=null):
                $page['rs'] = RatingProfile::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = RatingProfile::find()->where(['id' => "0"])->one();
        endif;
        if($page['details_id']!=null):
                $page['rs_details'] = RatingProfileDetails::find()->where(['id' => $page['details_id']])->one();
            else:
                $page['rs_details'] = RatingProfileDetails::find()->where(['id' => "0"])->one();
        endif;
        $page['records'] = RatingProfile::find()->orderBy('name')->all();
        $page['record_details'] = RatingProfileDetails::find()->orderBy('profile_id')->all();
        
        $rating_profile_map = yii\helpers\ArrayHelper::map($page['records'], 'id', 'title');
        $page['rating_profile'] =  \yii\helpers\Html::dropDownList("profile_id",$page['rs_details']['profile_id'],$rating_profile_map,['prompt'=>'Select a profile','class'=>'form-control']);
        return $this->render('index',$page);
    }
    public function actionSaveProfile(){
            $model = RatingProfile::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->setAttribute('description',Yii::$app->request->post('description'));
                $model->setAttribute('title',Yii::$app->request->post('title'));
                $model->setAttribute('name',Yii::$app->request->post('name'));  
                $model->setAttribute('default_label',Yii::$app->request->post('title'));
                $model->save();
                return "Rating profile successfully updated";
            else:
                $forms =  new RatingProfile();
                $forms->setAttribute('description',Yii::$app->request->post('description'));
                $forms->setAttribute('title',Yii::$app->request->post('title'));
                $forms->setAttribute('name',Yii::$app->request->post('name'));
                $forms->setAttribute('default_label',Yii::$app->request->post('title'));
                $forms->save();
                return "New Rating Profile created";
            endif;
    }
    public function actionSaveProfileDetails(){
            $model = RatingProfileDetails::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->setAttribute('profile_id',Yii::$app->request->post('profile_id'));
                $model->setAttribute('rating_name',Yii::$app->request->post('rating_name'));
                $model->setAttribute('rating_value',Yii::$app->request->post('rating_value'));
                $model->setAttribute('rating_rgb_color',Yii::$app->request->post('rating_rgb_color'));
                $model->save();
                return "Profile details successfully updated";
            else:
                $forms =  new RatingProfileDetails();
                $forms->setAttribute('profile_id',Yii::$app->request->post('profile_id'));
                $forms->setAttribute('rating_name',Yii::$app->request->post('rating_name'));
                $forms->setAttribute('rating_value',Yii::$app->request->post('rating_value'));
                $forms->setAttribute('rating_rgb_color',Yii::$app->request->post('rating_rgb_color'));
                $forms->save();
                return "New Profile details created";
            endif;
    }
    public function actionDeleteRating(){
        $id = str_replace("}","",Yii::$app->request->get("id"));    
        $a = RatingProfileDetails::deleteAll(['profile_id'=>Yii::$app->request->get("id")]);
        $a = RatingProfile::findOne($id);
        $a->delete();
    echo "Record successfully deleted";
    }
    public function actionDeleteDetails(){
        $id = str_replace("}","",Yii::$app->request->get("id"));    
        $a = RatingProfileDetails::findOne($id);
        $a->delete();
    echo "Record successfully deleted";
    }
}

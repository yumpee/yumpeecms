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
use backend\models\Domains;
use backend\models\MenuProfile;
use backend\models\Themes;
use yii\Helpers\ArrayHelper;
use backend\models\BackEndMenus;
use backend\models\BackEndMenuRole;
use backend\models\Settings;

class DomainsController extends Controller{
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
        $page =[];
        $page['id']= Yii::$app->request->get('id',null);
        if($page['id']!=null):
                $page['rs'] = Domains::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = Domains::find()->where(['id' => "0"])->one();
        endif;
        $setup_list = Domains::find()->orderBy('name')->all();
        $page['setup_list'] = ArrayHelper::map($setup_list, 'id', 'name');
        $tag_map =  yii\helpers\ArrayHelper::map(MenuProfile::find()->all(), 'id', 'name');
        $page['menu_profile'] = \yii\helpers\Html::dropDownList("menu_id",$page['rs']['menu_id'],$tag_map,['prompt'=>'System:Default','class'=>'form-control']);
        $tag_map =  yii\helpers\ArrayHelper::map(Themes::find()->orderBy('name')->all(), 'id', 'name');
        $page['theme_profile'] = \yii\helpers\Html::dropDownList("theme_id",$page['rs']['theme_id'],$tag_map,['prompt'=>'Select Theme','class'=>'form-control']);
        $page['records'] = Domains::find()->orderBy('name')->all();
        return $this->render('index',$page);
    }
    public function actionSave(){
            $model = Domains::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->attributes = Yii::$app->request->post();
                $model->name=Yii::$app->request->post("name");
                $model->domain_url = Yii::$app->request->post("domain_url");
                $model->theme_id=Yii::$app->request->post("theme_id");
                $model->menu_id=Yii::$app->request->post("menu_id");
                $model->active_stat=Yii::$app->request->post("active_stat");
                $model->description=Yii::$app->request->post("description");                
                $model->save();
                return "Domain profile successfully updated";
            else:
                $model =  new Domains();
                $model->id = md5(date("Hmdis").rand(1000,10000));
                $model->name=Yii::$app->request->post("name");
                $model->domain_url = Yii::$app->request->post("domain_url");
                $model->theme_id=Yii::$app->request->post("theme_id");
                $model->menu_id=Yii::$app->request->post("menu_id");
                $model->description=Yii::$app->request->post("description");  
                $model->active_stat=Yii::$app->request->post("active_stat");
                $model->save();
                return "New domain profile created";
            endif;
    }
    public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Domains::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
    }
    
}

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
use backend\models\Menus;
use backend\models\MenuProfile;
use backend\models\BackEndMenus;
use backend\models\BackEndMenuRole;
use backend\models\Settings;

class MenusController extends Controller{
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
public function actionIndex()
    {
        $page=[];      
        
        if(Yii::$app->request->get('profile')==null || Yii::$app->request->get('profile')=="0"):
            $page['active_menu'] = Menus::getActiveMenus();
            $page['inactive_menu'] = Menus::getInActiveMenus();
        else:
            $page['active_menu'] = Menus::getActiveMenus(Yii::$app->request->get('profile'));
            $page['inactive_menu'] = Menus::getInActiveMenus(Yii::$app->request->get('profile'));
        endif;
        $page['footer_active_menu'] = Menus::getFooterActiveMenus();
        $page['footer_inactive_menu'] = Menus::getFooterInActiveMenus();
        $page['records'] = MenuProfile::find()->all();
        if(Yii::$app->request->get("actions")=="edit_menu"):
            $page['menu_rs'] = MenuProfile::find()->where(['id'=>Yii::$app->request->get('menu_id')])->one();
        else:
            $page['menu_rs'] = MenuProfile::find()->where(['id'=>'0'])->one();
        endif;
        $page["submenu"] = Yii::$app->request->get("submenu","0");
        return $this->render('index',$page);        
    }

public function actionSave(){
    //insert and update
    if(Yii::$app->request->post("processor")=="true"){ 
            if((Yii::$app->request->post("top_profile")!="")&&(Yii::$app->request->post("top_profile")!="0")):
                    echo Menus::saveMenus(Yii::$app->request->post("top_profile"));
                else:
                    echo Menus::saveMenus();
            endif;
                                    
    }
}
public function actionSaveFooter(){
    //insert and update
    if(Yii::$app->request->post("processor")=="true"){            
            echo Menus::saveFooterMenus();                        
    }
}
public function actionSaveProfile(){
    
    $model = MenuProfile::findOne(Yii::$app->request->post("menu_id"));
            if($model!=null):
                $model->attributes = Yii::$app->request->post();
                $model->save();
                return "Menu successfully updated";
            else:
                $model =  new MenuProfile();
                $model->attributes = Yii::$app->request->post();
                $model->save();
                return "New menu created";
            endif;
}
public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = MenuProfile::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
}
}

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

class MenusController extends Controller{

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

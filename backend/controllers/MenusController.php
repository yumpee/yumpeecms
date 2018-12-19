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

}

<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\controllers;

/**
 * Description of BackendController
 *
 * @author Peter
 */
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\BackEndMenus;
use backend\models\BackEndMenuRole;
use backend\models\Roles;


class BackendController extends Controller {
    //put your code here
    public function actionIndex(){
       $page=[];
       
       $page['id'] = Yii::$app->request->get('id',null);
       if($page['id']!=null):
            $page['rs'] = BackEndMenus::find()->where(['id' => $page['id']])->one(); 
       else:
           $page['rs'] = new BackEndMenus();
       endif;
       
       $menus = BackEndMenus::find()->orderBy('label')->all();
       $menu_map = yii\helpers\ArrayHelper::map($menus, 'id', 'name');
       $page['parent_menus'] = \yii\helpers\Html::dropDownList("parent_id",$page['rs']['parent_id'],$menu_map,['prompt'=>'Select a menu','class'=>'form-control']);
       $page['records']= BackEndMenus::find()->orderBy('label')->all();
       
       $roles = Roles::find()->where(['access_type'=>'B'])->all();
       $roles_map = yii\helpers\ArrayHelper::map($roles, 'id', 'name');
       if(Yii::$app->request->get("role_id")!=null):                
                $page['roles'] = \yii\helpers\Html::dropDownList("role_id",Yii::$app->request->get("role_id"),$roles_map,['prompt'=>'Select a role','class'=>'form-control','id'=>'role_id']);
           else:
                $page['roles'] = \yii\helpers\Html::dropDownList("role_id",NULL,$roles_map,['prompt'=>'Select a role','class'=>'form-control','id'=>'role_id']);
       endif;
       
       $menus = BackEndMenus::find()->orderBy('parent_id')->all();
       $menu_map = yii\helpers\ArrayHelper::map($menus, 'id', 'name');
       if(Yii::$app->request->get("role_id")!=null): 
                $selected = BackEndMenuRole::find()->select('menu_id')->where(['role_id'=>Yii::$app->request->get("role_id")])->column();
                $page['menus_list'] = \yii\helpers\Html::checkboxList("menu_permission_id",$selected,$menu_map);
           else:
               $page['menus_list'] = \yii\helpers\Html::checkboxList("menu_permission_id",NULL,$menu_map);
       endif;
       
       
       return $this->render("index",$page); 
    }
    public function actionSave(){
        $model = BackEndMenus::findOne(Yii::$app->request->post("id"));
    if($model!=null): 
        $id = Yii::$app->request->post("id");
        $model->setAttribute("label",Yii::$app->request->post("label"));
        $model->setAttribute("icon",Yii::$app->request->post("icon"));
        if($model['custom_stat']=="Y"):
            $model->setAttribute("url",Yii::$app->request->post("url")); //we can only change the url if we added it ourselves
        endif;
        $model->setAttribute("parent_id",Yii::$app->request->post("parent_id"));
        $model->setAttribute("priority",Yii::$app->request->post("priority"));
        $model->save();
        return "Menu successfully updated";
    else: 
        $model = new BackEndMenus();
        $id=md5(date("Hmis").rand(1000,10000));        
        $model->setAttribute("id",$id);
        $model->setAttribute("label",Yii::$app->request->post("label"));
        $model->setAttribute("icon",Yii::$app->request->post("icon"));
        $model->setAttribute("url",Yii::$app->request->post("url"));
        $model->setAttribute("parent_id",Yii::$app->request->post("parent_id"));
        $model->setAttribute("priority",Yii::$app->request->post("priority"));
        $model->setAttribute("custom_stat","Y");
        //$model->setAttribute("original_label",Yii::$app->request->post("label"));
        $model->save();
        return "Menus successfully created";
    endif;
        
        
    }
    public function actionApply(){
        BackEndMenuRole::deleteAll(['IN','role_id',Yii::$app->request->post("role_id")]);
        
        
        foreach(Yii::$app->request->post("menu_permission_id") as $menu_id):
            $model = new BackEndMenuRole();
            $id=md5(date("Hmis").rand(1000,10000));
            $model->setAttribute("id",$id);
            $model->setAttribute("role_id",Yii::$app->request->post("role_id"));
            $model->setAttribute("menu_id",$menu_id);
            $model->save();
        endforeach;
        
        return "Permission successfully applied";
    }
}

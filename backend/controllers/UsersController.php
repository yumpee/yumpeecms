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
use backend\models\Users;
use backend\models\Roles;
use backend\models\MenuProfile;
use fedemotta\datatables\DataTables;
use yii\Helpers\ArrayHelper;

class UsersController extends Controller{

public function actionIndex()
    {
        $b = new Users();        
        $page=[]; 
        $page['rs']=[];        
        $page['id'] = Yii::$app->request->get('id',null);
        
        if($page['id']!=null){            
            $page['rs'] = Users::find()->where(['id' => $page['id']])->one();
            $page['edit']=true;
            //echo $page['recordset']['name'];
        }else{
            $page['rs'] = Users::find()->where(['id' => '0'])->one();
        }
        if(Yii::$app->request->get("actions")=="edit_roles"):
            $page['role_rs'] = Roles::find()->where(['id'=>Yii::$app->request->get('role_id')])->one();
        else:
            $page['role_rs'] = Roles::find()->where(['id'=>'0'])->one();
        endif;
        if(isset($page['rs']['published'])){
            if($page['rs']['published']=='1'){
                $page['published'] = \yii\helpers\Html::checkbox("published",true);
            }else{
                $page['published'] = \yii\helpers\Html::checkbox("published",false);
            }
        }else{
            $page['published'] = \yii\helpers\Html::checkbox("published");
        }
        
        $role_list = Roles::find()->all();
        $role_map =  yii\helpers\ArrayHelper::map($role_list, 'id', 'name');
        $page['role_dropdown'] = \yii\helpers\Html::dropDownList("role_id",$page['rs']['role_id'],$role_map,['prompt'=>'Select a role','class'=>'form-control']);
        $page['role_parent_dropdown'] = \yii\helpers\Html::dropDownList("parent_role_id",$page['role_rs']['parent_role_id'],$role_map,['prompt'=>'Select parent role','class'=>'form-control']);
        $page['access_type']=$page['role_rs']['access_type'];
     
        $menu_list = MenuProfile::find()->orderBy('name')->all();
        $menu= ArrayHelper::map($menu_list, 'id', 'name');
        $page['menu_list'] = \yii\helpers\Html::dropDownList("menu_id",$page['role_rs']['menu_id'],$menu,['prompt'=>'Select a default menu','class'=>'form-control']);
        
        $page['records'] = Users::find()->orderBy('first_name')->all(); 
        $page['roles'] = Roles::find()->orderBy('name')->all();
        return $this->render('index',$page);        
    }

public function actionSaveRegions(){
    return Users::saveRegions();
}
public function actionUpdateRegion(){
        //this function is used as a AJAX service
        if(!isset(Yii::$app->user->id)){
            echo "Service Not available";
            return;
        }
        if((Yii::$app->request->get('new_database'))==null){
            echo "Service Not available";
            return;
        }
        $new_region = Yii::$app->request->get('new_database');
        $session = Yii::$app->session;
        $session['mydatabase'] = $new_region;
        return "New Region - ".$new_region." saved";
}

public function actionSave(){
    //insert and update
    
    if(Yii::$app->request->post("processor")=="true"){   
            
            echo Users::saveUsers();                        
    }
}
public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Users::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
}
public function actionSaveRole(){
            $model = Roles::findOne(Yii::$app->request->post("role_id"));
            if($model!=null):
                $model->attributes = Yii::$app->request->post();
                $model->save();
                return "Roles successfully updated";
            else:
                $themes =  new Roles();
                $themes->attributes = Yii::$app->request->post();
                $themes->setAttribute('id',substr(md5(date("YmdHis")),30));
                $themes->save();
                return "New role created";
            endif;
    }
    public function actionDeleteRole(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Roles::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
    }
}

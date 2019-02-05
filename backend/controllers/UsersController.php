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
        if($menu_list==null):
            $c = new MenuProfile();
            $c->setAttribute("name","Custom");
            $c->setAttribute("description","");
            $c->save();
            $menu_list = MenuProfile::find()->orderBy('name')->all();
        endif;
        $menu= ArrayHelper::map($menu_list, 'id', 'name');
        $page['menu_list'] = \yii\helpers\Html::dropDownList("menu_id",$page['role_rs']['menu_id'],$menu,['prompt'=>'Select a default menu','class'=>'form-control']);
        
        $page['records'] = Users::find()->orderBy('first_name')->all(); 
        $page['roles'] = Roles::find()->orderBy('name')->all();
        return $this->render('index',$page);        
    }
public function actionProfile(){
    $page=[];
    $page=[]; 
        $page['rs']=[];        
        $page['id'] = Yii::$app->user->identity->id;
        
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
    return $this->render('profile',$page);
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
public function actionSaveProfile(){
    $records = Users::find()->where(['id'=>Yii::$app->user->identity->id])->one();
        $password = Yii::$app->request->post('passwd');
    $id = Yii::$app->request->post("id");    
            $records->setAttribute('first_name',Yii::$app->request->post("first_name"));
            $records->setAttribute('last_name',Yii::$app->request->post("last_name"));
            $records->setAttribute('extension',Yii::$app->request->post("extension"));
            $records->setAttribute('updated_at',time());
            $records->setAttribute('email',Yii::$app->request->post("email"));
            $records->setAttribute('about',Yii::$app->request->post("about"));
            $records->setAttribute('title',Yii::$app->request->post("title"));
            $records->setAttribute('display_image_id',Yii::$app->request->post("display_image_id"));
           //if the password has changed
           if($records['password_hash']<>Yii::$app->request->post('passwd')):
               $records->setAttribute('password_hash',Yii::$app->security->generatePasswordHash($password));
           endif;
            $records->save();
            return "Updates successfully made";
    
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

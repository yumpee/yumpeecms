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
use backend\models\ProfileDetails;
use backend\models\UserProfileFiles;
use backend\models\Roles;
use backend\models\MenuProfile;
use backend\models\Pages;
use fedemotta\datatables\DataTables;
use yii\Helpers\ArrayHelper;
use backend\models\BackEndMenus;
use backend\models\BackEndMenuRole;
use backend\models\Settings;

class UsersController extends Controller{
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
        $page_list = Pages::find()->orderBy('menu_title')->all();
        $page_map = yii\helpers\ArrayHelper::map($page_list, 'id', 'menu_title'); 
        $page['role_dropdown'] = \yii\helpers\Html::dropDownList("role_id",$page['rs']['role_id'],$role_map,['prompt'=>'Select a role','class'=>'form-control']);
        $page['home_page_dropdown'] = \yii\helpers\Html::dropDownList("home_page",$page['role_rs']['home_page'],$page_map,['prompt'=>'Select a page','class'=>'form-control']);
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
public function actionSaveProfileDetails(){    
    foreach($_POST as $key => $value)
                        {
                                //if there are more fields in this form, we should extend the information and store in the data model
                                $a = ProfileDetails::deleteAll(['profile_id'=>Yii::$app->request->post("account_id"),'param'=>$key]);
                                if($value<>""):
                                    if($key=="password"):
                                        //we cannot store the password
                                        continue;
                                    endif;
                                    $profile_data = new ProfileDetails();
                                    $profile_data->setAttribute("profile_id",Yii::$app->request->post("account_id"));
                                    $profile_data->setAttribute("param",$key);
                                    $profile_data->setAttribute("param_val",$value);
                                    $profile_data->save();
                                endif;
                        }
    return "Details saved";
}
public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Users::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
}
public function actionSaveRole(){
            $model = Roles::findOne(Yii::$app->request->post("role_id"));
            if(Yii::$app->request->post("menu_id")==null):
                    $menu_id=0;
                else:
                    $menu_id = Yii::$app->request->post("menu_id");
                endif;
            if($model!=null):                
                $model->attributes = Yii::$app->request->post();
                $model->setAttribute('menu_id',$menu_id);
                $model->save();
                return "Roles successfully updated";
            else:   
                $themes =  new Roles();
                $themes->attributes = Yii::$app->request->post();
                $themes->setAttribute('id',substr(md5(date("YmdHis")),0,30));
                $themes->setAttribute('menu_id',$menu_id);
                $themes->save();
                return "New role created";
            endif;
    }
    public function actionDeleteRole(){
    //first we check to see
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Roles::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
    }
    
    public function actionDetails(){
        $page=[];
        $page['account'] = Users::find()->where(['id'=>Yii::$app->request->get("user")])->one();
        $page['records'] = ProfileDetails::find()->where(['profile_id'=>Yii::$app->request->get("user")])->all();
        return $this->renderPartial("details",$page);
    }
    public function actionFiles(){
        $page=[];
        $page['account'] = Users::find()->where(['id'=>Yii::$app->request->get("user")])->one();
        $page['records'] = UserProfileFiles::find()->where(['profile_id'=>Yii::$app->request->get("user")])->all();
        return $this->renderPartial("files",$page);
    }
}

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
use common\models\LoginForm;
use backend\models\Tags;
use backend\models\TagTypes;
use fedemotta\datatables\DataTables;
use backend\models\BackEndMenus;
use backend\models\BackEndMenuRole;
use backend\models\Settings;

class TagsController extends Controller{
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
        //EventsCategories::saveEventsCategory();
        $b = new Tags();        
        $page=[]; 
        $page['rs']=[];
        
        $page['id'] = Yii::$app->request->get('id',null);
        
        if($page['id']!=null){            
            $page['rs'] = Tags::find()->where(['id' => $page['id']])->one();
            $page['edit']=true;
            //echo $page['recordset']['name'];
        }else{
            $page['rs'] = Tags::find()->where(['id' => '0'])->one();
        }
        if(isset($page['rs']['published'])){
            if($page['rs']['published']=='1'){
                $page['published'] = \yii\helpers\Html::checkbox("published",true);
            }else{
                $page['published'] = \yii\helpers\Html::checkbox("published",false);
            }
        }else{
            $page['published'] = \yii\helpers\Html::checkbox("published");
        }
        
        $page['records'] = Tags::getTags();        
        
        return $this->render('index',$page);        
    }

public function actionSave(){
    //insert and update
    if(Yii::$app->request->post("processor")=="true"){            
            echo Tags::saveTags();                        
    }
}
public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));   
    $a = Tags::findOne($id);
    $a->delete();
    return "Record successfully deleted";
}
public function actionTypes(){
    $page=[];
    $page['name']="";
    $page['id'] = Yii::$app->request->get('id');
    $page['selected_tags']=[];
    if($page['id']){            
            $rs = TagTypes::find()->where(['id' => $page['id']])->one();
            $page['name'] = $rs['name'];
            $page['edit']=true;
    }else{
            $rs = TagTypes::find()->where(['id' => '0'])->one();
    }
    $page['selected_tags'] = TagTypes::getSelectedTags();
    $page['records']=Tags::getTagTypes();
    
    
    return $this->render('tag-types',$page);
}
public function actionSearchTags(){
    //this is called via ajax from the view and returns the list of tags    
    $result="";
    $a = Tags::getTags();
    foreach($a as $c){
        $result.= "<option value='".$c['id']."'>".$c['name']."</option>";
    }
    return $result;
}
public function actionSaveTag(){
    if(Yii::$app->request->post("processor")=="true"){
        echo Tags::saveTagType();
    }
}
public function actionDeleteTag(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = TagTypes::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
}
}

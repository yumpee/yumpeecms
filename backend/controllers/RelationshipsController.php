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

/**
 * Description of Relationships
 *
 * @author Peter
 */
use Yii;
use yii\web\Controller;
use backend\models\Relationships;
use backend\models\RelationshipDetails;
use backend\models\Forms;
use backend\models\BackEndMenus;
use backend\models\BackEndMenuRole;
use backend\models\Settings;

class RelationshipsController extends Controller{
    //put your code here
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
        $page=[];
        $page['id']= Yii::$app->request->get('id',null);
        
        if($page['id']!=null):                
                $page['rs'] = Relationships::find()->where(['id' => $page['id']])->one();
        else:
                $page['rs'] = new Relationships();
        endif;
        $page['records'] = Relationships::find()->orderBy('title')->all();
        $source_arr = Forms::find()->all();
        $page['source_arr'] =  yii\helpers\ArrayHelper::map($source_arr, 'name', 'title');
        $target_arr = Forms::find()->all();
        $page['target_arr'] =  yii\helpers\ArrayHelper::map($target_arr, 'name', 'title');
        return $this->render('index',$page);
    }
    public function actionConfigure(){
        $page=[];
        $page['settings']=[];
        $page['id']= Yii::$app->request->get('id',null);
        $page['relations_id']= Yii::$app->request->get('relations_id',null);
        $page['details'] = Relationships::find()->where(['id'=>$page['relations_id']])->one();
        $page['records'] = RelationshipDetails::find()->where(['relationship_id'=>$page['relations_id']])->all();
        $page['edit_settings']= Yii::$app->request->get('actions',null);
        if($page['edit_settings']!=null):
            $page['settings'] = RelationshipDetails::find()->where(['id'=>$page['id']])->one();
        else:
            $page['settings'] = new RelationshipDetails();
        endif;
        
        return $this->render('details',$page);
    }
    
    public function actionSave(){
            $model = Relationships::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->setAttribute('title',Yii::$app->request->post('title'));
                $model->setAttribute('name',Yii::$app->request->post('name'));
                $model->setAttribute('source_type',Yii::$app->request->post('source_type'));
                $model->setAttribute('target_type',Yii::$app->request->post('target_type'));
                $model->setAttribute('source_id',Yii::$app->request->post('source_id'));
                $model->setAttribute('target_id',Yii::$app->request->post('target_id'));
                $model->setAttribute('notes',Yii::$app->request->post('notes'));
                $model->save();
                return "Relationship Profile successfully updated";
            else:
                $model =  new Relationships();
                $model->attributes = Yii::$app->request->post();
                $id = md5(date('YHmis').rand(1000,100000));
                $model->setAttribute('id',$id);
                $model->setAttribute('title',Yii::$app->request->post('title'));
                $model->setAttribute('name',Yii::$app->request->post('name'));
                $model->setAttribute('source_type',Yii::$app->request->post('source_type'));
                $model->setAttribute('target_type',Yii::$app->request->post('target_type'));
                $model->setAttribute('source_id',Yii::$app->request->post('source_id'));
                $model->setAttribute('target_id',Yii::$app->request->post('target_id'));
                $model->setAttribute('notes',Yii::$app->request->post('notes'));
                $model->save();
                return "New Relationship created";
            endif;
    }
    public function actionSaveDetails(){
        $model = RelationshipDetails::findOne(Yii::$app->request->post("id"));
        if($model!=null):
            $model->setAttribute('relationship_id',Yii::$app->request->post('relations_id'));
            $model->setAttribute('source_field',Yii::$app->request->post('source_field'));
            $model->setAttribute('target_field',Yii::$app->request->post('target_field'));
            $model->save();
            return "Relationship details updated";
        else:
            $model =  new RelationshipDetails();
            $model->attributes = Yii::$app->request->post();
            $id = md5(date('YHmis').rand(1000,100000));
            $model->setAttribute('id',$id);
            $model->setAttribute('relationship_id',Yii::$app->request->post('relations_id'));
            $model->setAttribute('source_field',Yii::$app->request->post('source_field'));
            $model->setAttribute('target_field',Yii::$app->request->post('target_field'));
            $model->save();
            return "New Relationship details created";
        endif;
    }
    public function actionDeleteDetails(){        
        RelationshipDetails::deleteAll(['id'=>Yii::$app->request->get('id')]);
        return "Relationship details deleted";
    }
}

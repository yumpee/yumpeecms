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

class TagsController extends Controller{

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

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
use backend\models\Skills;


class SkillsController extends Controller{

public function actionIndex()
    {
    $page=[]; 
    $page['name']="";
    $page['id']="";
    
    $page['id'] = Yii::$app->request->get('id',null);
        if($page['id']!=null){            
            $rs = Skills::find()->where(['id' => $page['id']])->all();
            $page['edit']=true;
            if(count($rs)>0):
                $page['name']=$rs[0]['name'];
                
            endif;
        }
    
    $page['records'] = Skills::find()->all();
    return $this->render('index',$page); 
   }
   
public function actionSave(){
    if(Yii::$app->request->post("processor")=="true"){
            echo Skills::saveGroup();                        
    }
}
public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Skills::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
}

}
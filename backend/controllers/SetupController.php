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
use backend\models\ClassSetup;
use backend\models\ClassElement;
use backend\models\ClassAttributes;
use backend\models\ClassElementAttributes;
use yii\Helpers\ArrayHelper;
use backend\models\BackEndMenus;
use backend\models\BackEndMenuRole;
use backend\models\Settings;

class SetupController extends Controller{
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
        $page =[];
        $page['id']= Yii::$app->request->get('id',null);
        if($page['id']!=null):
                $page['rs'] = ClassSetup::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = ClassSetup::find()->where(['id' => "0"])->one();
        endif;
        $setup_list = ClassSetup::find()->orderBy('alias')->all();
        $page['setup_list'] = ArrayHelper::map($setup_list, 'id', 'alias');
        
        $page['records'] = ClassSetup::find()->orderBy('alias')->all();
        return $this->render('index',$page);
    }
    public function actionSave(){
            $model = ClassSetup::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->attributes = Yii::$app->request->post();
                $model->name=Yii::$app->request->post("name");
                $model->parent_id = Yii::$app->request->post("parent_id");
                $model->alias=Yii::$app->request->post("alias");
                $model->show_in_menu=Yii::$app->request->post("show_in_menu");
                $model->display_image_id=Yii::$app->request->post("display_image_id");
                $model->display_order=Yii::$app->request->post("display_order");
                $model->save();
                return "Class successfully updated";
            else:
                $model =  new ClassSetup();
                $model->name=Yii::$app->request->post("name");
                $model->parent_id = Yii::$app->request->post("parent_id");
                $model->alias=Yii::$app->request->post("alias");
                $model->show_in_menu=Yii::$app->request->post("show_in_menu");
                $model->id = md5(date("Hmdis").rand(1000,10000));
                $model->display_image_id=Yii::$app->request->post("display_image_id");
                $model->display_order=Yii::$app->request->post("display_order");
                
                $model->save();
                return "New class created";
            endif;
    }
    public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = ClassSetup::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
    }
    public function actionDetails(){
        $page=[];
        $page['id']= Yii::$app->request->get('id',null);
        $page['prop_id']= Yii::$app->request->get('prop_id',null);
        $class_obj = ClassSetup::find()->where(['id'=>Yii::$app->request->get('class')])->one();
        $page['classname'] = $class_obj->alias;
        
        if($page['id']!=null):
                $page['rs'] = ClassElement::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = ClassElement::find()->where(['id' => "0"])->one();
        endif;  
        $element_list = ClassElement::find()->where(['class_id'=>Yii::$app->request->get('class')])->orderBy('alias')->all();
        $page['element_list'] = ArrayHelper::map($element_list, 'id', 'alias');
        $page['records_element'] = ClassElement::find()->where(['class_id'=>Yii::$app->request->get('class')])->orderBy('alias')->all();
        
        if($page['prop_id']!=null):
                $page['prop_rs'] = ClassAttributes::find()->where(['id' => $page['prop_id']])->one();
            else:
                $page['prop_rs'] = ClassAttributes::find()->where(['id' => "0"])->one();
        endif;  
        $prop_list = ClassAttributes::find()->where(['class_id'=>Yii::$app->request->get('class')])->orderBy('alias')->all();
        $page['prop_list'] = ArrayHelper::map($prop_list, 'id', 'alias');
        $page['records_attribute'] = ClassAttributes::find()->where(['class_id'=>Yii::$app->request->get('class')])->orderBy('alias')->all();
        
        
        
        return $this->render('details',$page);
   }
   
   public function actionSaveElement(){
       $model = ClassElement::findOne(Yii::$app->request->post("id"));
            if($model!=null):                
                $model->name=Yii::$app->request->post("name");
                $model->parent_id = Yii::$app->request->post("parent_id");
                $model->alias=Yii::$app->request->post("alias");
                $model->class_id=Yii::$app->request->post("class_id");
                $model->description=Yii::$app->request->post("description"); 
                $model->display_image_id=Yii::$app->request->post("display_image_id");
                $model->display_order=Yii::$app->request->post("display_order");
                $model->save();
                return Yii::$app->request->post("classname")." successfully updated";
            else:
                $model =  new ClassElement();
                $model->name=Yii::$app->request->post("name");
                $model->parent_id = Yii::$app->request->post("parent_id");
                $model->alias=Yii::$app->request->post("alias");
                $model->class_id=Yii::$app->request->post("class_id");
                $model->description=Yii::$app->request->post("description");  
                $model->display_image_id=Yii::$app->request->post("display_image_id");
                $model->id = md5(date("Hmdis").rand(1000,10000));
                $model->display_order=Yii::$app->request->post("display_order");
                $model->save();
                return "New ". Yii::$app->request->post("classname")." created";
            endif;
   }
   
   public function actionDeleteElement(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = ClassElement::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
    }
  public function actionDeleteAttribElement(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = ClassAttributes::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
    }
   
   public function actionSaveAttribute(){
       $model = ClassAttributes::findOne(Yii::$app->request->post("id"));
            if($model!=null):                
                $model->name=Yii::$app->request->post("name");
                $model->parent_id = Yii::$app->request->post("parent_id");
                $model->alias=Yii::$app->request->post("alias");
                $model->class_id=Yii::$app->request->post("class_id");
                $model->description=Yii::$app->request->post("description");      
                $model->display_order=Yii::$app->request->post("display_order");
                $model->save();
                return "Property for ".Yii::$app->request->post("classname")." successfully updated";
            else:
                $model =  new ClassAttributes();
                $model->name=Yii::$app->request->post("name");
                $model->parent_id = Yii::$app->request->post("parent_id");
                $model->alias=Yii::$app->request->post("alias");
                $model->class_id=Yii::$app->request->post("class_id");
                $model->description=Yii::$app->request->post("description");   
                $model->id = md5(date("Hmdis").rand(1000,10000));
                $model->display_order=Yii::$app->request->post("display_order");
                $model->save();
                return "New property for ". Yii::$app->request->post("classname")." created";
            endif;
   }
   public function actionSaveElementAttribute(){
       $subquery = ClassElement::find()->where(['class_id'=>Yii::$app->request->post('class_id')])->all();
       foreach($subquery as $rec):
           ClassElementAttributes::deleteAll(['IN','element_id',$rec->id]);
       endforeach;
       
       //loop through the text inputs
       foreach($_POST as $key => $value){
           if((substr($key,0,4)=="chk_")):
               list($del,$element,$property) = explode("_",$key);
               if(Yii::$app->request->post($key)=="on"):
                   $model = new ClassElementAttributes();
                   $model->id = md5(date("Hmdis").rand(1000,10000));
                   $model->element_id= $element;
                   $model->attribute_id = $property;
                   $model->element_attribute_val= Yii::$app->request->post("val_".$element."_".$property);
                   $model->save();   
               endif;
          endif;
       }
       return "Data successfully updated";
   }
   
}

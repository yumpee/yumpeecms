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
use backend\models\Widgets;
use backend\models\Themes;
use backend\models\Twig;
use backend\models\Roles;
use yii\Helpers\ArrayHelper;
use backend\models\BackEndMenus;
use backend\models\BackEndMenuRole;
use backend\models\Settings;

class WidgetsController extends Controller{
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
        $page['id']= Yii::$app->request->get('id',null);
        $perm_arr="";
        
        
        if($page['id']!=null):
                $page['rs'] = Widgets::find()->where(['id' => $page['id']])->one();
                $perm_arr = explode(" ",$page['rs']['permissions']);
            else:
                $page['rs'] = new Widgets();
        endif;
        $widget_list = Widgets::find()->where(['parent_id'=>'0'])->orderBy('name')->all();
        $page['widget_list'] = ArrayHelper::map($widget_list, 'id', 'name');
        $pages = Roles::find()->orderBy('name')->all();
        $page_map =  yii\helpers\ArrayHelper::map($pages, 'id', 'name');
        $page['permissions'] = \yii\helpers\Html::checkboxList("permissions",$perm_arr,$page_map);
        
        $page['records'] = Widgets::find()->orderBy('name')->all();
        return $this->render('index',$page);
    }
    
    public function actionSave(){
            $permissions = Yii::$app->request->post("permissions");
            $perm_val="";
            if(!empty($permissions)){
               $counter=0;
                // Loop to store and display values of individual checked checkbox.
                foreach($permissions as $selected){                    
                    $perm_val = $perm_val." ".$selected;       
                }
            }
            $model = Widgets::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->attributes = Yii::$app->request->post();
                $model->setting_value="";
                $model->parent_id = Yii::$app->request->post("parent_id");   
                $model->permissions=$perm_val;
                $model->require_login = Yii::$app->request->post("require_login");
                $model->save();
                return "Widget successfully updated";
            else:
                $widgets =  new Widgets();
                $widgets->attributes = Yii::$app->request->post();
                $widgets->setting_value="";
                $widgets->parent_id = Yii::$app->request->post("parent_id");
                $widgets->template_type='C';
                $widgets->name = Yii::$app->request->post("name");
                $widgets->short_name = Yii::$app->request->post("short_name");
                $widgets->permissions=$perm_val;
                $widgets->require_login = Yii::$app->request->post("require_login");
                $widgets->save();
                return "New widget created";
            endif;
    }
    public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Widgets::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
    }

    public function actionExtensions(){
        $page=[];
        $template_list = Themes::find()->orderBy('name')->all();
        $tag_map =  yii\helpers\ArrayHelper::map($template_list, 'id', 'name');
        if(Yii::$app->request->get("reload")=="true"):
            $page['theme'] = \yii\helpers\Html::dropDownList("theme", Yii::$app->request->get("theme"),$tag_map,['prompt'=>'Select a theme','id'=>'theme']);
            $page['selected_theme'] = Yii::$app->request->get("theme"); 
        else:
            $page['theme'] = \yii\helpers\Html::dropDownList("theme", \frontend\components\ContentBuilder::getSetting("current_theme"),$tag_map,['prompt'=>'Select a theme','id'=>'theme']);
            $page['selected_theme'] = \frontend\components\ContentBuilder::getSetting("current_theme");
        endif;
        $page['records'] = Widgets::find()->orderBy('name')->all();
        
        return $this->render('extensions',$page);
    }
    public function actionFetchTwigWidget(){
        $theme_id = Yii::$app->request->get('theme_id');
        $renderer = Yii::$app->request->get('renderer');
        
        $record = Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'W'])->one();
        if($record!=null):
            return $record['code'];
        else:
            return "";
        endif;
        
    }
    public function actionSaveTwigWidget(){
        $theme_id = Yii::$app->request->post('theme');
        $renderer = Yii::$app->request->post('renderer');
        $code = Yii::$app->request->post('code');
        
        $record = Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'W'])->one();
        
        if($record!=null):
                if(substr(Yii::$app->request->post('filename'), 0, strlen("twig/")) === "twig/"):
                    if(Yii::$app->request->post('code')==""):
                        $code="<!--Refer to ".Yii::$app->request->post('filename')." for content-->";
                    endif;
                    $record->setAttribute("filename",Yii::$app->request->post('filename'));
                else:
                    if($record->filename=="" || Yii::$app->request->post('filename')==""):
                        $record->setAttribute("filename",md5(date("YmdHis").rand(1000,10000)).".twig");
                    endif;
                endif;      
                $record->setAttribute("code",$code);
                $record->save();
                return "Twig template updated";
        else:
                $twig =  new Twig();
                $twig->setAttribute("theme_id",$theme_id);
                $twig->setAttribute("renderer",$renderer);
                $twig->setAttribute("renderer_type",'W');
                $twig->setAttribute("code",$code);
                $twig->setAttribute("filename",md5(date("YmdHis")).".twig");
                if(substr(Yii::$app->request->post('filename'), 0, strlen("twig/")) === "twig/"):
                    if(Yii::$app->request->post('code')==""):
                        $code="<!--Refer to ".Yii::$app->request->post('filename')." for content-->";
                        $twig->setAttribute("code",$code);
                    endif;
                    $twig->setAttribute("filename",Yii::$app->request->post('filename'));
                endif;
                $twig->save();
                return "Twig template updated";
        endif;
    }
}

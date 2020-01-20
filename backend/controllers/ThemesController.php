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
use yii\Helpers\ArrayHelper;
use backend\models\Themes;
use backend\models\Templates;
use backend\models\Settings;
use backend\models\Twig;
use backend\models\Forms;
use backend\models\FormTwig;
use backend\models\CustomSettings;
use backend\models\BackEndMenus;
use backend\models\BackEndMenuRole;


class ThemesController extends Controller{
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
        if($page['id']!=null):
                $page['rs'] = Themes::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = Themes::find()->where(['id' => "0"])->one();
        endif;
        $page['records'] = Themes::find()->orderBy('name')->all();
        $page['current_theme'] = Settings::find()->where(['setting_name'=>'current_theme'])->one();
        $my_header_home_url = Settings::find()->where(['setting_name'=>'home_url'])->one();
        $page['home_url'] = $my_header_home_url['setting_value'];
        return $this->render('index',$page);
    }
    
    public function actionSave(){
            $model = Themes::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->attributes = Yii::$app->request->post();
                $model->save();
                return "Theme successfully updated";
            else:
                $themes =  new Themes();
                $themes->attributes = Yii::$app->request->post();
                $themes->setAttribute('is_default','1');
                $themes->save();
                return "New theme created";
            endif;
    }
    public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Themes::findOne($id);
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
        
        $page['records'] = Templates::find()->where(['<>','route','forms/display'])->orderBy('name')->all(); //we do not want forms displaying here
        
        return $this->render('extensions',$page);
    }
    public function actionFetchTwigTheme(){
        $theme_id = Yii::$app->request->get('theme_id');
        $renderer = Yii::$app->request->get('renderer');
        $record = Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'V'])->one();
        if($record!=null):
            return $record['code'];
        else:
            return "";
        endif;
    }
    public function actionSaveTwigTheme(){
        $theme_id = Yii::$app->request->post('theme');
        $renderer = Yii::$app->request->post('renderer');
        $code = Yii::$app->request->post('code');
        
        $record = Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'V'])->one();
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
                $twig->setAttribute("renderer_type",'V');
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
    public function actionImport(){
        $page=[];
        $template_list = Themes::find()->orderBy('name')->all();
        $tag_map =  yii\helpers\ArrayHelper::map($template_list, 'id', 'name');
        $page['target_theme']= \yii\helpers\Html::dropDownList("target_theme",'',$tag_map,['prompt'=>'Select a theme','class'=>'form-control','id'=>'target_theme']);
        $page['source_theme']= \yii\helpers\Html::dropDownList("source_theme",'',$tag_map,['prompt'=>'Select a theme','class'=>'form-control','id'=>'source_theme']);
        return $this->render("import",$page);
    }
    public function actionTwig(){
        return json_encode(Twig::find()->select(['id','renderer','renderer_type'])->with('form','page','templates')->asArray()->where(['theme_id'=>Yii::$app->request->get('source')])->andWhere('renderer_type IN ("I","W","V","F","R","Z")')->orderBy(['renderer_type'=>SORT_ASC,'renderer'=>SORT_ASC])->all());
    }
    public function actionSaveImport(){
        $model = Twig::find()->where(['theme_id'=>Yii::$app->request->post('source_theme')])->andWhere('renderer_type IN ("I","W","V","F","R","Z")')->all();
        foreach($model as $program):
            if(Yii::$app->request->post("c".$program->id)=="on" && Yii::$app->request->post("target_theme")<>""):
                $rec = Twig::find()->where(['renderer'=>$program->renderer])->andWhere('theme_id="'.Yii::$app->request->post("target_theme").'"')->one();
                if($rec==null):
                        $new_insert = new Twig();
                        $new_insert->setAttribute("theme_id",Yii::$app->request->post("target_theme"));
                        $new_insert->setAttribute("renderer",$program->renderer);
                        $new_insert->setAttribute("renderer_type",$program->renderer_type);
                        $new_insert->setAttribute("code",$program->code);
                        if(strpos($program->filename,'twig/')!==false):
                            $filename = $program->filename;
                        else:
                            $filename=md5(date("Hmdis").rand(1000,100000)).".twig";
                        endif;
                        $new_insert->setAttribute("filename",$filename);
                        $new_insert->save();
                    else:
                        $rec->setAttribute("theme_id",Yii::$app->request->post("target_theme"));
                        $rec->setAttribute("renderer",$program->renderer);
                        $rec->setAttribute("renderer_type",$program->renderer_type);
                        $rec->setAttribute("code",$program->code);
                        $rec->setAttribute("filename",$program->filename);                        
                        $rec->save();
                endif;
            endif;
        endforeach;
        return "Import completed successfully";
    }
    public function actionSettings(){
        $page['records'] = Themes::find()->orderBy('name')->all();
        $page['current_theme'] = Settings::find()->where(['setting_name'=>'current_theme'])->one();
        return $this->render('settings',$page);
    }
    public function actionFetchTwigSettings(){
        $theme_id = Yii::$app->request->get('theme_id');
        $renderer = Yii::$app->request->get('renderer');
        
        $record = Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'Z'])->one();
        if($record!=null):
            return $record['code'];
        else:
            return "";
        endif;
        
    }
    public function actionSaveTwigSettings(){
        $theme_id = Yii::$app->request->post('theme_id');
        $renderer = Yii::$app->request->post('renderer');
        $code = Yii::$app->request->post('code');
        
        $record = Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'Z'])->one();
        
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
                $twig->setAttribute("renderer_type",'Z');
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
 public function actionManageSettings(){    
        //we handle the loading of twig template if it is turned on
                        $content="";
                        $theme_id = Yii::$app->request->get("id");
                        $page['theme_id'] = $theme_id;
                        $theme_obj =  Themes::find()->where(['id'=>$theme_id])->one();
                        $renderer = $theme_obj['id']."_".$theme_obj['folder'];
                        $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');
                        $metadata['form_id'] = $theme_id; 
                        $metadata['param'] = Yii::$app->request->csrfParam;
                        $metadata['token'] = Yii::$app->request->csrfToken;
                        $settings = CustomSettings::find()->where(['theme_id'=>$theme_id])->all();
                        $form = Forms::find()->where(['id'=>Yii::$app->request->get("form_id")])->one();
                        $codebase=\frontend\models\Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'Z'])->one();
                        $theme_list = Themes::find()->orderBy('name')->all();
                        $page['theme_list'] = ArrayHelper::map($theme_list, 'id', 'name');
                        
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new \frontend\models\Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'], ['form'=>$form,'metadata'=>$metadata,'app'=>Yii::$app,'settings'=>$settings]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                        $page['custom_id'] = Yii::$app->request->get('custom_id',null);
                        if($page['custom_id']!=null):
                            $page['custom_rs'] = CustomSettings::find()->where(['id' => $page['custom_id']])->one();
                        else:
                            $page['custom_rs'] = CustomSettings::find()->where(['id' => "0"])->one();
                        endif;
                        $page['custom_records'] =CustomSettings::find()->where(['theme_id'=>$theme_id])->all();
                        return $this->render('custom-settings',$page);
     
 }
 public function actionCustomSave(){
        $model = CustomSettings::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                
                $model->setting_name=Yii::$app->request->post("setting_name");
                $model->setting_value= Yii::$app->request->post("setting_value");
                $model->description = Yii::$app->request->post("description");
                $model->theme_id = Yii::$app->request->post("theme_id");
                $model->save();
                return "Custom Setting successfully updated";
            else:
                $model =  new CustomSettings();
                $model->id = md5(date("YmdHis").rand(1000,10000));
                $model->setting_name=Yii::$app->request->post("setting_name");
                $model->setting_value= Yii::$app->request->post("setting_value");
                $model->description = Yii::$app->request->post("description");
                $model->theme_id = Yii::$app->request->post("theme_id");
                $model->save();
                return "New Custom Setting created";
            endif;
    }
    public function actionDeleteCustom(){
        $id = str_replace("}","",Yii::$app->request->get("id"));    
        $a = CustomSettings::findOne($id);
        $a->delete();
        echo "Record successfully deleted";
    }
    public function actionImportTheme(){
        $settings = CustomSettings::find()->where(['theme_id'=>Yii::$app->request->post("target_theme")])->all();
        foreach($settings as $setting):
            $a = new CustomSettings();
            $id = md5(date("Hmisd").rand(10000,1000000));
            $a->setAttribute("id",$id);
            $a->setAttribute("setting_name",$setting["setting_name"]);
            $a->setAttribute("setting_value",$setting["setting_value"]);
            $a->setAttribute("theme_id",Yii::$app->request->post("current_theme"));
            $a->save();
        endforeach;
        echo "Setting import completed";
    }
 
}

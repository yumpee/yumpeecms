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
use backend\models\Themes;
use backend\models\Templates;
use backend\models\Settings;
use backend\models\Twig;

class ThemesController extends Controller{

    public function actionIndex(){
        $page['id']= Yii::$app->request->get('id',null);
        if($page['id']!=null):
                $page['rs'] = Themes::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = Themes::find()->where(['id' => "0"])->one();
        endif;
        $page['records'] = Themes::find()->orderBy('name')->all();
        $page['current_theme'] = Settings::find()->where(['setting_name'=>'current_theme'])->one();
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
        return json_encode(Twig::find()->select(['id','renderer'])->asArray()->where(['theme_id'=>Yii::$app->request->get('source')])->andWhere('renderer_type IN ("I","W","V")')->orderBy('renderer')->all());
    }
    public function actionSaveImport(){
        $model = Twig::find()->where(['theme_id'=>Yii::$app->request->post('source_theme')])->andWhere('renderer_type IN ("I","W","V")')->all();
        foreach($model as $program):
            if(Yii::$app->request->post("c".$program->id)=="on" && Yii::$app->request->post("target_theme")<>""):
                $rec = Twig::find()->where(['renderer'=>$program->renderer])->andWhere('theme_id="'.Yii::$app->request->post("target_theme").'"')->one();
                if($rec==null):
                        $new_insert = new Twig();
                        $new_insert->setAttribute("theme_id",Yii::$app->request->post("target_theme"));
                        $new_insert->setAttribute("renderer",$program->renderer);
                        $new_insert->setAttribute("renderer_type",$program->renderer_type);
                        $new_insert->setAttribute("code",$program->code);
                        $filename=md5(date("Hmdis").rand(1000,1000)).".twig";
                        $new_insert->setAttribute("filename",$filename);
                        $new_insert->save();
                    else:
                        $rec->setAttribute("theme_id",Yii::$app->request->post("target_theme"));
                        $rec->setAttribute("renderer",$program->renderer);
                        $rec->setAttribute("renderer_type",$program->renderer_type);
                        $rec->setAttribute("code",$program->code);
                        $rec->save();
                endif;
            endif;
        endforeach;
        return "Import completed successfully";
        
    }
}

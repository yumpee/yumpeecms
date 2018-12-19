<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\Widgets;
use backend\models\Themes;
use backend\models\Twig;
use yii\Helpers\ArrayHelper;

class WidgetsController extends Controller{

    public function actionIndex(){
        $page['id']= Yii::$app->request->get('id',null);
        if($page['id']!=null):
                $page['rs'] = Widgets::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = new Widgets();
        endif;
        $widget_list = Widgets::find()->where(['parent_id'=>'0'])->orderBy('name')->all();
        $page['widget_list'] = ArrayHelper::map($widget_list, 'id', 'name');
        
        $page['records'] = Widgets::find()->orderBy('name')->all();
        return $this->render('index',$page);
    }
    
    public function actionSave(){
            $model = Widgets::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->attributes = Yii::$app->request->post();
                $model->setting_value="";
                $model->parent_id = Yii::$app->request->post("parent_id");     
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

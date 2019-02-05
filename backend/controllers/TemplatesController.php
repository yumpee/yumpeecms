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
use backend\models\Templates;
use backend\models\Widgets;
use backend\models\Blocks;
use backend\models\MenuProfile;
use yii\Helpers\ArrayHelper;
use backend\models\TemplateWidget;
use backend\models\WidgetPosition;
use fedemotta\datatables\DataTables;

class TemplatesController extends Controller{

public function actionIndex()
    {
        //EventsCategories::saveEventsCategory();
        $b = new Templates();        
        $page=[]; 
        $page['rs']=[];
        $page['id'] = Yii::$app->request->get('id',null);
        $page['pid'] = Yii::$app->request->get('pid',null);
        if($page['id']!=null){            
            $page['rs'] = Templates::find()->where(['id' => $page['id']])->one();
            $page['edit']=true;
            //echo $page['recordset']['name'];
        }else{
            $page['rs'] = Templates::find()->where(['id' => '0'])->one();
        }
        if($page['pid']!=null):
            $page['rsp']= WidgetPosition::find()->where(['id'=>$page['pid']])->one();
        else:
            $page['rsp']=new WidgetPosition();
        endif;
        $page['records'] = Templates::find()->orderBy('name')->all(); 
        $page['widget_details']  = Widgets::find()->orderBy('name')->all();
        $page['widget_position']  = WidgetPosition::find()->orderBy('title')->all();
        $page['selected_widget'] = Templates::getMyWidgets($page['id']);
        $parent_arr=['blog/details','contact/index','standard/index','blog/category','registration/index','accounts/password','accounts/login','roles/index','accounts/logout','blog/index','roles/details'];
        $template_list = Templates::find()->where(['IN','route',$parent_arr])->orderBy('name')->all();
        $tag_map =  yii\helpers\ArrayHelper::map($template_list, 'id', 'name');        
        $page['custom_template'] = \yii\helpers\Html::dropDownList("custom_template","",$tag_map,['prompt'=>'Select a template','class'=>'form-control']);
        $tp = WidgetPosition::find()->all();
        $page['template_position'] = yii\helpers\ArrayHelper::map($tp, 'name', 'title'); 
        
        return $this->render('index',$page);        
    }

public function actionSave(){
    //insert and update
    if(Yii::$app->request->post("processor")=="true"){ 
            echo Templates::saveTemplates();                        
    }
}
public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Templates::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
}
public function actionDeletePosition(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = WidgetPosition::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
}
public function actionManage(){
    $page=[];
    $page['settings']="";
    $page['settings']['html_blocks']="";
    $page['id']="";
    $widget = Yii::$app->request->get("id");
    $record = TemplateWidget::find()->where(['widget'=>Yii::$app->request->get("id")])->andWhere(['page_id'=>Yii::$app->request->get("template_id")])->one();
    
    if($record!=null):
        $page['settings'] = json_decode($record->settings);
        $page['id']= $record->id;
    endif;
    if($widget=="widget_html"):
        $pages_list = Blocks::find()->orderBy('name')->all();
        $page['html_blocks'] = ArrayHelper::map($pages_list, 'id', 'name');
    endif;
    if($widget=="widget_menu"):
        $pages_list = MenuProfile::find()->orderBy('name')->all();
        $page['menu_blocks'] = ArrayHelper::map($pages_list, 'id', 'name');
    endif;
    $page['widget'] = Yii::$app->request->get("id");
    $page['template_id'] = Yii::$app->request->get("template_id");
    
    $widget_rec= Widgets::find()->where(['short_name'=>$widget])->one();
    if($widget_rec["parent_id"]!="0"):
        $widget_parent = Widgets::find()->where(['id'=>$widget_rec["parent_id"]])->one();
        $widget = $widget_parent["short_name"];
    endif;
    
    return $this->renderPartial('@backend/widgets/'.$widget,$page);
    /*if(file_exists('@backend/widgets/'.$widget.'.php')):
        return $this->renderPartial('@backend/widgets/'.$widget,$page);
    else:
        $widget_rec= Widgets::find()->where(['short_name'=>$widget])->one();
        $widget_parent = Widgets::find()->where(['id'=>$widget_rec["parent_id"]])->one();
        return $this->renderPartial('@backend/widgets/'.$widget_parent["short_name"],$page);
    endif;*/
}

public function actionSaveWidget(){
    $json_data = json_encode(Yii::$app->request->post());
    $a = TemplateWidget::findOne(Yii::$app->request->post('id'));
    $a->settings = $json_data;
    $a->update();
    return "Successfully saved settings";
}
public function actionSaveCustom(){
    
    $child = Templates::find()->where(['id'=>Yii::$app->request->post("custom_template")])->one();
    list($control,$action)=explode("/",$child->route);
    $route = $control."/".Yii::$app->request->post("renderer");
    
    $a = new Templates();    
    $id = md5(date('HmdHis').rand(1000,10000));
    $a->setAttribute('id',$id);
    $a->setAttribute('name',Yii::$app->request->post("name"));
    $a->setAttribute('route',$route);
    $a->setAttribute('internal_route_stat','N');
    $a->setAttribute('parent_id',Yii::$app->request->post("custom_template"));
    $a->setAttribute('renderer',$route);
    $a->save(false);
    return "New child template successfully created";
}

public function actionSavePosition(){
    $model = WidgetPosition::find()->where(['id'=>Yii::$app->request->post("position_id")])->one();
    
    if($model!=null): 
        $id = Yii::$app->request->post("position_id");
        $model->name = "yumpee_pos_".Yii::$app->request->post("name");
        $model->title = Yii::$app->request->post("title");
        $model->description = Yii::$app->request->post("description");
        $model->save();
        return "Widget position updated successfully";
    else:
        $id = md5(date("Hmdis").rand(1000,10000));
        $model =  new WidgetPosition();
        $model->setAttribute("id",$id);
        $model->setAttribute("name","yumpee_pos_".Yii::$app->request->post("name"));
        $model->setAttribute("description",Yii::$app->request->post("description"));
        $model->setAttribute("title",Yii::$app->request->post("title"));
        $model->save();
        return "New widget position created successfully";
    endif;
}
}

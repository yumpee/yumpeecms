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
use backend\models\Blocks;
use backend\models\BlockGroup;
use backend\models\BlockGroupList;
use backend\models\Pages;
use backend\models\Roles;
use backend\models\WidgetPosition;
use fedemotta\datatables\DataTables;

class BlocksController extends Controller{

public function actionIndex()
    {
    
        $b = new Blocks();        
        $page=[]; 
        $page['rs']=[];
        $page['rsg']=[];
        $title_level="";
        $position="";
        $wposition="";
        $page_arr="";
        $perm_arr="";
        
        $page['id'] = Yii::$app->request->get('id',null);
        
        if($page['id']!=null){             
            $page['rs'] = Blocks::find()->where(['id' => $page['id']])->one();            
            $page['edit']=true;
            if(count($page['rs']) > 0):
            $title_level = $page['rs']['title_level'];
            $position = $page['rs']['position'];
            $wposition = $page['rs']['widget'];
            endif;
            $c = $page['rs']->blockPages;
            $page_arr =  yii\helpers\ArrayHelper::getColumn($c, 'page_id');
            $perm_arr = explode(" ",$page['rs']['permissions']);
        }else{
            $page['rs'] = Blocks::find()->where(['id' => "0"])->one();
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
        //Show title
        if(isset($page['rs']['show_title'])){
            if($page['rs']['show_title']=='1'){
                $page['show_title'] = \yii\helpers\Html::checkbox("show_title",true);
            }else{
                $page['show_title'] = \yii\helpers\Html::checkbox("show_title",false);
            }
        }else{
            $page['show_title'] = \yii\helpers\Html::checkbox("show_title");
        }
        //Editable
        if(isset($page['rs']['editable'])){
            if($page['rs']['editable']=='1'){
                $page['editable'] = \yii\helpers\Html::checkbox("editable",true);
            }else{
                $page['editable'] = \yii\helpers\Html::checkbox("editable",false);
            }
        }else{
            $page['editable'] = \yii\helpers\Html::checkbox("editable");
        }
        
        $pages = Pages::find()->orderBy('menu_title')->all();
        $page_map =  yii\helpers\ArrayHelper::map($pages, 'id', 'menu_title');
        $page['pages'] = \yii\helpers\Html::checkboxList("pages",$page_arr,$page_map);
        
        $pages = Roles::find()->orderBy('name')->all();
        $page_map =  yii\helpers\ArrayHelper::map($pages, 'id', 'name');
        $page['permissions'] = \yii\helpers\Html::checkboxList("permissions",$perm_arr,$page_map);
        
        $blocks = Blocks::find()->orderBy('name')->all();
        $group_id = Yii::$app->request->get('group_id',null);
        $page_map =  yii\helpers\ArrayHelper::map($blocks, 'id', 'name');
        $c = BlockGroupList::find()->where(['group_id'=>$group_id])->all();
        $page_arr =  yii\helpers\ArrayHelper::getColumn($c, 'block_id');
        $page['blocks'] = \yii\helpers\Html::checkboxList("blocks",$page_arr,$page_map);
        
        $page['title_level'] = \yii\helpers\Html::dropDownList("title_level",$title_level,['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5'],["class"=>"form-control"]);
        $page['position'] = \yii\helpers\Html::dropDownList("position",$position,['before_left'=>'Before Widget Left','after_left'=>'After Widget Left','before_right'=>'Before Widget Right','after_right'=>'After Widget Right','before_header'=>'Before Header','after_header'=>'After Header','before_content'=>'Before Content','after_content'=>'After Content','before_footer'=>'Before Footer','after_footer'=>'After Footer'],["class"=>"form-control"]);
        $widget_position = WidgetPosition::find()->orderBy('title')->all();
        $wp_arr = yii\helpers\ArrayHelper::map($widget_position, 'name', 'title');
        $page['custom_position']= \yii\helpers\Html::dropDownList("widget",$wposition,$wp_arr,["class"=>"form-control","prompt"=>"Select a Widget Position"]);
        
        $page['records'] = Blocks::find()->orderBy('name')->all();
        $page['group_records'] = BlockGroup::find()->all();
        if(Yii::$app->request->get("group_id")!=null):
            $page['rsg'] = BlockGroup::find()->where(['id'=>Yii::$app->request->get("group_id")])->one();
        else:
            $page['rsg'] = BlockGroup::find()->where(['id' => "0"])->one();
        endif;
        
        return $this->render('index',$page);        
    }

public function actionSave(){
    //insert and update
    if(Yii::$app->request->post("processor")=="true"){ 
            //echo Yii::$app->request->post("cont");
            echo Blocks::saveBlocks();                        
    }
}
public function actionSaveGroup(){
    $model = BlockGroup::findOne(Yii::$app->request->post("id"));
    if($model!=null): 
        $id = Yii::$app->request->post("id");
        $model->name=Yii::$app->request->post("name");
        $model->save();
    else:
        $id = md5(date("Hmdis").rand(1000,10000));
        $model =  new BlockGroup();
        $model->name=Yii::$app->request->post("name");
        $model->id = $id;
        $model->save();
    endif;
    BlockGroupList::deleteAll(['IN','group_id',$id]);
    $block_arr = Yii::$app->request->post("blocks");
    if(!empty($block_arr)):
        
        foreach($block_arr as $selected):
            $model = new BlockGroupList();
            $model->id = md5(date("Hmdis").rand(1000,10000));
            $model->block_id = $selected;
            $model->group_id = $id;
            $model->save();
        endforeach;
    endif;
    return "Group update successful";
}
public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Blocks::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
}


}
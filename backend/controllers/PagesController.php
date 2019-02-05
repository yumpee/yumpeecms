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
use backend\models\Pages;
use backend\models\Tags;
use backend\models\Templates;
use backend\models\CSS;
use backend\models\MenuProfile;
use backend\models\Settings;
use backend\models\Forms;
use backend\models\Roles;
use backend\models\Twig;
use fedemotta\datatables\DataTables;


class PagesController extends Controller{

public function actionIndex()
    {
        $b = new Pages();        
        $page=[]; 
        $page['rs']=[];
        
        $page['id'] = Yii::$app->request->get('id',null);
        
        $layout="";
        $template="";
        $sidebar="";
        $tag_id="";
        $show_in_menu="";
        $index_tag_arr=""; //this variable is used to define the index tags
        $show_footer_image="";
        $show_header_image="";
        $perm_arr="";
        
        if($page['id']!=null){            
            $page['rs'] = Pages::find()->where(['id' => $page['id']])->one();
            $page['edit']=true;
            $perm_arr = explode(" ",$page['rs']['permissions']);
            if(count($page['rs']) > 0): // if we find any record then
                $layout = $page['rs']['layout'];
                $template = $page['rs']['template'];
                $sidebar = $page['rs']['sidebar'];
                $tag_id = $page['rs']['tag_id'];
                $show_in_menu = $page['rs']['show_in_menu'];
                $show_footer_image=$page['rs']['show_footer_image'];
                $show_header_image=$page['rs']['show_header_image'];
                //what of index tags we handle them here
                $c = Pages::getIndexTags($page['id']);
                $index_tag_arr =  yii\helpers\ArrayHelper::getColumn($c, 'index_tag_id');
            endif;
        }else{
            $page['rs'] = Pages::find()->where(['id' => "0"])->one();
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
        $page['layout'] = \yii\helpers\Html::dropDownList("layout",$layout,['column1'=>'Single Column','column2'=>'Two Columns'],['class'=>'form-control']);
        
        $cb=[];
        $r = Templates::find()->where(['route'=>'blog/details'])->one();
        if($r!=null):
            $cr = Templates::find()->select('route')->asArray()->where(['parent_id'=>$r['id']])->column();
        endif;
        $r = Templates::find()->where(['route'=>'roles/details'])->one();
        if($r!=null):
            $b = Templates::find()->select('route')->asArray()->where(['parent_id'=>$r['id']])->column();
            $cr = array_merge($cr,$b);
        endif;
        
        $b=['blog/details','roles/details'];
        $cr = array_merge($cr,$b);
                     
        $template_list = Templates::find()->where(['NOT IN','route',$cr])->andWhere('internal_route_stat="N"')->orderBy('name')->all();
        $tag_map =  yii\helpers\ArrayHelper::map($template_list, 'id', 'name');
        $page['template'] = \yii\helpers\Html::dropDownList("template",$page['rs']['template'],$tag_map,['prompt'=>'Select a template','class'=>'form-control']);
        
             
        $page['sidebar'] = \yii\helpers\Html::dropDownList("sidebar",$sidebar,['default'=>'Default Sidebar','contact'=>'Contact Page'],['class'=>'form-control']);
        $page['show_in_menu'] = \yii\helpers\Html::dropDownList("show_in_menu",$show_in_menu,['1'=>'Yes','0'=>'No'],['class'=>'form-control']);
        $page['show_header_image'] = \yii\helpers\Html::dropDownList("show_header_image",$show_header_image,['1'=>'Yes','0'=>'No'],['class'=>'form-control']);
        $page['show_footer_image'] = \yii\helpers\Html::dropDownList("show_footer_image",$show_footer_image,['1'=>'Yes','0'=>'No'],['class'=>'form-control']);
        $tags = Tags::getTags();
        $page['selected_tags'] = Pages::getSelectedTags();
        if(count($page['selected_tags'])==0):
            $page['selected_tags'][0]['id']="";
            $page['selected_tags'][0]['name']="";
        endif;
        $tag_map =  yii\helpers\ArrayHelper::map($tags, 'id', 'name'); 
        $page['tags'] = \yii\helpers\Html::dropDownList("tag_id",$tag_id,$tag_map,['prompt'=>'Select Tag'],['class'=>'form-control']);
        //$page['records'] = Pages::getMyPages(); 
        $page['records'] = Pages::find()->orderBy('title')->all();
        
        
        $tag_map =  yii\helpers\ArrayHelper::map($page['records'], 'id', 'menu_title');
        $page['parent_id'] = \yii\helpers\Html::dropDownList("parent_id",$page['rs']['parent_id'],$tag_map,['prompt'=>'Select a page','class'=>'form-control']);
        
        $tag_map =  yii\helpers\ArrayHelper::map(CSS::find()->all(), 'id', 'name');
        $page['css'] = \yii\helpers\Html::dropDownList("css",$page['rs']['css'],$tag_map,['prompt'=>'N/A','class'=>'form-control']);
        
        $form_map =  yii\helpers\ArrayHelper::map(Forms::find()->orderBy('title')->all(), 'id', 'title');
        $page['forms'] = \yii\helpers\Html::dropDownList("forms",$page['rs']['form_id'],$form_map,['prompt'=>'N/A','class'=>'form-control']);
        $role_map =  yii\helpers\ArrayHelper::map(Roles::find()->orderBy('name')->all(), 'id', 'name');
        $page['roles'] = \yii\helpers\Html::dropDownList("roles",$page['rs']['role_id'],$role_map,['prompt'=>'N/A','class'=>'form-control']);
        
        $tag_map =  yii\helpers\ArrayHelper::map(MenuProfile::find()->all(), 'id', 'name');
        $page['menu_profile'] = \yii\helpers\Html::dropDownList("menu_profile",$page['rs']['menu_profile'],$tag_map,['prompt'=>'System:Default','class'=>'form-control']);
        
        //lets fetch the index tags here
        $index_tags = Tags::getTagTypes();
        $tag_map =  yii\helpers\ArrayHelper::map($index_tags, 'id', 'name');
        $page['tag_types'] = \yii\helpers\Html::checkboxList("index_tags",$index_tag_arr,$tag_map);
        $page['home_url'] = Settings::find()->where(['setting_name'=>'home_url'])->one();
        
        $brender= Templates::find()->where(['route'=>'roles/details'])->all();
        $blog_map =  yii\helpers\ArrayHelper::map($brender, 'id', 'name');
        $child_blog_render= Templates::find()->where(['parent_id'=>$brender[0]['id']])->all();
        $render_map =  yii\helpers\ArrayHelper::map($child_blog_render, 'id', 'name');
        $page['renderer'] = \yii\helpers\Html::dropDownList("renderer",$page['rs']['renderer'],array_merge($blog_map,$render_map),['prompt'=>'','class'=>'form-control']);
        
        $pages = Roles::find()->orderBy('name')->all();
        $page_map =  yii\helpers\ArrayHelper::map($pages, 'id', 'name');
        $page['permissions'] = \yii\helpers\Html::checkboxList("permissions",$perm_arr,$page_map);
        
        return $this->render('index',$page);        
    }

public function actionSave(){
    //insert and update
    if(Yii::$app->request->post("processor")=="true"){            
            echo Pages::savePages();                        
    }
}
public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Pages::findOne($id);
    $a->delete();
    Pages::deleteTrace();
    echo "Record successfully deleted";
}
public function actionDuplicate(){
    echo Pages::savePages("saveasnew");  
}


}

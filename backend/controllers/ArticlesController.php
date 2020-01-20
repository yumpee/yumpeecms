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
use backend\models\Articles;
use backend\models\ArticlesCategories;
use backend\models\ArticlesBlogIndex;
use backend\models\ArticlesCategoryRelated;
use backend\models\ArticlesTag;
use backend\models\Settings;
use backend\models\Pages;
use backend\models\Forms;
use backend\models\ArticleMedia;
use backend\models\ArticleDetails;
use backend\models\Templates;
use backend\models\Roles;
use backend\models\Tags;
use backend\models\BackEndMenus;
use backend\models\BackEndMenuRole;



use fedemotta\datatables\DataTables;

class ArticlesController extends Controller{
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
public function actionIndex()
    {
        
        $page=[]; 
        $page['rs']=[];
        $page_arr="";
        $index_arr="";
        $page['selected_tags']=[];
        $perm_arr="";
        
        $page['id'] = Yii::$app->request->get('id',null);
        
        if($page['id']!=null){            
            $page['rs'] = Articles::find()->where(['id' => $page['id']])->one();
            $page['edit']=true;
            $page['selected_tags'] = $page['rs']->selectedTags;
            $c = $page['rs']->categories;
            $page_arr =  yii\helpers\ArrayHelper::getColumn($c, 'category_id');            
            $c = $page['rs']->blogIndex;
            $index_arr =  yii\helpers\ArrayHelper::getColumn($c, 'blog_index_id');
            $perm_arr = explode(" ",$page['rs']['permissions']);
        }else{
            $page['rs'] = Articles::find()->where(['id' => "0"])->one();
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
        
        if(isset($page['rs']['published_by_stat'])){
            if($page['rs']['published_by_stat']=='1'){
                $page['published_by_stat'] = \yii\helpers\Html::checkbox("published_by_stat",true);
            }else{
                $page['published_by_stat'] = \yii\helpers\Html::checkbox("published_by_stat",false);
            }
        }else{
            $page['published_by_stat'] = \yii\helpers\Html::checkbox("published_by_stat");
        }
        
        $pages = ArticlesCategories::getMyEventsCategories();
        $page_map =  yii\helpers\ArrayHelper::map($pages, 'id', 'name');
        $page['category'] = \yii\helpers\Html::checkboxList("category",$page_arr,$page_map);
        
        $pages = Forms::find()->where(['form_type'=>'form-feedback'])->all();
        $page_map =  yii\helpers\ArrayHelper::map($pages, 'id', 'title');
        $page['feedback'] = \yii\helpers\Html::dropDownList("feedback",$page['rs']['feedback'],$page_map,['prompt'=>'Select a form','class'=>'form-control']);
        
        
        
        $pages = Pages::getBlogIndex();
        $page_map =  yii\helpers\ArrayHelper::map($pages, 'id', 'title');
        $page['blog_index'] = \yii\helpers\Html::checkboxList("blog_index",$index_arr,$page_map);
        $page['home_url'] = Settings::find()->where(['setting_name'=>'home_url'])->one();
        $page['records'] = Articles::find()->all();  
        $brender= Templates::find()->where(['route'=>'blog/details'])->all();
        $blog_map =  yii\helpers\ArrayHelper::map($brender, 'id', 'name');
        $child_blog_render= Templates::find()->where(['parent_id'=>$brender[0]['id']])->all();
        $render_map =  yii\helpers\ArrayHelper::map($child_blog_render, 'id', 'name');
        $page['renderer'] = \yii\helpers\Html::dropDownList("render_template",$page['rs']['render_template'],array_merge($blog_map,$render_map),['class'=>'form-control']);
        $pages = Roles::find()->orderBy('name')->all();
        $page_map =  yii\helpers\ArrayHelper::map($pages, 'id', 'name');
        $page['permissions'] = \yii\helpers\Html::checkboxList("permissions",$perm_arr,$page_map);
        return $this->render('index',$page);        
    }

public function actionSave(){
    if(Yii::$app->request->post("processor")=="true"){  
            $model = Articles::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                ArticleMedia::deleteAll(['article_id'=>Yii::$app->request->post("id")]);
                $img_array = Yii::$app->request->post("document_listing");            
                $ev_arr = explode(" ",$img_array);
                for($i=0; $i < count($ev_arr);$i++): 
                    if(trim($ev_arr[$i])!=""):
                            $model = new ArticleMedia();
                            $random=rand(1000,10000);
                            $model->setAttribute("id",md5(date('YmdHis')).$random);
                            $model->setAttribute("article_id",Yii::$app->request->post("id"));
                            $model->setAttribute("media_id",$ev_arr[$i]);
                            $model->save();                            
                    endif;
                endfor;
                $db_arr=array("category","blog_index","articles_id","url","id","processor","tag_array","date","title","featured_media","published","published_by_stat","render_template","sort_order","require_login","article_type","lead_content","body_content");
                        $id = Yii::$app->request->post("id");
                        foreach($_POST as $key => $value)
                        {
                                //if there are more fields in this form, we should extend the information and store in the data model
                                $a = ArticleDetails::deleteAll(['article_id'=>$id,'param'=>$key]);
                                if($value<>"" && !in_array($key,$db_arr) && $value!=null):                                    
                                    $profile_data = new ArticleDetails();
                                    $profile_data->setAttribute("article_id",$id);
                                    $profile_data->setAttribute("param",$key);
                                    $profile_data->setAttribute("param_val",$value);
                                    //$profile_data->save(false);
                                endif;
                        }
            endif;
            echo Articles::saveArticle();                        
    }
}
public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Articles::findOne($id);
    $a->delete();
    //we also delete the reference in other pages
    ArticlesBlogIndex::deleteAll(['articles_id'=>Yii::$app->request->get("id")]);
    ArticlesCategoryRelated::deleteAll(['articles_id'=>Yii::$app->request->get("id")]);
    ArticlesTag::deleteAll(['articles_id'=>Yii::$app->request->get("id")]);
    ArticleMedia::deleteAll(['article_id'=>Yii::$app->request->get("id")]);
    ArticleDetails::deleteAll(['article_id'=>Yii::$app->request->get("id")]);    
    echo "Record successfully deleted";
}
public function actionDuplicate(){
    echo Articles::saveArticle("saveasnew");  
    
}

public function actionCategory(){
        
        $page=[]; 
        $page['rs']=[];
        $page['id'] = Yii::$app->request->get('id');
        $page_arr="";
        
        if($page['id']){            
            $page['rs'] = ArticlesCategories::find()->where(['id' => $page['id']])->one();
            $page['edit']=true;
            $c = ArticlesCategories::getcategoryIndex($page['id']);
            $page_arr =  yii\helpers\ArrayHelper::getColumn($c, 'category_index_id');
        }else{
            $page['rs'] = ArticlesCategories::find()->where(['id' => '0'])->one();
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
        
        $pages = Pages::getCategoryIndex();
        $page_map =  yii\helpers\ArrayHelper::map($pages, 'id', 'title');
        $page['category'] = \yii\helpers\Html::checkboxList("category",$page_arr,$page_map);
        
        $page['records'] = ArticlesCategories::getMyEventsCategories();     
        return $this->render('category',$page);        
    }

public function actionSaveCategory(){ 
    if(Yii::$app->request->post("processor")=="true"){            
            echo ArticlesCategories::saveEventsCategory();                        
    }
}
public function actionCategoryDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = ArticlesCategories::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
}
public function actionDeleteAttachment(){
    ArticleMedia::deleteAll(['article_id'=>Yii::$app->request->get('article_id'),'media_id'=>Yii::$app->request->get('id')]);
    echo "Record successfully deleted";
}
public function actionAddTag(){
    //we check to see if the tag already exist and if not we create it and get the ID
    $tag = Tags::find()->where(['name'=>Yii::$app->request->get('tag')])->one();
    if($tag!=null):
        return $tag->id;
    else:
            
            if(Yii::$app->request->get("url")==""):
                $url=strtolower(str_replace(" ","-",Yii::$app->request->get("tag")));            
            endif;
           $url_gen=0;
           $url_exist = Pages::find()->where(['url'=>$url])->one();
           if($url_exist!=null):
               $url_gen=1;
               $url=$url."_".md5(date("YmdHis").rand(10000,1000000));
           endif;
           if($url_gen==0):
            $url_exist = Tags::find()->where(['url'=>$url])->one();
            if($url_exist!=null):
                $url_gen=1;
                $url=$url."_".md5(date("YmdHis").rand(10000,1000000));
            endif;
           endif;
           if($url_gen==0):
            $url_exist = Templates::find()->where(['url'=>$url])->one();
            if($url_exist!=null):
                $url_gen=1;
                $url=$url."_".md5(date("YmdHis").rand(10000,1000000));
            endif;
           endif;
           if($url_gen==0):
            $url_exist = ArticlesCategories::find()->where(['url'=>$url])->one();
            if($url_exist!=null):
                $url_gen=1;
                $url=$url."_".md5(date("YmdHis").rand(10000,1000000));
            endif;
           endif;
           $records = new Tags();
           $id = md5(date('Ymdis').rand(1000,100000));
           $records->setAttribute('id',$id);
           $records->setAttribute('url',$url);
           $records->setAttribute('name',Yii::$app->request->get("tag"));
           $records->setAttribute('description',"");
           $records->setAttribute('master_content','1');
           $records->save();  
           return $id;
    endif;
}
public function actionDetails(){
        $page=[];
        $page['article'] = Articles::find()->where(['id'=>Yii::$app->request->get("article")])->one();
        $page['records'] = ArticleDetails::find()->where(['article_id'=>Yii::$app->request->get("article")])->all();
        return $this->renderPartial("details",$page);
}
public function actionSaveProfileDetails(){    
    foreach($_POST as $key => $value)
                        {
                                //if there are more fields in this form, we should extend the information and store in the data model
                                $a = ArticleDetails::deleteAll(['article_id'=>Yii::$app->request->post("article_id"),'param'=>$key]);
                                if($value<>""):                                    
                                    $profile_data = new ArticleDetails();
                                    $profile_data->setAttribute("article_id",Yii::$app->request->post("article_id"));
                                    $profile_data->setAttribute("param",$key);
                                    $profile_data->setAttribute("param_val",$value);
                                    $profile_data->save();
                                endif;
                        }
    return "Details saved";
}
}


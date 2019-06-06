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
namespace common\components;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\db\Expression;
use Yii;
use frontend\models\ClassSetup;
use backend\models\ClassElement;
use backend\models\ClassAttributes;
use frontend\models\Blocks;
use backend\models\BlockGroup;
use backend\models\BlockGroupList;
use backend\models\Templates;
use frontend\models\FormData;
use frontend\models\FormSubmit;
use backend\models\Forms;
use backend\models\Roles;
use frontend\components\ContentBuilder;
use frontend\models\Twig;
use frontend\models\Users;
use frontend\models\ProfileDetails;
use frontend\models\Themes;

class GUIBehavior extends Behavior
{
    
   public $fields;
   public $gui_type; //this can be select , checkbox, radio buttons

    public function events()
    {
        return [
            // after find event
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',            
        ];
    }
    
    public function afterFind(){
        //we define the shortcodes here and then process when a match is found in this function
        $pattern = "/{yumpee_class}(.*?){\/yumpee_class}/";        
        $pattern_data = "/{yumpee_data}(.*?){\/yumpee_data}/";
        $pattern_user = "/{yumpee_user}(.*?){\/yumpee_user}/";
        $pattern_login_to_view="/{yumpee_login_to_view}(.*?){\/yumpee_login_to_view}/";
        $pattern_hide_on_login="/{yumpee_hide_on_login}(.*?){\/yumpee_hide_on_login}/";
        $pattern_setting= "/{yumpee_setting}(.*?){\/yumpee_setting}/";
        $pattern_twig= "/{yumpee_include}(.*?){\/yumpee_include}/";
        $pattern_get= "/{yumpee_get}(.*?){\/yumpee_get}/";
        $pattern_post= "/{yumpee_post}(.*?){\/yumpee_post}/";
        $pattern_block= "/{yumpee_block}(.*?){\/yumpee_block}/";
        $pattern_block_group= "/{yumpee_block_group}(.*?){\/yumpee_block_group}/";
        $pattern_widget="/{yumpee_widget}(.*?){\/yumpee_widget}/";
        $pattern_backend="/{yumpee_backend_view}(.*?){\/yumpee_backend_view}/";
        $pattern_map="/{yumpee_map}(.*?){\/yumpee_map}/";
        $pattern_role= "/{yumpee_role:(.*?)}(.*?){\/yumpee_role}/";
        $pattern_env = "/{yumpee_env}(.*?){\/yumpee_env}/";
        $pattern_menu = "/{yumpee_menu}(.*?){\/yumpee_menu}/";
        $pattern_translate_full= "/{yumpee_t:(.*?)}(.*?){\/yumpee_t}/";
        $pattern_translate= "/{yumpee_t}(.*?){\/yumpee_t}/";
        $pattern_submit="/{yumpee_submit}(.*?){\/yumpee_submit}/";
        $pattern_testimonial="/{yumpee_testimonial}(.*?){\/yumpee_testimonial}/";
        $pattern_article="/{yumpee_article}(.*?){\/yumpee_article}/";
        $pattern_page="/{yumpee_page}(.*?){\/yumpee_page}/";
        $pattern_comment="/{yumpee_comment}(.*?){\/yumpee_comment}/";
        $pattern_gallery="/{yumpee_gallery}(.*?){\/yumpee_gallery}/";
        
		
		$themes = new Themes();
		$theme_id=$themes->dataTheme;
		
		if($theme_id=="0"):
			$theme_id = ContentBuilder::getSetting("current_theme");			
		endif;
							
        foreach($this->owner->fields as $field):
        $content = $this->owner->{$field};  
        //$content = str_replace("\r\n","",$content);
        $content = preg_replace_callback($pattern_setting,function ($matches) use($theme_id){
                            $replacer = ContentBuilder::getSetting($matches[1],$theme_id);                            
                            return $replacer;
                    },$content); 
        $content = preg_replace_callback($pattern_get,function ($matches) {
                            $replacer="";
                            $replacer=Yii::$app->request->get($matches[1]);
                            return $replacer;
                    },$content); 
                  
        $content = preg_replace_callback($pattern_post,function ($matches) {
                            $replacer="";
                            $replacer=Yii::$app->request->post($matches[1]);
                            return $replacer;
                    },$content);         
        $content = preg_replace_callback($pattern_translate_full,function ($matches) {
                            $replacer="";
                            if($matches[2]<>"0"):
                                //this means we are setting the default language
                                \Yii::$app->language = $matches[2]; 
                            endif;
                            $replacer=\Yii::t('app',$app->request->get($matches[1]));
                            return $replacer;
                    },$content); 
        $content = preg_replace_callback($pattern_translate,function ($matches) {
                            $replacer="";                            
                            $replacer=\Yii::t('app',$app->request->get($matches[1]));
                            return $replacer;
                    },$content);
        $content = preg_replace_callback($pattern_env,function ($matches) {
                            $replacer=null;
                            if($matches[1]=="pathInfo"):
                                $replacer=Yii::$app->request->pathInfo;
                            endif;
                            if($matches[1]=="url"):
                                $replacer=ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
                            endif;
                            if($matches[1]=="username" && Yii::$app->user->identity!=null):
                                $replacer=Yii::$app->user->identity->username;
                            endif;
                            if($matches[1]=="role_id" && Yii::$app->user->identity!=null):
                                $replacer=Yii::$app->user->identity->role_id;
                            endif;
                            return $replacer;
                    },$content);  
        $content = preg_replace_callback($pattern,function ($matches) {
                            $replacer="";
                            $elements=[];
                            list($name,$attribute,$id) = preg_split("/:/",preg_replace("/}/",":",$matches[1]));
                            $class_setup = ClassSetup::find()->where(['name'=>$name])->one();
                            if(trim($attribute=="child")):
                                if($id=="*"):
                                        $elements = ClassSetup::find()->with('displayImage','parent','child','list')->asArray()->where(['parent_id'=>$class_setup->id])->orderBy(['display_order'=>SORT_ASC,'alias'=>SORT_ASC])->all();
                                    else:
                                        $elements = ClassSetup::find()->where(['name'=>$id])->orderBy(['display_order'=>'SORT_ASC'])->one();
                                endif;
                                
                            endif;
                            if(trim($attribute)=="list"||trim($attribute)=="elements"):   
                                if($id=="*"):
                                    $elements = ClassElement::find()->with('displayImage','parent','child')->asArray()->where(['class_id'=>$class_setup['id']])->orderBy(['display_order'=>SORT_ASC,'alias'=>SORT_ASC])->all();
                                elseif($id=="parent"):
                                    $elements = ClassElement::find()->with('displayImage','parent','child')->asArray()->where(['class_id'=>$class_setup['id']])->andWhere("parent_id=''")->orderBy(['display_order'=>SORT_ASC,'alias'=>SORT_ASC])->all();
                                else:
                                    $elements = ClassElement::find()->with('displayImage','parent','child')->asArray()->where(['class_id'=>$class_setup['id']])->andWhere("name='".$id."'")->orderBy('alias')->one();
                                endif;
                            endif;
                            if(trim($attribute)=="property"):   
                                if($id=="*"):
                                    $elements = ClassAttributes::find()->where(['class_id'=>$class_setup['id']])->orderBy(['display_order'=>SORT_ASC,'alias'=>SORT_ASC])->all();
                                else:
                                    $elements = ClassAttributes::find()->where(['class_id'=>$class_setup['id']])->andWhere("name='".$id."'")->orderBy('alias')->one();
                                endif;
                            endif;
							if($elements!=null){
								$replacer = \yii\helpers\Json::encode($elements);
							}
                            return $replacer;
                    },$content);
                    
        $content = preg_replace_callback($pattern_data,function ($matches) {
                            $replacer="";
                            $order="";
                            $limit="";
                            $params=null;
                            $data_query=[];
                            $sent_data = explode(":",$matches[1]);
                            if($sent_data[0]!=null):
                                $name = $sent_data[0];
                            endif;
                            if(count($sent_data) > 1):
                                $params = $sent_data[1];
                            endif;
                            if(count($sent_data) > 2):
                                $order = $sent_data[2];
                            endif;
                            if(count($sent_data) > 3):
                                $limit = $sent_data[3];
                            endif;
                            //list($name,$params) = explode(":",$matches[1]);
                            $form = Forms::find()->select('id')->where(['name'=>$name])->one();
                            //we handle filtering of data search parameters
                            
                            $andWhere="";
                            if($params!=null):
                                $data_query = FormData::find()->select('form_submit_id');
                                $search_params=explode("|",$params);
                                $counter=0;
                                
                                foreach($search_params as $param):
                                    list($p,$v)=explode("=",$param);
                                    if(trim($p)=="url"):
                                        $andWhere="url='".$v."'";
                                        continue;
                                    endif;
                                    if(trim($p)=="usrname"):
                                        $andWhere="usrname='".$v."'";
                                        continue;
                                    endif;
                                    if($counter==0):
                                        $data_query->andWhere('param="'.$p.'"')->andFilterCompare('param_val',$v);
                                    else:
                                        $data_query->andWhere('param="'.$p.'"')->orWhere(['like','param_val',$v])->andFilterCompare('param_val',$v);
                                    endif;
                                    $counter++;
                                endforeach;
                                $data_query->all();
                            endif;
                            $submit_query = FormSubmit::find();
                            $order_sorted=0;
                            if($order!=""):
                                if($order=="random"):
                                    $submit_query->orderBy(new Expression('rand()'));
                                    $order_sorted=1;
                                endif;
                                if($order=="last"):
                                    $submit_query->orderBy(['date_stamp'=>SORT_DESC]);
                                    $order_sorted=1;
                                endif;
                                if($order=="first"):
                                    $submit_query->orderBy(['date_stamp'=>SORT_ASC]);
                                    $order_sorted=1;
                                endif;
                                if($order=="views"):
                                    $submit_query->orderBy(['no_of_views'=>SORT_DESC]);
                                    $order_sorted=1;
                                endif;
                                if($order=="user"):
                                    $submit_query->orderBy(['usrname'=>SORT_ASC]);
                                    $order_sorted=1;
                                endif;
                                if($order=="rating"):
                                    $submit_query->orderBy(['rating'=>SORT_DESC]);
                                    $order_sorted=1;
                                endif;                               
                                
                                if($order=="count"):
                                    if($andWhere!=""):
                                        $elements = $submit_query->where(['IN','id',$data_query])->andWhere('form_id="'.$form->id.'"')->andWhere($andWhere);
                                    else:
                                        $elements = $submit_query->where(['IN','id',$data_query])->andWhere('form_id="'.$form->id.'"');
                                    endif;
                                    $elements = $submit_query->count();
                                    $replacer = \yii\helpers\Json::encode($elements);
                                    return $replacer;
                                endif;
                                if($order_sorted==0):
                                    $order_arr= explode(" ",$order);
                                    $ordering="";
                                    if(sizeof($order_arr) > 1):
                                        $ordering = $order_arr[1];
                                        $order=trim($order_arr[0]);
                                    endif;
                                    if(trim($ordering)=="DESC"):
                                        if($limit!=""):
                                            $submit_arr = FormData::find()->select('form_submit_id')->where(['param'=>$order])->orderBy(['param_val'=>SORT_DESC])->offset($limit)->asArray()->column();
                                        else:
                                            $submit_arr = FormData::find()->select('form_submit_id')->where(['param'=>$order])->orderBy(['param_val'=>SORT_DESC])->asArray()->column();
                                        endif;                                        
                                    else:
                                        if($limit!=""):
                                            $submit_arr = FormData::find()->select('form_submit_id')->where(['param'=>$order])->orderBy(['param_val'=>SORT_ASC])->offset($limit)->asArray()->column();
                                        else:
                                            $submit_arr = FormData::find()->select('form_submit_id')->where(['param'=>$order])->orderBy(['param_val'=>SORT_ASC])->asArray()->column();  
                                        endif;
                                        
                                    endif;
                                    $submit_query->andWhere(['in','id',$submit_arr])->orderBy(new Expression('FIND_IN_SET (id,:form_submit_id)'))->addParams([':form_submit_id'=>implode(",",$submit_arr)]);
                                    
                                endif;
                            endif;
                            if($limit!=""):
                                $submit_query->limit($limit);
                            endif;
							
                            if($andWhere!=""){
				$elements = $submit_query->with('data','file','user','user.displayImage','data.setup','data.setupVal','data.element','data.elementVal','data.property','data.propertyVal')->asArray()->where(['IN','id',$data_query])->andWhere('form_id="'.$form->id.'"')->andWhere($andWhere)->all();
				}else{
				$elements = $submit_query->with('data','file','user','user.displayImage','data.setup','data.setupVal','data.element','data.elementVal','data.property','data.propertyVal')->asArray()->where(['IN','id',$data_query])->andWhere('form_id="'.$form->id.'"')->all();
                            }
                            $replacer = \yii\helpers\Json::encode($elements);
                            return $replacer;
                    },$content); 
                    
        $content = preg_replace_callback($pattern_user,function ($matches) {
                            $replacer="";
                            $order="";
                            $limit="";
                            $params=null;
                            $data_query=[];
                            $sent_data = explode(":",$matches[1]);
                            
                            if($sent_data[0]!=null):
                                $params = $sent_data[0];
                            endif;
                            if(isset($sent_data[1])):
                                $order = $sent_data[1];
                            endif;
                            if(isset($sent_data[2])):
                                $limit = $sent_data[2];
                            endif;                            
                            
                            $andWhere="";
                            if($params!=null):
                                $data_query = ProfileDetails::find()->select('profile_id');
                                $search_params=explode("|",$params);
                                $counter=0;
                                $search_criteria=array('username','first_name','last_name','title','role_id','email','status','created_at','updated_at');
                                
                                foreach($search_params as $param):
                                    list($p,$v)=explode("=",$param);
                                    if(in_array($p,$search_criteria)):
                                        //we add this to the submit query condition later on
                                        $andWhere=$p."='".$v."'";
                                        continue;
                                    endif;
                                    if($counter==0):
                                        $data_query->andWhere('param="'.$p.'"')->andFilterCompare('param_val',$v);
                                    else:
                                        $data_query->andWhere('param="'.$p.'"')->orWhere(['like','param_val',$v])->andFilterCompare('param_val',$v);
                                    endif;
                                    $counter++;
                                endforeach;
                                $data_query->all();
                            endif;
                            $submit_query = Users::find();
                            
                            if($order!=""):
                                if($order=="random"):
                                    $submit_query->orderBy(new Expression('rand()'));
                                endif;
                                if($order=="last"):
                                    $submit_query->orderBy(['created_at'=>SORT_DESC]);
                                endif;
                                if($order=="first"):
                                    $submit_query->orderBy(['created_at'=>SORT_ASC]);
                                endif;
                                if($order=="views"):
                                    $submit_query->orderBy(['no_of_views'=>SORT_DESC]);
                                endif;
                                if($order=="user"):
                                    $submit_query->orderBy(['username'=>SORT_ASC]);
                                endif;
                                if($order=="count"):
                                    if($andWhere!=""):
                                        $elements = $submit_query->where(['IN','id',$data_query])->andWhere($andWhere);
                                    else:
                                        if(!empty($data_query)):
                                            $elements = $submit_query->where(['IN','id',$data_query]);
                                        endif;
                                    endif;
                                    $elements = $submit_query->count();
                                    $replacer = \yii\helpers\Json::encode($elements);
                                    return $replacer;
                                endif;
                            endif;
                            if($limit!=""):
                                $submit_query->limit($limit);
                            endif;
							
                            if($andWhere!=""){
                                    if(!empty($data_query)):
                                        $elements = $submit_query->with('details','profileFiles')->asArray()->where(['IN','id',$data_query])->andWhere($andWhere)->all();
                                    else:
                                        $elements = $submit_query->with('details','profileFiles')->asArray()->andWhere($andWhere)->all();
                                    endif;
				}else{
                                    if(!empty($data_query)):
                                            $elements = $submit_query->with('details','profileFiles')->asArray()->where(['IN','id',$data_query])->all();
                                    else:
                                            $elements = $submit_query->with('details','profileFiles')->asArray()->all();
                                    endif;
                            }
                            $replacer = \yii\helpers\Json::encode($elements);
                            return $replacer;
                    },$content);
                    
        $content = preg_replace_callback($pattern_testimonial,function ($matches) {
                            $replacer="";
                            if($matches[1]<>"all"):
                                $testimonial_arr=\backend\models\Testimonials::find()->limit($matches[1])->all();
                            else:
                                $testimonial_arr=\backend\models\Testimonials::find()->all();
                            endif;
                            
                            return \yii\helpers\Json::encode($testimonial_arr);
                    },$content);
        $content = preg_replace_callback($pattern_comment,function ($matches) {
                            $replacer="";
                            if($matches[1]<>"all"):
                                $testimonial_arr=\backend\models\Comments::find()->limit($matches[1])->all();
                            else:
                                $testimonial_arr=\backend\models\Comments::find()->all();
                            endif;
                            
                            return \yii\helpers\Json::encode($testimonial_arr);
                    },$content);
        $content = preg_replace_callback($pattern_gallery,function ($matches) {
                            $replacer="";
                            $gallery_arr=[];
                            if($matches[1]<>"all"):
                                $gallery_arr=\backend\models\Gallery::find()->with('items')->asArray()->where(['name'=>$matches[1]])->one();                            
                            endif;
                            
                            return \yii\helpers\Json::encode($gallery_arr);
                    },$content);
        //process page calls
        $content = preg_replace_callback($pattern_page,function ($matches) { 
            if($matches[1]=="*"):
                $page_arr = \frontend\models\Pages::find()->all();
            else:
                $params = explode(":",$matches[1]);
                if($params[0]!=null):
                    $submit_query = \frontend\models\Pages::find();
                     $filter = explode("|",$params[0]);
                                foreach ($filter as $filter_rec):
                                    list($v,$p)=explode("=",$filter_rec);
                                    if($v=="route"):
                                        $template = \backend\models\Templates::find()->where(['route'=>$p])->one();
                                        $submit_query->where(['template'=>$template['id']]);
                                    endif;
                                endforeach;
                                if($params[1]!=null):
                                    $limit = $params[1];
                                    $submit_query->limit($limit);
                                endif;
                                if($params[2]!=null):
                                    $p=explode(" ",$params[2]);
                                    if(count($p) > 1):
                                        if($p[1]=="DESC"):
                                            $sort="SORT_DESC";
                                        else:
                                            $sort="SORT_ASC";
                                        endif;
                                    else:
                                        $sort="SORT_ASC";
                                    endif;
                                    $orderby=$p[0];
                                    if($params[2]=="last"):                                    
                                        $submit_query->orderBy(['updated'=>SORT_DESC]);
                                    elseif($params[2]=="first"):
                                        $submit_query->orderBy(['updated'=>SORT_ASC]);
                                    else:
                                        if($sort=="SORT_DESC"):
                                            $submit_query->orderBy([$orderby=>SORT_DESC]);
                                        else:
                                            $submit_query->orderBy([$orderby=>SORT_ASC]);
                                        endif;
                                    endif;
                                endif;
                                $page_arr = $submit_query->all();
                                return \yii\helpers\Json::encode($page_arr);
                endif;
                $page_arr = \frontend\models\Pages::find()->where(['url'=>$matches[1]])->one();
            endif;
            return \yii\helpers\Json::encode($page_arr);
        },$content);
        //process Articles call
        $content = preg_replace_callback($pattern_article,function ($matches) {                            
                            $limit=3;
                            $submit_query = \backend\models\Articles::find();
                            $params = explode(":",$matches[1]);
                            if($params[0]!=null):
                                $filter = explode("|",$params[0]);
                                foreach ($filter as $filter_rec):
                                    list($v,$p)=explode("=",$filter_rec);
                                    if($v=="index"):                                        
                                        $page = \backend\models\Pages::find()->where(['url'=>$p])->one();
                                        $blog_index_articles = \backend\models\ArticlesBlogIndex::find()->select('articles_id')->where(['blog_index_id'=>$page['id']])->column();                                        
                                        $submit_query->where(['IN','id',$blog_index_articles]);
                                    endif;
                                    if($v=="category"):
                                        $page = \backend\models\ArticlesCategories::find()->where(['url'=>$p])->one();
                                        $blog_index_articles = \backend\models\ArticlesCategoryRelated::find()->select('articles_id')->where(['category_id'=>$page['id']])->column();
                                        $submit_query->where(['IN','id',$blog_index_articles]);
                                    endif;
                                endforeach; 
                            endif;
                            if($params[1]!=null):
                                $limit = $params[1];
                                $submit_query->limit($limit);
                            endif;
                            if($params[2]!=null):
                                $p=explode(" ",$params[2]);
                                if(count($p) > 1):
                                    if($p[1]=="DESC"):
                                        $sort="SORT_DESC";
                                    else:
                                        $sort="SORT_ASC";
                                    endif;
                                else:
                                        $sort="SORT_ASC";
                                endif;
                                $orderby=$p[0];
                                if($params[2]=="last"):                                    
                                    $submit_query->orderBy(['date'=>SORT_DESC]);
                                elseif($params[2]=="first"):
                                    $submit_query->orderBy(['date'=>SORT_ASC]);
                                else:
                                    if($sort=="SORT_DESC"):
                                        $submit_query->orderBy([$orderby=>SORT_DESC]);
                                    else:
                                        $submit_query->orderBy([$orderby=>SORT_ASC]);
                                    endif;
                                endif;
                                
                            endif;
                            $article_arr = $submit_query->with('documents','feedback','details','approvedComments','displayImage','blogIndex','blogIndex.page','author')->asArray()->all();                            
                            return \yii\helpers\Json::encode($article_arr);
                    },$content);
        //End of Articles shortcodes
                    
        $content = preg_replace_callback($pattern_menu,function ($matches) {
                            $replacer="";
                            $menu_arr=\backend\models\MenuProfile::find()->where(['name'=>$matches[1]])->one();
                            if($menu_arr==null):
                                $menu_id=0;
                            else:
                                $menu_id = $menu_arr['id'];
                            endif;
                            
                            return \yii\helpers\Json::encode(\backend\models\Menus::getProfileMenus($menu_id));
                    },$content);
        $content = preg_replace_callback($pattern_login_to_view,function ($matches) {
                            $replacer="";
                            if(Yii::$app->user->id):
                                //list($replacer) = preg_split("/:/",preg_replace("/}/",":",$matches[1]));
                                return $matches[1]; 
                            else:                                
                                $replacer="";
                            endif;
                            return $replacer;
                    },$content);  
        $content = preg_replace_callback($pattern_hide_on_login,function ($matches) {
                            $replacer="";
                            if(Yii::$app->user->isGuest):
                                //list($replacer) = preg_split("/:/",preg_replace("/}}/",":",$matches[1]));
                                return $matches[1]; 
                            else:
                                $replacer="";
                            endif;
                            
                            return $replacer;
                    },$content);  
        $content = preg_replace_callback($pattern_role,function ($matches) {
                            if($matches[1]=="all"):
                                $replacer = Roles::find()->orderBy('name')->all(); 
                                return \yii\helpers\Json::encode($replacer);
                            endif;
                        if(Yii::$app->user->id):
                            
                            $role = Roles::find()->where(['id'=>Yii::$app->user->identity->role_id])->one();
                            if($role->name==$matches[1]):
                                return $matches[2];
                            else:
                                return "";
                            endif;
                        endif;
                    },$content);
        $array=$content;           
        $array = preg_replace_callback($pattern_setting,function ($matches) {
                            if($matches[1]=="yumpee_role_home_page"):
                                return ContentBuilder::getRoleHomePage();
                            endif;
                            $replacer = ContentBuilder::getSetting($matches[1]);                            
                            return $replacer;
                    },$array); 
        $array = preg_replace_callback($pattern_widget,function ($matches) {
                            $replacer = "<div class=\"yumpee_custom_widget:".$matches[1]."\"></div>";                            
                            return $replacer;
                    },$array);
        $array = preg_replace_callback($pattern_backend,function ($matches) {
                            return "";
                    },$array);
        $array = preg_replace_callback($pattern_block,function ($matches) {
                            $replacer = Blocks::find()->where(['name'=>$matches[1]])->one();                            
                            if($replacer<>null):
                                return \yii\helpers\Json::encode($replacer);
                            else:
                                return \yii\helpers\Json::encode(['']);
                            endif;
                    },$array); 
        $array = preg_replace_callback($pattern_block_group,function ($matches) {
                            list($name,$style,$limit) = explode(":",$matches[1]);
                            $block_group_id = BlockGroup::find()->where(['name'=>$name])->one();
                            if($block_group_id<>null):
                                $block_array = BlockGroupList::find()->select('block_id')->where(['group_id'=>$block_group_id->id])->column();
                                $replacer = Blocks::find()->where(['IN','id',$block_array])->orderBy(['name'=>SORT_ASC])->limit($limit)->all();
                                if($style=="random"):
                                    $replacer = Blocks::find()->where(['IN','id',$block_array])->orderBy(new Expression('rand()'))->limit($limit)->all();                            
                                endif;
                                return \yii\helpers\Json::encode($replacer);
                            else:
                                return \yii\helpers\Json::encode(['']);
                            endif;
                    },$array); 
                    
        $array = preg_replace_callback($pattern_twig,function ($matches) use($theme_id){
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader); 
                            $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');
                            $metadata['param'] = Yii::$app->request->csrfParam;
                            $metadata['token'] = Yii::$app->request->csrfToken;      
                            
                            $content= $twig->render(Twig::find()->where(['renderer'=>$matches[1],'theme_id'=>$theme_id])->one()->filename,['app'=>Yii::$app,'metadata'=>$metadata]);
                            return $content;
                            //return $replacer;
                    },$array); 
        
        
        $array = preg_replace_callback($pattern_map,function($matches){
                    $argos = explode(":",$matches[1]);
                    if($argos[0]=="class"):
                       $cat_name = preg_replace('/\s+/', '', $argos[1]);
                       $ad = ClassSetup::find()->where(['name'=>trim($cat_name)])->one();
                       if($ad<>null):
                        return $ad->alias;
                       else:
                        return $argos[1];
                       endif;
                       
                    endif;
                    if($argos[0]=="property"):
                       $cat_name = preg_replace('/\s+/', '', $argos[1]);
                       $ad = ClassAttributes::find()->where(['name'=>trim($argos[1])])->one();
                       if($ad<>null):
                        return $ad->alias;
                       else:
                        return $argos[1];
                       endif;                       
                    endif;
                    
        },$array);
        
        $array = preg_replace_callback($pattern_submit,function ($matches) {
                            $argos = explode("|",$matches[1]);
                            return "";
        },$array);
        
         
        $this->owner->{$field}=$array;   
        
        
        endforeach;
    }
    
}
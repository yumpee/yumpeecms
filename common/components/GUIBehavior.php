<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 * This Behavior class is used to translate all components bound to the class list defined in System->Class Setup
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
use frontend\models\FormData;
use frontend\models\FormSubmit;
use backend\models\Forms;
use backend\models\Roles;
use frontend\components\ContentBuilder;
use frontend\models\Twig;
use frontend\models\Users;
use frontend\models\ProfileDetails;

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
        $pattern = "/{yumpee_class}(.*?){\/yumpee_class}/";
        //this method searches through
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
        
        foreach($this->owner->fields as $field):
        $content = $this->owner->{$field};  
        //$content = str_replace("\r\n","",$content);
        $content = preg_replace_callback($pattern_setting,function ($matches) {
                            $replacer = ContentBuilder::getSetting($matches[1]);                            
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
                            return $replacer;
                    },$content);  
        $array = preg_replace_callback($pattern,function ($matches) {
                            $replacer="";
                            $elements=[];
                            list($name,$attribute,$id) = preg_split("/:/",preg_replace("/}/",":",$matches[1]));
                            $class_setup = ClassSetup::find()->where(['name'=>$name])->one();
                            if(trim($attribute=="child")):
                                if($id=="*"):
                                        $elements = ClassSetup::find()->with('displayImage','parent','child')->asArray()->where(['parent_id'=>$class_setup->id])->orderBy(['display_order'=>SORT_ASC,'alias'=>SORT_ASC])->all();
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
                            $replacer = \yii\helpers\Json::encode($elements);
                            return $replacer;
                    },$content);
                    
        $array = preg_replace_callback($pattern_data,function ($matches) {
                            $replacer="";
                            $order="";
                            $limit="";
                            $params=null;
                            $data_query=[];
                            $sent_data = explode(":",$matches[1]);
                            if($sent_data[0]!=null):
                                $name = $sent_data[0];
                            endif;
                            if($sent_data[1]!=null):
                                $params = $sent_data[1];
                            endif;
                            if(isset($sent_data[2])):
                                $order = $sent_data[2];
                            endif;
                            if(isset($sent_data[3])):
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
                            
                            if($order!=""):
                                if($order=="random"):
                                    $submit_query->orderBy(new Expression('rand()'));
                                endif;
                                if($order=="last"):
                                    $submit_query->orderBy(['date_stamp'=>SORT_DESC]);
                                endif;
                                if($order=="first"):
                                    $submit_query->orderBy(['date_stamp'=>SORT_ASC]);
                                endif;
                                if($order=="views"):
                                    $submit_query->orderBy(['no_of_views'=>SORT_DESC]);
                                endif;
                                if($order=="user"):
                                    $submit_query->orderBy(['usrname'=>SORT_ASC]);
                                endif;
                                if($order=="rating"):
                                    $submit_query->orderBy(['rating'=>SORT_DESC]);
                                endif;  
                                if($order=="count"):
                                    if($andWhere!=""){
                                        $elements = $submit_query->where(['IN','id',$data_query])->andWhere('form_id="'.$form->id.'"')->andWhere($andWhere);
                                    }else{
                                        $elements = $submit_query->where(['IN','id',$data_query])->andWhere('form_id="'.$form->id.'"');
                                    }
                                    $elements = $submit_query->count();
                                    $replacer = \yii\helpers\Json::encode($elements);
                                    return $replacer;
                                endif;
                            endif;
                            if($limit!=""):
                                $submit_query->limit($limit);
                            endif;
							
                            if($andWhere!=""){
				$elements = $submit_query->with('data','file','user')->asArray()->where(['IN','id',$data_query])->andWhere('form_id="'.$form->id.'"')->andWhere($andWhere)->all();
				}else{
				$elements = $submit_query->with('data','file','user')->asArray()->where(['IN','id',$data_query])->andWhere('form_id="'.$form->id.'"')->all();
                            }
                            $replacer = \yii\helpers\Json::encode($elements);
                            return $replacer;
                    },$array); 
                    
        $array = preg_replace_callback($pattern_user,function ($matches) {
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
                    },$array);
        
        $array = preg_replace_callback($pattern_menu,function ($matches) {
                            $replacer="";
                            $menu_arr=\backend\models\MenuProfile::find()->where(['name'=>$matches[1]])->one();
                            if($menu_arr==null):
                                $menu_id=0;
                            else:
                                $menu_id = $menu_arr['id'];
                            endif;
                            return \yii\helpers\Json::encode(\backend\models\Menus::getProfileMenus($menu_id));
                    },$array);
        $array = preg_replace_callback($pattern_login_to_view,function ($matches) {
                            $replacer="";
                            if(Yii::$app->user->id):
                                //list($replacer) = preg_split("/:/",preg_replace("/}/",":",$matches[1]));
                                return $matches[1]; 
                            else:                                
                                $replacer="";
                            endif;
                            return $replacer;
                    },$array);  
        $array = preg_replace_callback($pattern_hide_on_login,function ($matches) {
                            $replacer="";
                            if(Yii::$app->user->isGuest):
                                //list($replacer) = preg_split("/:/",preg_replace("/}}/",":",$matches[1]));
                                return $matches[1]; 
                            else:
                                $replacer="";
                            endif;
                            
                            return $replacer;
                    },$array);  
        $array = preg_replace_callback($pattern_role,function ($matches) {
                        if(Yii::$app->user->id):
                            $role = Roles::find()->where(['id'=>Yii::$app->user->identity->role_id])->one();
                            if($role->name==$matches[1]):
                                return $matches[2];
                            else:
                                return "";
                            endif;
                        endif;
                    },$array);
                    
        $array = preg_replace_callback($pattern_setting,function ($matches) {
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
                    
        $array = preg_replace_callback($pattern_twig,function ($matches) {
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader); 
                            $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');
                            $metadata['param'] = Yii::$app->request->csrfParam;
                            $metadata['token'] = Yii::$app->request->csrfToken;                            
                            $content= $twig->render(Twig::find()->where(['renderer'=>$matches[1]])->one()->filename,['app'=>Yii::$app,'metadata'=>$metadata]);
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
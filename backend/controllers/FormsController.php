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

/**
 * Description of FormsController
 *
 * @author Peter
 */
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use backend\models\Forms;
use backend\models\Templates;
use backend\models\Themes;
use backend\models\Twig;
use backend\models\FormTwig;
use backend\models\FormSubmit;
use frontend\models\FormData;
use frontend\models\FormFiles;
use backend\models\Pages;
use backend\models\CustomWidget;
use backend\models\WebHookEmail;
use backend\models\WebHook;
use backend\models\Roles;
use backend\models\FormRoles;
use backend\models\CustomFormSettings;
use backend\models\ServicesOutgoing;
use frontend\components\ContentBuilder;
use backend\models\Relationships;
use backend\models\RelationshipDetails;
use backend\models\Media;
use backend\models\BackEndMenus;
use backend\models\BackEndMenuRole;
use backend\models\Settings;

class FormsController extends Controller{
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
        $page['notify_send_data']="";
        $page['response_send_data']="";
        $page['form_send_data']="";
        $page['published']="";
        $page_arr="";
        $page['roles']="";
        
        
        if($page['id']!=null):                
                $page['rs'] = Forms::find()->where(['id' => $page['id']])->one();
                if($page['rs']['published']=="Y"):
                    $page['published']=" checked";
                endif;
                $page['notify_rs'] = WebHookEmail::find()->where(['form_id'=>$page['id']])->andWhere('webhook_type="N"')->one();
                $page['response_rs'] = WebHookEmail::find()->where(['form_id'=>$page['id']])->andWhere('webhook_type="R"')->one();
                $page['form_rs'] = WebHookEmail::find()->where(['form_id'=>$page['id']])->andWhere('webhook_type="F"')->one();
                $page['win_rs'] = WebHook::find()->where(['form_id'=>$page['id']])->andWhere('hook_type="I"')->one();
                $page['wex_rs'] = WebHook::find()->where(['form_id'=>$page['id']])->andWhere('hook_type="E"')->one();
                if($page['notify_rs']['include_data']=="Y"):
                    $page['notify_send_data']=" checked";
                endif;
                if($page['response_rs']['include_data']=="Y"):
                    $page['response_send_data']=" checked";
                endif;
                if($page['form_rs']['include_data']=="Y"):
                    $page['form_send_data']=" checked";
                endif;
                $c = FormRoles::find()->where(['form_id'=>$page['id']])->all();
                $page_arr =  yii\helpers\ArrayHelper::getColumn($c, 'role_id');
            else:
                $page['rs'] = Forms::find()->where(['id' => "0"])->one();              
                
        endif;
        $roles=Roles::find()->orderBy(['name'=>SORT_ASC])->all();
        $role_map =  yii\helpers\ArrayHelper::map($roles, 'id', 'name');
        $client_profile= ServicesOutgoing::find()->orderBy(['name'=>SORT_ASC])->all();
        $page['client_profiles'] = ArrayHelper::map($client_profile, 'id', 'name');
        $page['roles'] = \yii\helpers\Html::checkboxList("roles",$page_arr,$role_map,['itemOptions' => ['class' => 'role_permit','labelOptions' => ['class' => 'role_permit_label']]]);
        $page['records'] = Forms::find()->orderBy('name')->all();
        $page['forms'] = ArrayHelper::map($page['records'], 'id', 'title');
        $widget = CustomWidget::find()->orderBy('name')->all();
        $page['widgets'] = ArrayHelper::map($widget, 'id', 'title');
        return $this->render('index',$page);
    }
    
    public function actionSave(){            
            if(Yii::$app->request->post("published")=="on"):
                $published="Y";
            else:
                $published="N";
            endif;
            $model = Forms::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->setAttribute('form_type',Yii::$app->request->post('form_type'));
                $model->setAttribute('title',Yii::$app->request->post('title'));
                $model->setAttribute('name',Yii::$app->request->post('name'));
                $model->setAttribute('form_fill_entry_type',Yii::$app->request->post('form_fill_entry_type'));
                $model->setAttribute('form_fill_limit',Yii::$app->request->post('form_fill_limit'));
                $model->setAttribute('published',$published);
                $model->setAttribute('show_in_menu',Yii::$app->request->post('show_in_menu'));
                $model->update(false);
                $roles = Yii::$app->request->post("roles");
                
                FormRoles::deleteAll(['AND','form_id ="'.Yii::$app->request->post("id").'"',['NOT IN','role_id',$roles]]); 
                
                $counter=0;
                foreach($roles as $selected):
                    $insert_id = md5(date("Hims").$counter);
                    $counter++;
                    $roles_model= new FormRoles();
                    $roles_model->setAttribute("id",$insert_id);
                    $roles_model->setAttribute("form_id",Yii::$app->request->post("id"));
                    $roles_model->setAttribute("role_id",$selected);
                    $roles_model->save();
                endforeach;
                return "Form successfully updated";
            else:
                $forms =  new Forms();
                $forms->attributes = Yii::$app->request->post();
                $id=md5(date("YmdHis"));
                $forms->setAttribute('id',$id);
                $forms->setAttribute('form_type',Yii::$app->request->post('form_type'));
                $forms->setAttribute('title',Yii::$app->request->post('title'));
                $forms->setAttribute('name',Yii::$app->request->post('name'));
                $forms->setAttribute('form_fill_entry_type',Yii::$app->request->post('form_fill_entry_type'));
                $forms->setAttribute('form_fill_limit',Yii::$app->request->post('form_fill_limit'));
                $forms->setAttribute('published',$published);
                $forms->setAttribute('show_in_menu',Yii::$app->request->post('show_in_menu'));
                $forms->save();
                $roles = Yii::$app->request->post("roles");
                $counter=0;
                foreach($roles as $selected):
                    $insert_id = md5(date("Hims").$counter);
                    $counter++;
                    $roles_model= new FormRoles();
                    $roles_model->setAttribute("id",$insert_id);
                    $roles_model->setAttribute("form_id",$forms->id);
                    $roles_model->setAttribute("role_id",$selected);
                    $roles_model->save();
                endforeach;
                return "New form created";
            endif;
    }
    public function actionSaveWidget(){
            $permissions = Yii::$app->request->post("permissions");
            $perm_val="";
            if(!empty($permissions)){
               $counter=0;
                // Loop to store and display values of individual checked checkbox.
                foreach($permissions as $selected){                    
                    $perm_val = $perm_val." ".$selected;       
                }
            }
            $model = CustomWidget::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->attributes = Yii::$app->request->post();
                $model->setAttribute('title',Yii::$app->request->post('title'));
                $model->setAttribute('name',Yii::$app->request->post('name'));
                $model->setAttribute('form_id',Yii::$app->request->post('form_id'));
                $model->setAttribute('permissions',$perm_val);
                $model->setAttribute('require_login',Yii::$app->request->post("require_login"));
                $model->save();
                return "Form successfully updated";
            else:
                $forms =  new CustomWidget();
                $forms->attributes = Yii::$app->request->post();
                $id=md5(date("YmdHis"));
                $forms->setAttribute('id',$id);
                $forms->setAttribute('form_id',Yii::$app->request->post('form_id'));
                $forms->setAttribute('title',Yii::$app->request->post('title'));
                $forms->setAttribute('name',Yii::$app->request->post('name'));
                $forms->setAttribute('permissions',$perm_val);
                $forms->setAttribute('require_login',Yii::$app->request->post("require_login"));
                $forms->save();
                return "New widget created";
            endif;
    }
    public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Forms::findOne($id);
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
        
        $page['records'] = Forms::find()->orderBy('name')->all();
        
        return $this->render('extensions',$page);
    }
    public function actionViews(){
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
        $form_view_route = Templates::find()->where(['route'=>'forms/view'])->one();
        $page['records'] = Pages::find()->where(['template'=>$form_view_route['id']])->orderBy('title')->all();
        
        return $this->render('views',$page);
    }
    public function actionFdetails(){
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
        $form_view_route = Templates::find()->where(['route'=>'forms/view'])->one();
        $page['records'] = Pages::find()->where(['template'=>$form_view_route['id']])->orderBy('title')->all();
        
        return $this->render('frontend-details',$page);
    }
    public function actionFwidgets(){
        $page=[];
        $page_arr="";
        $perm_arr="";
        $template_list = Themes::find()->orderBy('name')->all();
        $tag_map =  yii\helpers\ArrayHelper::map($template_list, 'id', 'name'); 
        $page['t'] = Yii::$app->request->get('t',null);
        if(Yii::$app->request->get("reload")=="true"):
            $page['theme'] = \yii\helpers\Html::dropDownList("theme", Yii::$app->request->get("theme"),$tag_map,['prompt'=>'Select a theme','id'=>'theme']);
            $page['selected_theme'] = Yii::$app->request->get("theme"); 
        else:
            $page['theme'] = \yii\helpers\Html::dropDownList("theme", \frontend\components\ContentBuilder::getSetting("current_theme"),$tag_map,['prompt'=>'Select a theme','id'=>'theme']);
            $page['selected_theme'] = \frontend\components\ContentBuilder::getSetting("current_theme");
        endif;
        $form_view_route = Templates::find()->where(['route'=>'forms/view'])->one();
        $page['records'] = CustomWidget::find()->orderBy('title')->all();
        $page['id']= Yii::$app->request->get('id',null);
        
        if($page['id']!=null):
                $page['rs'] = CustomWidget::find()->where(['id' => $page['id']])->one();
                $perm_arr = explode(" ",$page['rs']['permissions']);
        else:
                $page['rs'] = CustomWidget::find()->where(['id' => "0"])->one();
        endif;
        $form_map =  yii\helpers\ArrayHelper::map(Forms::find()->all(), 'id', 'title');
        $page['forms'] = \yii\helpers\Html::dropDownList("form_id",$page['rs']['form_id'],$form_map,['prompt'=>'N/A','class'=>'form-control']);
        $page['widget_list']=CustomWidget::find()->all();
        
        $pages = Roles::find()->orderBy('name')->all();
        $page_map =  yii\helpers\ArrayHelper::map($pages, 'id', 'name');
        $page['permissions'] = \yii\helpers\Html::checkboxList("permissions",$perm_arr,$page_map);
        return $this->render('frontend-widgets',$page);
    }
    public function actionFetchTwigTheme(){
        $theme_id = Yii::$app->request->get('theme_id');
        $renderer = Yii::$app->request->get('renderer');
        $record = Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'F'])->one();
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
        
        $record = Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'F'])->one();
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
                $twig->setAttribute("renderer_type",'F');
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
    public function actionFetchViewTwigTheme(){
        $theme_id = Yii::$app->request->get('theme_id');
        $renderer = Yii::$app->request->get('renderer');
        $record = Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'R'])->one();
        if($record!=null):
            return $record['code'];
        else:
            return "";
        endif;
        
    }
    public function actionSaveViewTwigTheme(){
        $theme_id = Yii::$app->request->post('theme');
        $renderer = Yii::$app->request->post('renderer');
        $code = Yii::$app->request->post('code');
        
        $record = Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'R'])->one();
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
                $twig->setAttribute("renderer_type",'R');
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
    public function actionFetchDetailedTwigTheme(){
        $theme_id = Yii::$app->request->get('theme_id');
        $renderer = Yii::$app->request->get('renderer');
        $record = Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'D'])->one();
        if($record!=null):
            return $record['code'];
        else:
            return "";
        endif;
        
    }
    public function actionSaveDetailedTwigTheme(){
        $theme_id = Yii::$app->request->post('theme');
        $renderer = Yii::$app->request->post('renderer');
        $code = Yii::$app->request->post('code');
        
        
        $record = Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'D'])->one();
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
                $record->setAttribute("renderer_type",'D');
                $record->save();
                return "Twig template updated";
        else:
                $twig =  new Twig();
                $twig->setAttribute("theme_id",$theme_id);
                $twig->setAttribute("renderer",$renderer);
                $twig->setAttribute("renderer_type",'D');
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
    
    //The actionFechtWidgetTheme is used to get the Widgets for a previously submitted form
    public function actionFetchWidgetTwigTheme(){
        $theme_id = Yii::$app->request->get('theme_id');
        $renderer = Yii::$app->request->get('renderer');
        $record = Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'I'])->one();
        if($record!=null):
            return $record['code'];
        else:
            return "";
        endif;
        
    }
    public function actionSaveWidgetTwigTheme(){
        $theme_id = Yii::$app->request->post('theme');
        $renderer = Yii::$app->request->post('renderer');
        $code = Yii::$app->request->post('code');
        
        $record = Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'I'])->one();
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
                $twig->setAttribute("renderer_type",'I');
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
    public function actionData(){
        $page=[];
        if(Yii::$app->request->get('owner')!==null):
            $subquery = FormData::find()->select('form_submit_id');
            $source = Relationships::find()->where(['source_id'=>Yii::$app->request->get('source'),'target_id'=>Yii::$app->request->get('target')])->one();
            if($source==null):
                $source = Relationships::find()->where(['source_id'=>Yii::$app->request->get('target'),'target_id'=>Yii::$app->request->get('source')])->one();               
                $relation_obj = RelationshipDetails::find()->where(['relationship_id'=>$source['id']])->all();
                $form_submit_param = FormData::find()->where(['form_submit_id'=>Yii::$app->request->get('owner')])->all();
                $rel_arr =[];
                $rel_a=[];
                foreach($relation_obj as $param): 
                    $a = FormData::find()->select('param_val')->where(['form_submit_id'=>Yii::$app->request->get('owner'),'param'=>$param['target_field']])->asArray()->column();                                
                    $rel_a = array_merge($rel_a,$a);
                endforeach;
                $b = FormData::find()->select('form_submit_id')->where(['IN','param_val',$rel_a])->andWhere('param="'.$param['source_field'].'"')->asArray()->column();
                $rel_arr = array_merge($rel_arr,$b);
            else:
                $relation_obj = RelationshipDetails::find()->where(['relationship_id'=>$source['id']])->all();
                $form_submit_param = FormData::find()->where(['form_submit_id'=>Yii::$app->request->get('owner')])->all();
                $rel_arr =[];
                $rel_a=[];
                foreach($relation_obj as $param): 
                    $a = FormData::find()->select('param_val')->where(['form_submit_id'=>Yii::$app->request->get('owner'),'param'=>$param['source_field']])->asArray()->column();                                
                    $rel_a = array_merge($rel_a,$a);
                endforeach;
                $b = FormData::find()->select('form_submit_id')->where(['IN','param_val',$rel_a])->andWhere('param="'.$param['target_field'].'"')->asArray()->column();
                $rel_arr = array_merge($rel_arr,$b);
            endif;
            $target_query = $subquery->column();
            $page['records'] = FormSubmit::find()->where(['IN','id',$rel_arr])->andWhere('form_id="'.Yii::$app->request->get('id').'"')->all();
            
        else:
            $page['records'] = $page['records'] = FormSubmit::find()->where(['form_id'=>Yii::$app->request->get('id')])->all();
        endif;
        $page['header'] = CustomFormSettings::find()->where(['form_id'=>Yii::$app->request->get('id')])->orderBy('field_name')->all();
        $page['related'] = Forms::find()->where(['id'=>Yii::$app->request->get('id')])->one();
        return $this->render('data',$page);
    }
    public function actionDetails(){
        $page=[];
        $page['info'] = FormSubmit::find()->where(['id'=>Yii::$app->request->get('id')])->one();
        $page['records'] = FormData::find()->where(['form_submit_id'=>Yii::$app->request->get('id')])->all();
        return $this->render('details',$page);
        
    }
    public function actionDeleteFormSubmit(){
        $id = str_replace("}","",Yii::$app->request->get("id"));
        FormData::deleteAll(['form_submit_id'=>$id]);
        FormFiles::deleteAll(['form_submit_id'=>$id]);
        $a = FormSubmit::find()->where(['id'=>$id])->one();
        $a->delete();        
        return "Record Deleted";
    }
    public function actionEditDetails(){
        $page=[];
        
        $page['info'] = FormSubmit::find()->where(['id'=>Yii::$app->request->get('id')])->one();
        $page['records'] = FormData::find()->where(['form_submit_id'=>Yii::$app->request->get('id')])->all();
        $page['files'] = FormFiles::find()->where(['form_submit_id'=>Yii::$app->request->get('id')])->all();
        //we need to extract the yumpee_backend part of this form that will be used to update the data on this page
        $twig_content= Twig::find()->where(['renderer'=>$page['info']['form_id']])->andWhere('renderer_type="F"')->andWhere('theme_id="'.ContentBuilder::getSetting("current_theme").'"')->one();
        $pattern_backend="/{yumpee_backend_view}(.*?){\/yumpee_backend_view}/s";
        preg_match($pattern_backend, $twig_content['code'], $matches);
        if(count($matches) > 1):
            $content = $matches[1];   
        else:
            $content="";
        endif;
        return $this->render('edit-details',['info'=>$page['info'],'records'=>$page['records'],'files'=>$page['files'],'backend_data'=>$content]);
    }
    public function actionUpdateFormSubmit(){
        $a = FormSubmit::find()->where(['id'=>Yii::$app->request->post('id')])->one();
        $a->setAttribute('published',Yii::$app->request->post('published'));
        $a->setAttribute('rating',Yii::$app->request->post('rating'));
        $a->update(false);
        return "Form information saved";
    }
    public function actionUpdateFormData(){        
                    foreach($_POST as $key => $value)
                        {
                                if($value<>""):
                                    $a = FormData::find()->where(['form_submit_id'=>Yii::$app->request->post('id')])->andWhere('param="'.$key.'"')->one();
                                    if($a==null):
                                    $form_data = new FormData();
                                    $form_data->setAttribute("form_submit_id",Yii::$app->request->post('id'));
                                    $form_data->setAttribute("param",$key);
                                    $form_data->setAttribute("param_val",$value);
                                    $form_data->save();
                                    else:
                                        $a->setAttribute("param_val",$value);
                                        $a->update(false);
                                    endif;
                                endif;
                        }
                $img_array = Yii::$app->request->post("document_listing");            
                $ev_arr = explode(" ",$img_array);
                for($i=0; $i < count($ev_arr);$i++): 
                    if(trim($ev_arr[$i])!=""):
                            $media = Media::find()->where(['path'=>$ev_arr[$i]])->one();
                            $model = new FormFiles();
                            $random=rand(1000,10000);                            
                            $model->setAttribute("form_submit_id",Yii::$app->request->post("id"));                            
                            $model->setAttribute("file_name",$media['name']);
                            $model->setAttribute("file_path",$media['path']);
                            //$model->setAttribute("file_type",mime_content_type(Yii::getAlias('@image_dir/').$media['path']));
                            $model->setAttribute("file_size",$media['size']);
                            $model->save();                            
                    endif;
                endfor;
    }
    public function actionConfigure(){
        $page=[];
        $page['id']= Yii::$app->request->get('id',null);
        if($page['id']!=null):    
                $page['rs'] = CustomFormSettings::find()->where(['id'=>$page['id']])->one();
            else:
                $page['rs'] = new CustomFormSettings();
        endif;
        $page['form_id']= Yii::$app->request->get('form_id',null);
        $page['records'] = CustomFormSettings::find()->where(['form_id'=>$page['form_id']])->all();
        $widget = CustomWidget::find()->all();
        $page['custom_widget'] =  yii\helpers\ArrayHelper::map($widget, 'name', 'name');
        
        return $this->render("configure",$page);
    }
    public function actionSaveConfigure(){
        $model = CustomFormSettings::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->setAttribute("form_id",Yii::$app->request->post("form_id"));
                $model->setAttribute("field_name",Yii::$app->request->post("field_name"));
                $model->setAttribute("view_label",Yii::$app->request->post("view_label"));
                $model->setAttribute("view_order",Yii::$app->request->post("view_order"));
                $model->setAttribute("class_related",Yii::$app->request->post("class_related"));
                $model->setAttribute("property_related",Yii::$app->request->post("property_related"));
                $model->setAttribute("return_alias",Yii::$app->request->post("return_alias"));
                $model->setAttribute("return_widget",Yii::$app->request->post("return_widget"));
                $model->setAttribute("return_eval",Yii::$app->request->post("return_eval"));
                $model->save();
                return "Custom form successfully updated";
            else:
                $model =  new CustomFormSettings();
                $id = md5(date("YmdHis"));
                $model->setAttribute("id",$id);
                $model->setAttribute("form_id",Yii::$app->request->post("form_id"));
                $model->setAttribute("field_name",Yii::$app->request->post("field_name"));
                $model->setAttribute("view_label",Yii::$app->request->post("view_label"));
                $model->setAttribute("view_order",Yii::$app->request->post("view_order"));
                $model->setAttribute("class_related",Yii::$app->request->post("class_related"));
                $model->setAttribute("property_related",Yii::$app->request->post("property_related"));
                $model->setAttribute("return_alias",Yii::$app->request->post("return_alias"));
                $model->setAttribute("return_widget",Yii::$app->request->post("return_widget"));
                $model->setAttribute("return_eval",Yii::$app->request->post("return_eval"));
                $model->save();
                return "New Setting added";
            endif;
        
    }
    public function actionDeleteConfigure(){
        $id = str_replace("}","",Yii::$app->request->get("id"));
        CustomFormSettings::deleteAll(['id'=>$id]);
        return "Custom Form Setting deleted";
    }
    public function actionPost(){
        //we handle the loading of twig template if it is turned on
                        $content="";
                        $theme_id = \frontend\components\ContentBuilder::getSetting("current_theme");
                        $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');
                        $metadata['form_id'] = Yii::$app->request->get("form_id"); 
                        $metadata['param'] = Yii::$app->request->csrfParam;
                        $metadata['token'] = Yii::$app->request->csrfToken;
                        $form = Forms::find()->where(['id'=>Yii::$app->request->get("form_id")])->one();
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>Yii::$app->request->get("form_id"),'renderer_type'=>'F'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new FormTwig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'], ['form'=>$form,'metadata'=>$metadata,'app'=>Yii::$app]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                        return $this->render('@frontend/views/layouts/html',['data'=>$content]);
    }
    public function actionDeleteWidget(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = CustomWidget::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
    }
    
    public function actionDeleteAttachment(){
    FormFiles::deleteAll(['form_submit_id'=>Yii::$app->request->get('article_id'),'id'=>Yii::$app->request->get('id')]);
    echo "Record successfully deleted";
    }
    public function actionPermissions(){
        $page=[];
        $page_arr="";
        $page["can_create"]="";
        $update_roles="";
        $view_roles="";
        $delete_roles="";
        $page['selected_role'] = Yii::$app->request->get("role");
        $page['role_id'] = Yii::$app->request->get("role_id");
        $page['form_id'] = Yii::$app->request->get("form_id");
        $roles=Roles::find()->orderBy(['name'=>SORT_ASC])->all();
        $role_map =  yii\helpers\ArrayHelper::map($roles, 'id', 'name');
        $perm_obj = FormRoles::find()->where(['form_id'=>Yii::$app->request->get("form_id"),'role_id'=>Yii::$app->request->get("role_id")])->one();
        if($perm_obj!==null):
            $page_arr = unserialize($perm_obj->permissions);
            if(isset($page_arr['can_create_record'])):
                $page['can_create'] = $page_arr['can_create_record'];
            endif;
            if(isset($page_arr['view_roles'])):
                $view_roles = $page_arr['view_roles'];
            endif;
            if(isset($page_arr['delete_roles'])):
                $delete_roles = $page_arr['delete_roles'];
            endif;
            if(isset($page_arr['update_roles'])):
                $update_roles = $page_arr['update_roles'];
            endif;
        endif;
        $page['update_roles'] = \yii\helpers\Html::checkboxList("update_roles",$update_roles,$role_map,['itemOptions' => ['class' => 'role_permit','labelOptions' => ['class' => 'role_permit_label']]]);
        $page['view_roles'] = \yii\helpers\Html::checkboxList("view_roles",$view_roles,$role_map,['itemOptions' => ['class' => 'role_permit','labelOptions' => ['class' => 'role_permit_label']]]);
        $page['delete_roles'] = \yii\helpers\Html::checkboxList("delete_roles",$delete_roles,$role_map,['itemOptions' => ['class' => 'role_permit','labelOptions' => ['class' => 'role_permit_label']]]);
        return $this->renderPartial('permissions',$page);
    }
    public function actionAddPermissions(){
        $frm_role = FormRoles::find()->where(['form_id'=>Yii::$app->request->post("permission_form_id"),'role_id'=>Yii::$app->request->post("permission_role_id")])->one();
        if($frm_role!==null):
            $encode = print_r(Yii::$app->request->post());
            $frm_role->setAttribute("permissions",serialize(Yii::$app->request->post()));
            $frm_role->update(false);
        endif;
        return "Permission successfully added";
    }
}

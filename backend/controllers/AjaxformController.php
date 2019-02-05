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
 * Description of AjaxformController
 *
 * @author Peter
 */
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use frontend\components\ContentBuilder;
use backend\models\Pages;
use backend\models\Forms;
use backend\models\ArticlesCategories;
use backend\models\Media;
use backend\models\CustomWidget;
use backend\models\CustomSettings;
use common\components\ResizeImage;
use yii\web\Response;
use frontend\models\Articles;
use frontend\models\ArticleFiles;
use frontend\models\Twig;
use frontend\models\Users;
use frontend\models\FormSubmit;
use frontend\models\FormData;
use frontend\models\FormFiles;
use frontend\models\Feedback;
use frontend\models\FeedbackDetails;
use frontend\models\ProfileDetails;
use frontend\models\UserProfileFiles;
use frontend\models\WebHook;

use yii\helpers\FileHelper;
use yii\db\Expression;

class AjaxformController extends Controller{
    //put your code here
    public function actionSave(){
        
        if (Yii::$app->request->isAjax && Yii::$app->request->post()) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                if(Yii::$app->request->post("form_type")=="form-article"):
                        if(Yii::$app->user->isGuest):
                                $record=null;
                                $usrname="";
                            else:
                                $usrname = Yii::$app->user->identity->username;
                                $record_limit_arr = Forms::find()->where(['id'=>Yii::$app->request->post("form_id")])->one();
                                if($record_limit_arr->form_fill_entry_type=='S' && $record==null):
                                    if ((Articles::find()->where(['usrname'=>Yii::$app->user->identity->username])->count() + 1) > 1):
                                        return "A previous article entry has been made. Consider updating the previous entry made";
                                    endif;
                                endif;
                                if($record_limit_arr->form_fill_limit > 0):
                                    if((Articles::find()->where(['usrname'=>Yii::$app->user->identity->username])->count() + 1) > $record_limit_arr->form_fill_limit):
                                        return "Data cannot be saved. Article submission limit exceeded";
                                    endif;
                                endif;
                        endif;
                    $id = Articles::saveArticle(); //we get the ID of the article saved
                    //we now deal with files if they have been uploaded with this
                    if( strtolower( $_SERVER[ 'REQUEST_METHOD' ] ) == 'post' && !empty( $_FILES ) && !Yii::$app->user->isGuest && $id > 0) {
                                $random = rand(1000,100000);
                                $session = md5(date('YmdHis')).$random;
                                $directory = Yii::getAlias('@uploads/uploads/') .$session;
                                if (!is_dir($directory)) {
                                                FileHelper::createDirectory($directory);
                                }
                                foreach ($_FILES as $k=>$v){
                                    if(is_array($v)){
                                        //for single files
                                        if(is_array($v)){
                                            if(!is_array($v['tmp_name']) && !empty($v['tmp_name'])){	
                                                $uid = uniqid(time(), true);   
                                                $uid= str_replace(".","-",$uid);
                                                $fileName = $uid . '_' . str_replace(" ","_",$v['name']);
                                                $filePath = $directory;
                                                if (strpos($k, 'yumpee-image') !== false) {
                                                    list($label,$width,$height) = explode("_",$k);
                                                    $resize = new ResizeImage($v['tmp_name']);
                                                    $resize->resizeTo($width, $height, 'exact');
                                                    $resize->saveImage($filePath."/".$fileName);
                                                }else{
                                                    move_uploaded_file( $v['tmp_name'], $filePath."/".$fileName); // move to new location perhaps?
                                                }
                                                $frmFiles = new ArticleFiles();
                                                $frmFiles->setAttribute("article_id",Yii::$app->user->identity->id);
                                                $frmFiles->setAttribute("file_name",$v['name']);
                                                $frmFiles->setAttribute("file_path",$session ."/".$fileName);
                                                $frmFiles->setAttribute("file_type",$v['type']);
                                                if (file_exists($filePath."/".$fileName)):
                                                    $frmFiles->setAttribute("file_size",filesize($filePath."/".$fileName));
                                                    $frmFiles->save();
                                                endif;
                                            }
				
                                        }
                                        $counter=0;
                                        foreach ($v as $sk=>$sv){ 
                                            $arr[$sk][$k]=$sv;
                                            if(is_array($sv) && !empty($v['tmp_name'][$counter])){
                                                    //echo $k." - ". $v['name'][$counter]." - ".$v['tmp_name'][$counter]."-".$v['type'][$counter]."<br>"	;
                                                    //move_uploaded_file( $v['tmp_name'][$counter], $filePath."/".$v['name'][$counter]);
                                                    $uid = uniqid(time(), true);   
                                                    $uid= str_replace(".","-",$uid);
                                                    $fileName = $uid . '_' . str_replace(" ","_",$v['name'][$counter]);
                                                    $filePath = $directory;
                                                    if (strpos($k, 'yumpee-image') !== false) {
                                                        list($label,$width,$height) = explode("_",$k);
                                                        $resize = new ResizeImage($v['tmp_name'][$counter]);
                                                        $resize->resizeTo($width, $height, 'exact');
                                                        $resize->saveImage($filePath."/".$fileName);
                                                    }else{
                                                        move_uploaded_file( $v['tmp_name'][$counter], $filePath."/".$fileName); // move to new location perhaps?
                                                    }
                                                    $frmFiles = new ArticleFiles();
                                                    $frmFiles->setAttribute("article_id",Yii::$app->user->identity->id);
                                                    $frmFiles->setAttribute("file_name",$v['name'][$counter]);
                                                    $frmFiles->setAttribute("file_path",$session ."/".$fileName);
                                                    $frmFiles->setAttribute("file_type",$v['type'][$counter]);
                                                    if (file_exists($filePath."/".$fileName)):
                                                        $frmFiles->setAttribute("file_size",filesize($filePath."/".$fileName));
                                                        $frmFiles->save();
                                                    endif;
                                            }
                                            $counter++;
				
                                        }
                                    }
                                }
                                
                            }
                endif;
                if(Yii::$app->request->post("form_type")=="form-profile"):
                    $model = Users::find()->where(['id'=>Yii::$app->user->identity->id])->one();
                    if(Yii::$app->request->post("first_name")!=null):
                        $model->setAttribute("first_name",Yii::$app->request->post("first_name"));
                    endif;
                    if(Yii::$app->request->post("last_name")!=null):
                        $model->setAttribute("last_name",Yii::$app->request->post("last_name"));
                    endif;
                    if(Yii::$app->request->post("email")!=null):
                        $model->setAttribute("email",Yii::$app->request->post("email"));
                    endif;
                    if(Yii::$app->request->post("about")!=null):
                        $model->setAttribute("about",Yii::$app->request->post("about"));
                    endif;
                    if(Yii::$app->request->post("passwd")!=null):
                        if($model['password_hash']<>Yii::$app->request->post('passwd')):
                            $model->setAttribute("password_hash",Yii::$app->security->generatePasswordHash(Yii::$app->request->post('passwd')));
                        endif;
                    endif;
                        $model->setAttribute("updated_at",time());
                        //what if there is an upload of a profile image - check for display_image field
                        if(!empty($_FILES[ 'display_image' ][ 'tmp_name' ])):
                                //lets deal with uploaded files here
                                if(Yii::$app->session->id==null):
                                    $session=md5(date("YmdHis").rand(1000,100000));
                                else:
                                    $session=Yii::$app->session->id;
                                endif;
                                $directory = Yii::getAlias('@uploads/uploads/') .$session;
                                if (!is_dir($directory)) {
                                                FileHelper::createDirectory($directory);
                                }
                                $uid = uniqid(time(), true);   
                                $uid= str_replace(".","-",$uid);
                                $fileName = $uid . '_' . str_replace(" ","_",$_FILES['display_image']['name']);
                                $filePath = $directory;
                                move_uploaded_file( $_FILES['display_image']['tmp_name'], $filePath."/".$fileName); // move to new location perhaps?
                                $random = rand(1,10000);
                                $media = new Media();
                                $image_id = md5(date('YmdHis')).$random;
                                $media->setAttribute('id',$image_id);
                                $media->setAttribute('upload_date',date('Y-m-d'));
                                $media->setAttribute('author',Yii::$app->user->identity->id);
                                $media->setAttribute('media_type','1');
                                $media->setAttribute('size',$_FILES['display_image']['size']);
                                $media->setAttribute('path',Yii::$app->session->id ."/".$fileName);
                                $media->setAttribute('name',$_FILES['display_image']['name']);
                                $media->setAttribute('alt_tag',$_FILES['display_image']['name']);
                                $media->save();
                                $model->setAttribute('display_image_id',$image_id);
                        endif;
                    $model->save();  
                    
                    //if there are more fields in this form, we should extend the information and store in the data model
                        $a = ProfileDetails::deleteAll(['profile_id'=>Yii::$app->user->identity->id]);
                       
                        foreach($_POST as $key => $value)
                        {
                                if($value<>""):
                                    $profile_data = new ProfileDetails();
                                    $profile_data->setAttribute("profile_id",Yii::$app->user->identity->id);
                                    $profile_data->setAttribute("param",$key);
                                    $profile_data->setAttribute("param_val",$value);
                                    $profile_data->save();
                                endif;
                        }
                    //we handle form uploads here
                        if( strtolower( $_SERVER[ 'REQUEST_METHOD' ] ) == 'post' && !empty( $_FILES ) ) {
                                $random = rand(1,10000);
                                $session = md5(date('YmdHis')).$random;
                                $directory = Yii::getAlias('@uploads/uploads/') .$session;
                                if (!is_dir($directory)) {
                                                FileHelper::createDirectory($directory);
                                }
                                foreach ($_FILES as $k=>$v){
                                    if(is_array($v)){
                                        //for single files
                                        if(is_array($v)){
                                            if(!is_array($v['tmp_name']) && !empty($v['tmp_name'])){					
                                                //echo $k." - ". $v['name']." - " .$v['tmp_name']." ".$v['size']."<br>";
                                                //move_uploaded_file( $v['tmp_name'], $filePath."/".$v['name']);
                                                $uid = uniqid(time(), true);   
                                                $uid= str_replace(".","-",$uid);
                                                $fileName = $uid . '_' . str_replace(" ","_",$v['name']);
                                                $filePath = $directory;
                                                if (strpos($k, 'yumpee-image') !== false) {
                                                    list($label,$width,$height) = explode("_",$k);
                                                    $resize = new ResizeImage($v['tmp_name']);
                                                    $resize->resizeTo($width, $height, 'exact');
                                                    $resize->saveImage($filePath."/".$fileName);
                                                }else{
                                                    move_uploaded_file( $v['tmp_name'], $filePath."/".$fileName); // move to new location perhaps?
                                                }
                                                $frmFiles = new UserProfileFiles();
                                                $frmFiles->setAttribute("profile_id",Yii::$app->user->identity->id);
                                                $frmFiles->setAttribute("file_name",$v['name']);
                                                $frmFiles->setAttribute("file_path",$session ."/".$fileName);
                                                $frmFiles->setAttribute("file_type",$v['type']);
                                                if (file_exists($filePath."/".$fileName)):
                                                    $frmFiles->setAttribute("file_size",filesize($filePath."/".$fileName));
                                                    $frmFiles->save();
                                                endif;
                                            }
				
                                        }
                                        $counter=0;
                                        foreach ($v as $sk=>$sv){ 
                                            $arr[$sk][$k]=$sv;
                                            if(is_array($sv) && !empty($v['tmp_name'][$counter])){
                                                    //echo $k." - ". $v['name'][$counter]." - ".$v['tmp_name'][$counter]."-".$v['type'][$counter]."<br>"	;
                                                    //move_uploaded_file( $v['tmp_name'][$counter], $filePath."/".$v['name'][$counter]);
                                                    $uid = uniqid(time(), true);   
                                                    $uid= str_replace(".","-",$uid);
                                                    $fileName = $uid . '_' . str_replace(" ","_",$v['name'][$counter]);
                                                    $filePath = $directory;
                                                    if (strpos($k, 'yumpee-image') !== false) {
                                                        list($label,$width,$height) = explode("_",$k);
                                                        $resize = new ResizeImage($v['tmp_name'][$counter]);
                                                        $resize->resizeTo($width, $height, 'exact');
                                                        $resize->saveImage($filePath."/".$fileName);
                                                    }else{
                                                        move_uploaded_file( $v['tmp_name'][$counter], $filePath."/".$fileName); // move to new location perhaps?
                                                    }
                                                    $frmFiles = new UserProfileFiles();
                                                    $frmFiles->setAttribute("profile_id",Yii::$app->user->identity->id);
                                                    $frmFiles->setAttribute("file_name",$v['name'][$counter]);
                                                    $frmFiles->setAttribute("file_path",$session ."/".$fileName);
                                                    $frmFiles->setAttribute("file_type",$v['type'][$counter]);
                                                    if (file_exists($filePath."/".$fileName)):
                                                        $frmFiles->setAttribute("file_size",filesize($filePath."/".$fileName));
                                                        $frmFiles->save();
                                                    endif;
                                            }
                                            $counter++;
				
                                        }
                                    }
                                }
                                
                            }
                    
                    
                    return "Profile Saved";
                endif;
                if(Yii::$app->request->post("form_type")=="form-feedback"):
                    //we need to check if it has an associated article to it
                            $feedback = new Feedback();
                            $id=md5(date('YmHis').rand(1000,100000));
                            $feedback->setAttribute("id",$id);
                            $feedback->setAttribute("feedback_type",Yii::$app->request->post("feedback_type"));
                            $feedback->setAttribute("target_id",Yii::$app->request->post("target_id"));
                            $feedback->setAttribute("date_submitted",date("Y-m-d H:i:s"));
                            $feedback->setAttribute("form_id",Yii::$app->request->post("form_id"));
                            $feedback->setAttribute("ip_address",Yii::$app->getRequest()->getUserIP());
                            if(Yii::$app->user->identity!=null):
                                $usrname= Yii::$app->user->identity->username;
                            else:
                                $usrname="";
                            endif;
                            $feedback->setAttribute("usrname",$usrname);
                            $feedback->save();
                            $a = FeedbackDetails::deleteAll(['feedback_id'=>$id]);
                       
                        foreach($_POST as $key => $value)
                        {
                                if($value<>""):
                                    $feedback_data = new FeedbackDetails();
                                    $feedback_data->setAttribute("feedback_id",$id);
                                    $feedback_data->setAttribute("param",$key);
                                    $feedback_data->setAttribute("param_val",$value);
                                    $feedback_data->save();
                                endif;
                        }
                    return "Feedback Saved";
                endif;
                if(Yii::$app->request->post("form_type")=="form-settings"):
                    CustomSettings::deleteAll(['theme_id'=>Yii::$app->request->get("id",Yii::$app->request->post("form_id"))]);
                
                    foreach($_POST as $key => $value)
                        {
                                if($value<>""):
                                    $id=md5(date('YmHis').rand(1000,100000));
                                    $settings_data = new CustomSettings();
                                    $settings_data->setAttribute("id",$id);
                                    $settings_data->setAttribute("theme_id",Yii::$app->request->get("id",Yii::$app->request->post("form_id")));
                                    $settings_data->setAttribute("setting_name",$key);
                                    $settings_data->setAttribute("setting_value",$value);
                                    $settings_data->save();
                                endif;
                        }
                    return "Settings Saved";                    
                endif;
                
                if(Yii::$app->request->post("form_type")=="form-twig"):
                        //we add the new
                    $usrname="";
                        if(Yii::$app->user->isGuest):
                                $record=null;
                                $usrname="";
                            else:
                                //we need to check here what the form submission limit is for this form entry
                                $record = FormSubmit::find()->where(['id'=>Yii::$app->request->post("id")])->andWhere('usrname="'.Yii::$app->user->identity->username.'"')->one();
                                $usrname = Yii::$app->user->identity->username;
                                $record_limit_arr = Forms::find()->where(['id'=>Yii::$app->request->post("form_id")])->one();
                                if($record_limit_arr->form_fill_entry_type=='S' && $record==null):
                                    if ((FormSubmit::find()->where(['usrname'=>Yii::$app->user->identity->username])->andWhere('form_id="'.$record_limit_arr->id.'"')->count() + 1) > 1):
                                        return "A previous entry has been made. Consider updating the previous entry made";
                                    endif;
                                endif;
                                if($record_limit_arr->form_fill_limit > 0):
                                    if((FormSubmit::find()->where(['usrname'=>Yii::$app->user->identity->username])->andWhere('form_id="'.$record_limit_arr->id.'"')->count() + 1) > $record_limit_arr->form_fill_limit):
                                        return "Data cannot be saved. Form submission limit exceeded";
                                    endif;
                                endif;
                        endif;
                        
                        $form = Forms::find()->where(['id'=>Yii::$app->request->post("form_id")])->one();
                        if($record==null):
                            $form_submit = new FormSubmit();
                            if($form->published=="Y"):
                                $form_submit->setAttribute('published',"1");
                            else :
                                $form_submit->setAttribute('published',"0");
                            endif;
                            $form_submit->setAttribute("form_id",Yii::$app->request->post("form_id"));
                            $form_submit->setAttribute("usrname",$usrname);
                            $form_submit->setAttribute("token",Yii::$app->request->post("_csrf-backend"));
                            $form_submit->setAttribute("date_stamp",date("Y-m-d H:i:s"));
                            $form_submit->setAttribute("ip_address",Yii::$app->getRequest()->getUserIP());
                            $form_submit->setAttribute("url",$usrname.md5(Yii::$app->getRequest()->getUserIP().date('YmdHiis')));
                            if(Yii::$app->request->post("yumpee_ignore_save")=="true"):
                                //lets ignore the save
                                $id="0";
                            else:
                                $form_submit->save();
                                $id = $form_submit->id;
                            endif;
                            
                        else:
                            if($form->published=="Y"):
                                $record->setAttribute('published',"1");
                            else:
                                $record->setAttribute('published',"0");
                            endif;
                            $record->setAttribute("form_id",Yii::$app->request->post("form_id"));
                            $record->setAttribute("usrname",$usrname);
                            $record->setAttribute("token",Yii::$app->request->post("_csrf-backend"));
                            $record->setAttribute("date_stamp",date("Y-m-d H:i:s"));
                            $record->setAttribute("ip_address",Yii::$app->getRequest()->getUserIP());
                            
                            if(Yii::$app->request->post("yumpee_ignore_save")=="true"):
                                            //do not save the data
                                        else:
                                            $record->save();
                            endif;
                            if(Yii::$app->request->post("id")):
                                $id = Yii::$app->request->post("id");
                            endif;
                        endif;
                        $x="";
                        //delete where form id is 
                        $a = FormData::deleteAll(['form_submit_id'=>$id]);
                       
                        foreach($_POST as $key => $value)
                        {
                                if($value<>""):
                                    $form_data = new FormData();
                                    $form_data->setAttribute("form_submit_id",$id);
                                    $form_data->setAttribute("param",$key);
                                    $form_data->setAttribute("param_val",$value);
                                    if(Yii::$app->request->post("yumpee_ignore_save")=="true"):
                                            //do not save the data
                                        else:
                                            $form_data->save();
                                    endif;
                                        
                                endif;
                        }
                        //lets deal with uploaded files here
                        if(Yii::$app->session->id==null):
                            $session=md5(date("YmdHis").rand(1000,100000));
                        else:
                            $session=Yii::$app->session->id;
                        endif;
                        if( strtolower( $_SERVER[ 'REQUEST_METHOD' ] ) == 'post' && !empty( $_FILES ) ) {
                                $directory = Yii::getAlias('@uploads/uploads/') .$session;
                                if (!is_dir($directory)) {
                                                FileHelper::createDirectory($directory);
                                }
                                
                                foreach ($_FILES as $k=>$v){
                                    if(is_array($v)){
                                        //for single files
                                        if(is_array($v)){
                                            if(!is_array($v['tmp_name']) && !empty($v['tmp_name'])){					
                                                //echo $k." - ". $v['name']." - " .$v['tmp_name']." ".$v['size']."<br>";
                                                //move_uploaded_file( $v['tmp_name'], $filePath."/".$v['name']);
                                                $uid = uniqid(time(), true);   
                                                $uid= str_replace(".","-",$uid);
                                                $fileName = $uid . '_' . str_replace(" ","_",$v['name']);
                                                $filePath = $directory;
                                                if (strpos($k, 'yumpee-image') !== false) {
                                                    list($label,$width,$height) = explode("_",$k);
                                                    $resize = new ResizeImage($v['tmp_name']);
                                                    $resize->resizeTo($width, $height, 'exact');
                                                    $resize->saveImage($filePath."/".$fileName);
                                                }else{
                                                    move_uploaded_file( $v['tmp_name'], $filePath."/".$fileName); // move to new location perhaps?
                                                }
                                                $frmFiles = new FormFiles();
                                                $frmFiles->setAttribute("form_submit_id",$id);
                                                $frmFiles->setAttribute("file_name",$v['name']);
                                                $frmFiles->setAttribute("file_path",$session ."/".$fileName);
                                                $frmFiles->setAttribute("file_type",$v['type']);
                                                $frmFiles->setAttribute("file_size",filesize($filePath."/".$fileName));
                                                $frmFiles->save();
                                            }
				
                                        }
                                        $counter=0;
                                        foreach ($v as $sk=>$sv){ 
                                            $arr[$sk][$k]=$sv;
                                            if(is_array($sv) && !empty($v['tmp_name'][$counter])){
                                                    //echo $k." - ". $v['name'][$counter]." - ".$v['tmp_name'][$counter]."-".$v['type'][$counter]."<br>"	;
                                                    //move_uploaded_file( $v['tmp_name'][$counter], $filePath."/".$v['name'][$counter]);
                                                    $uid = uniqid(time(), true);   
                                                    $uid= str_replace(".","-",$uid);
                                                    $fileName = $uid . '_' . str_replace(" ","_",$v['name'][$counter]);
                                                    $filePath = $directory;
                                                    if (strpos($k, 'yumpee-image') !== false) {
                                                        list($label,$width,$height) = explode("_",$k);
                                                        $resize = new ResizeImage($v['tmp_name'][$counter]);
                                                        $resize->resizeTo($width, $height, 'exact');
                                                        $resize->saveImage($filePath."/".$fileName);
                                                    }else{
                                                        move_uploaded_file( $v['tmp_name'][$counter], $filePath."/".$fileName); // move to new location perhaps?
                                                    }
                                                    $frmFiles = new FormFiles();
                                                    $frmFiles->setAttribute("form_submit_id",$id);
                                                    $frmFiles->setAttribute("file_name",$v['name'][$counter]);
                                                    $frmFiles->setAttribute("file_path",$session ."/".$fileName);
                                                    $frmFiles->setAttribute("file_type",$v['type'][$counter]);
                                                    $frmFiles->setAttribute("file_size",filesize($filePath."/".$fileName));
                                                    $frmFiles->save();
                                            }
                                            $counter++;
				
                                        }
                                    }
                                }
                                
                                
                            }
                            //lets see if we can connect to the external webservice
                            $webhook = WebHook::find()->where(['hook_type'=>'E'])->andWhere('form_id="'.$form->id.'"')->one();
                            if($webhook!=null && $webhook->end_point!=""):
                                $hook_behave = $this->attachBehavior('myhook', new \frontend\components\FormSubmitAPIBehaviour);
                                $return = $hook_behave->connect($webhook,$_POST);
                                //our return can either be a json encode back or passed into a renderer
                                if(Yii::$app->request->post("return-type")=="json"):
                                    return $return;
                                else:
                                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                                    //we handle the loading of twig template if it is turned on
                                        $theme_id = ContentBuilder::getSetting("current_theme");
                                        $renderer = CustomWidget::find()->where(['id'=>$webhook->response_target])->one();
                                    //since we may get the widget we want to use to display the result
                                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer['name'],'renderer_type'=>'I'])->one();
                                        if(($codebase!=null)&& ($codebase['code']<>"")):
                                            $loader = new Twig();
                                            $twig = new \Twig_Environment($loader);
                                            $content= $twig->render($codebase['filename'],['app'=>Yii::$app,'webservice'=>$return]);
                                            return $this->renderPartial('@frontend/views/layouts/html',['data'=>$content]);
                                        else:
                                            return $return;
                                        endif;
                                    endif;
                                    
                                endif;
                                
                            endif;
                endif;
                return "Form saved successfully ";
        }
    }
}

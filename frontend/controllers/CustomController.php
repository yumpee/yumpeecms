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

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use frontend\components\ContentBuilder;
use frontend\models\Pages;
use frontend\models\Twig;
use frontend\models\Templates;
use frontend\models\CustomSettings;
use backend\models\Settings;
use backend\models\Domains;
use backend\models\ClassSetup;
use backend\models\ClassElement;
use common\components\ResizeImage;
use backend\models\Media;
use backend\models\Articles;


class CustomController extends Controller{
    public static function allowedDomains()
{
    if(ContentBuilder::getSetting("allow_multiple_domains")=="Yes"):
		return Domains::find()->select('domain_url')->where(['active_stat'=>'Yes'])->column();
	endif;
}

/**
 * @inheritdoc
 */
public function behaviors()
{
    return array_merge(parent::behaviors(), [

        // For cross-domain AJAX request
        'corsFilter'  => [
            'class' => \yii\filters\Cors::className(),
            'cors'  => [
                // restrict access to domains:
                'Origin'                           => static::allowedDomains(),
                'Access-Control-Request-Method'    => ['POST','GET'],
                'Access-Control-Allow-Credentials' => false,
                'Access-Control-Max-Age'           => 3600,                 // Cache (seconds)
            ],
        ],

    ]);
}


public function actionIndex(){
	
     $page =[];
     $news_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
     if (strpos($news_url, '?') !== false):
           list($news_url,$search)= explode("?",$news_url);
     endif;      
     $article = Pages::find()->where(['url'=>$news_url])->one();     
     if(($article['require_login']=="Y")&&(Yii::$app->user->isGuest)):
         return "Permission denied";
     endif;
     if(strpos($article['permissions'],Yii::$app->user->identity->role_id) === false && $article['url']==str_replace("/","",$news_url)):
         return "Permission Denied";
     endif;
     
    $theme_id = ContentBuilder::getSetting("current_theme");
    
    if(Yii::$app->request->post("method")=="get"):
        if(Yii::$app->request->post("setting_name")=="*"):
            $setting = CustomSettings::find()->where(['theme_id'=>$theme_id])->all();
            return \yii\helpers\Json::encode($setting);
        endif;
        $setting = ContentBuilder::getSetting(Yii::$app->request->post("setting_name"));
        return \yii\helpers\Json::encode($setting);        
    endif;    
    if(Yii::$app->request->post("method")=="set"):
        foreach($_POST as $key => $value): 
        if($value<>""):
			if($key=="method"):
				continue;
			endif;
            $setting = CustomSettings::find()->where(['setting_name'=>$key])->andWhere('theme_id="'.$theme_id.'"')->one();
            if($setting!=null):				
                $setting->setAttribute("setting_value",$value);
                $setting->save();
            else:				
                $setting = new CustomSettings();
                $setting->setAttribute("id",md5(date("YHmis").rand(10000,10000000)));
                $setting->setAttribute("theme_id",$theme_id);
                $setting->setAttribute("setting_value",$value);
                $setting->setAttribute("setting_name",$key);
				$setting->setAttribute("description",$value);
                $setting->save();
            endif;
        endif;
        endforeach;
        return "Update completed";
    endif;
	
	/////////////////////////////////////////////////////////WORKING WITH ARTICLES ///////////////////////////////////////////
	if(Yii::$app->request->post("method")=="getarticle"):
		if(Yii::$app->request->post("id")=="all"):
			$articles = Articles::find()->all();
			return \yii\helpers\Json::encode($articles);
		else:
			$articles = Articles::find()->where(['id'=>Yii::$app->request->post("id")])->one();
			return \yii\helpers\Json::encode($articles);
		endif;
			
	endif;
	if(Yii::$app->request->post("method")=="addarticle"):
	
			$display_image_id="";
        if( strtolower( $_SERVER[ 'REQUEST_METHOD' ] ) == 'post' && !empty( $_FILES ) && !Yii::$app->user->isGuest) {	
								
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
                                                $image_location=realpath($filePath."/".$fileName);
                                                $mime = mime_content_type($image_location);
                                                $mime_type = explode("/",$mime);
                                                $up_type="1";
                                                if($mime_type[0]=="image"):
                                                    $up_type="1";
                                                endif;                                                
                                                $media = new Media();
                                                $random=rand(10000,10000000);
												$id = md5(date('YmdHis').$random);
												
												$id = substr($id,0,36);
                                                $media->setAttribute('id',$id);
                                                $media->setAttribute('upload_date',date('Y-m-d'));
                                                $media->setAttribute('author',Yii::$app->user->identity->id);
                                                $media->setAttribute('media_type',$up_type);
                                                $media->setAttribute('size',filesize($filePath."/".$fileName));
                                                $media->setAttribute('path',$session ."/".$fileName);
                                                $media->setAttribute('name',$v['name']);
                                                $media->setAttribute('alt_tag',$k);                
                                                $media->save();
												$display_image_id=$id;
                                                
                                            }
                                        }
                                    }
                                }
        }
	
			$id=md5(date('Ymdis'));
            if(Yii::$app->request->post("url")==""):
                $url=strtolower(str_replace(" ","-",Yii::$app->request->post("title")));
            else:
                $url=strtolower(str_replace(" ","-",Yii::$app->request->post("url")));
            endif;
			
            
			
           //we need to check if a url already exist
           $url_exist = Articles::find()->where(['url'=>$url])->one();
           if($url_exist!=null):
               $url=$url."_".md5(date("YmdHis").rand(10000,1000000));
			   
           endif;
		   
           $archive = date("mY");
           $model = new Articles();
           $model->setAttribute('id',$id);
           $model->setAttribute('url',$url);
		   
           $model->setAttribute('title',Yii::$app->request->post("title"));
           $model->setAttribute('lead_content',str_replace("../../uploads",Yii::getAlias("@image_dir"),Yii::$app->request->post("lead_content")));
           $model->setAttribute('body_content',str_replace("../../uploads",Yii::getAlias("@image_dir"),Yii::$app->request->post("body_content")));
		   
           $model->setAttribute('date',date("Y-m-d"));
           $model->setAttribute('master_content','1');
           $model->setAttribute('published','1');
		  
           $model->setAttribute('alternate_header_content',Yii::$app->request->post("alternate_header_content"));
           $model->setAttribute('show_header_image','1');           
           $model->setAttribute('usrname',Yii::$app->user->identity->username);
		 
           $model->setAttribute('display_image_id',$display_image_id);
           $model->setAttribute('thumbnail_image_id',Yii::$app->request->post("thumbnail_image_id"));
           $model->setAttribute('article_type',Yii::$app->request->post("article_type"));
		    
           $model->setAttribute('featured_media',Yii::$app->request->post("featured_media"));
           $model->setAttribute('require_login',Yii::$app->request->post("require_login"));
		   
		   $model->setAttribute('published_by_stat','1');
		     
           $model->setAttribute('disable_comments',"Y");
		   
		   $model->setAttribute('rating',"0");
		   
		   
		   $model->setAttribute('no_of_views',"0");
           $model->setAttribute('render_template',Yii::$app->request->post("render_template"));           
           
           
           $model->save(false); 
		return "Article saved";
	endif;
    if(Yii::$app->request->post("method")=="addpage"):
		//if we are required to do a bulk pages update we check for mode=all
		if(Yii::$app->request->post("id")=="*"):
			foreach($_POST as $key => $value): 
				if($value<>""):
					if($key=="method"):
						continue;
					endif;
					$page = Pages::find()->where(['id' => $key])->one();
					if($page!=null):
						$page->setAttribute("menu_title",$value);						
						$page->save(false);
					endif;   
				endif;
			endforeach;
			
			return "Update completed";
		endif;
        $page = Pages::find()->where(['id' => Yii::$app->request->post("id")])->one();
        if($page!=null):
            $page->setAttribute("menu_title",Yii::$app->request->post("menu_title"));
			if(Yii::$app->request->post("description")!=null):
				$page->setAttribute("description",Yii::$app->request->post("description"));
			endif;
			if(Yii::$app->request->post("show_in_menu")!=null):
				$page->setAttribute("show_in_menu",Yii::$app->request->post("show_in_menu"));
			endif;
			if(Yii::$app->request->post("show_in_footer_menu")!=null):
				$page->setAttribute("show_in_footer_menu",Yii::$app->request->post("show_in_footer_menu"));
			endif;
            $page->save(false);
            return "Updated completed";
        endif;
    endif;
    if(Yii::$app->request->post("method")=="addclass"):
        //we check if class exists and if not we create it or update
        $display_image_id="";
        if( strtolower( $_SERVER[ 'REQUEST_METHOD' ] ) == 'post' && !empty( $_FILES ) && !Yii::$app->user->isGuest) {	
								
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
                                                $image_location=realpath($filePath."/".$fileName);
                                                $mime = mime_content_type($image_location);
                                                $mime_type = explode("/",$mime);
                                                $up_type="1";
                                                if($mime_type[0]=="image"):
                                                    $up_type="1";
                                                endif;                                                
                                                $media = new Media();
                                                $random=rand(10000,10000000);
												$id = md5(date('YmdHis')).$random;
												$id = substr($id,0,36);
                                                $media->setAttribute('id',$id);
                                                $media->setAttribute('upload_date',date('Y-m-d'));
                                                $media->setAttribute('author',Yii::$app->user->identity->id);
                                                $media->setAttribute('media_type',$up_type);
                                                $media->setAttribute('size',filesize($filePath."/".$fileName));
                                                $media->setAttribute('path',$session ."/".$fileName);
                                                $media->setAttribute('name',$v['name']);
                                                $media->setAttribute('alt_tag',$k);                
                                                $media->save();
                                                $display_image_id=$id;
                                            }
                                        }
                                    }
                                }
        }
        $setting = ClassSetup::find()->where(['id'=>Yii::$app->request->post("id")])->one();
            if($setting!=null):
                if($display_image_id!=""):
                    $setting->setAttribute("display_image_id",$display_image_id);
                endif;
                $setting->setAttribute("alias",Yii::$app->request->post("alias"));
                $setting->save();
            else:
                $setting = new ClassSetup();
                
                $setting->setAttribute("id",md5(date("YHmis").rand(10000,10000000)));
                $setting->setAttribute("name",Yii::$app->request->post("name"));
                $setting->setAttribute("alias",Yii::$app->request->post("alias"));
                $setting->setAttribute("parent_id",Yii::$app->request->post("parent_id"));
                if($display_image_id!=""):
                    $setting->setAttribute("display_image_id",$display_image_id);
                endif;
                $setting->save();
            endif;
			return "Form Saved";
    endif;
    if(Yii::$app->request->post("method")=="addelement"):
        //we check if element exists and if not we create it or update
            $setting = ClassElement::find()->where(['id'=>Yii::$app->request->post("id")])->one();
            if($setting!=null):
                $setting->setAttribute("alias",Yii::$app->request->post("alias"));
                $setting->save();
            else:
                $setting = new ClassElement();
                $setting->setAttribute("id",md5(date("YHmis").rand(10000,10000000)));
                $setting->setAttribute("name",Yii::$app->request->post("name"));
                $setting->setAttribute("alias",Yii::$app->request->post("alias"));
                $setting->setAttribute("class_id",Yii::$app->request->post("class_id"));
                $setting->save();
            endif;
    endif;
    if(Yii::$app->request->post("method")=="delclass"):
        //we check if class exists and if not we create it or update
        $setting = ClassElement::find()->where(['class_id'=>Yii::$app->request->post("id")])->one();
        if($setting==null):
            $a = ClassSetup::findOne(Yii::$app->request->post("id"));
            $a->delete();
        else:
            return "Error: Cannot delete a class with a child element";
        endif;
    endif;
    if(Yii::$app->request->post("method")=="delelement"):
        //we check if element exists and if not we create it or update
        $a = ClassElement::findOne(Yii::$app->request->post("id"));
        $a->delete();
    endif;
    if(Yii::$app->request->post("method")=="addmedia"):
		
        //add a media and then return the addition details
        if( strtolower( $_SERVER[ 'REQUEST_METHOD' ] ) == 'post' && !empty( $_FILES ) && !Yii::$app->user->isGuest) {
			
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
                                                $image_location=realpath($filePath."/".$fileName);
                                                $mime = mime_content_type($image_location);
                                                $mime_type = explode("/",$mime);
                                                $up_type="1";
                                                if($mime_type[0]=="image"):
                                                    $up_type="1";
                                                elseif($mime_type[0]=="video"):
                                                    $up_type="2";
                                                elseif($mime_type[0]=="audio"):
                                                    $up_type="3";
                                                elseif($mime_type[0]=="text"):
                                                    $up_type="4";
                                                elseif($mime_type[0]=="application"):
                                                    $up_type="5";
                                                else:
                                                    $up_type="6";
                                                endif;
                                                $media = new Media();
                                                $random=rand(10000,10000000);
						$id = md5(date('YmdHis')).$random;
                                                $media->setAttribute('id',$id);
                                                $media->setAttribute('upload_date',date('Y-m-d'));
                                                $media->setAttribute('author',Yii::$app->user->identity->id);
                                                $media->setAttribute('media_type',$up_type);
                                                $media->setAttribute('size',filesize($filePath."/".$fileName));
                                                $media->setAttribute('path',$session ."/".$fileName);
                                                $media->setAttribute('name',$v['name']);
                                                $media->setAttribute('alt_tag',$k);                
                                                $media->save();
                                            }
				
                                        }
                                        $counter=0;
                                        foreach ($v as $sk=>$sv){ 
                                            $arr[$sk][$k]=$sv;
                                            if(is_array($sv) && !empty($v['tmp_name'][$counter])){                                                    
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
                                                    $image_location=realpath($filePath."/".$fileName);
                                                    $mime = mime_content_type($image_location);
                                                    $mime_type = explode("/",$mime);
                                                    $up_type="1";
                                                    if($mime_type[0]=="image"):
                                                        $up_type="1";
                                                    elseif($mime_type[0]=="video"):
                                                        $up_type="2";
                                                    elseif($mime_type[0]=="audio"):
                                                        $up_type="3";
                                                    elseif($mime_type[0]=="text"):
                                                        $up_type="4";
                                                    elseif($mime_type[0]=="application"):
                                                        $up_type="5";
                                                    else:
                                                        $up_type="6";
                                                    endif;
                                                    $media = new Media();
                                                    $random=rand(10000,10000000);
													$id = md5(date('YmdHis')).$random;
                                                    $media->setAttribute('id',$id);
                                                    $media->setAttribute('upload_date',date('Y-m-d'));
                                                    $media->setAttribute('author',Yii::$app->user->identity->id);
                                                    $media->setAttribute('media_type',$up_type);
                                                    $media->setAttribute('size',filesize($filePath."/".$fileName));
                                                    $media->setAttribute('path',$session ."/".$fileName);
                                                    $media->setAttribute('name',$v['name'][$counter]);
                                                    $media->setAttribute('alt_tag',$k);                
                                                    $media->save();                                                    
                                            }
                                            $counter++;
				
                                        }
                                    }
                                }
                                
                            }
        return $id;
    endif;
    
}

}
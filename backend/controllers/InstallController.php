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
 * Description of Download
 *
 * @author Peter
 */
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use backend\models\Media;
use backend\models\Themes;
use backend\models\Twig;
use backend\models\Forms;
use backend\models\Pages;
use backend\models\Widgets;
use backend\models\CustomWidget;
use backend\models\Templates;

class InstallController extends Controller {
    public function actionIndex(){
       $page=[];
       
       return $this->render('index',$page);
    }
    
    public function actionUploadExten(){
    $file_name="yprolearn.zip";
    $ran_folder = rand(10,10000000);
    $directory=Yii::getAlias('@uploads/uploads/').Yii::$app->session->id ."/".$ran_folder;
    if (!is_dir($directory)) {
                FileHelper::createDirectory($directory);
    }
    
    if(isset($_FILES['image'])){
        //return ("test");
      $errors= array();
      $file_name = $_FILES['image']['name'];
      $file_size =$_FILES['image']['size'];
      $file_tmp =$_FILES['image']['tmp_name'];
      $file_type=$_FILES['image']['type'];
      //return $file_name;
      //$file_ext=strtolower(end(explode('.',$file_name)));
      list($file_name1,$file_ext) = explode(".",$file_name);
      $expensions= array("zip");
      
      if(in_array($file_ext,$expensions)=== false){
         $errors[]="extension not allowed, please choose a ZIP file.";
      }
      
      if($file_size > 2097152){
         //$errors[]='File size must be excately 2 MB';
      }
      $rand_folder="";
      if(empty($errors)==true){      
         move_uploaded_file($file_tmp,Yii::getAlias('@uploads/uploads/').Yii::$app->session->id ."/".$ran_folder."/".$file_name);
         //echo "Success";
      }else{
         print_r($errors);
      }
   }
        // zip file path.
    $file = $path = Yii::getAlias('@uploads/uploads/').Yii::$app->session->id ."/".$ran_folder."/".$file_name; 
        //create an instance of ZipArchive Class
    $zip = new \ZipArchive();
 
        //open the file that you want to unzip. 
        //NOTE: give the correct path. In this example zip file is in the same folder
    $zipped = $zip->open($file);
 
    // get the absolute path to $file, where the files has to be unzipped
    $path = pathinfo(realpath($file), PATHINFO_DIRNAME);
 
    //check if it is actually a Zip file
    if ($zipped) { 
    //if yes then extract it to the said folder
    $extract = $zip->extractTo($path);
 
    //if unzipped succesfully then show the success message
        if($extract){
            //echo "Your file extracted to $folder_name";            
            $property_file = Yii::getAlias('@uploads/uploads/').Yii::$app->session->id ."/".$ran_folder."/properties.xml";
            $path = realpath($property_file);
            $install_file = file_get_contents($path);
            $xml = simplexml_load_string($install_file);
            //Yii::$app->api->sendSuccessResponse(json_encode($xml));
            $a = array();
            $a['folders'] = $ran_folder;
            $a['properties'] = $xml;
            
            //array_push($a,$xml);
            $json = json_encode($a);            
            return $json;
        } else {
            echo "There was a problem with the uploaded file";
        }
 
        //close the zip
        $zip->close();  
        
        //we should probably display information about the extracted zip before attempting to install it
        echo "Successfully Installed";
    }
}


public function actionInstall(){    
    //we need to read the install.xml files
    $ran_folder = Yii::$app->request->get("id");
    $properties_file = Yii::getAlias('@uploads/uploads/').Yii::$app->session->id ."/".$ran_folder."/properties.xml";
    $path = realpath($properties_file);
    $prop_contents = simplexml_load_string(file_get_contents($path)); 
    $theme = new Themes();
    $theme->setAttribute("name",$prop_contents->theme->name);
    $theme->setAttribute("folder",$ran_folder);
    $theme->setAttribute("description",$prop_contents->description);
    
    
    
    $install_file = Yii::getAlias('@uploads/uploads/').Yii::$app->session->id ."/".$ran_folder."/install.xml";
    $path = realpath($install_file);       
    $contents = simplexml_load_string(file_get_contents($path)); 
    if($contents->header!=null){ $theme->setAttribute("header",$contents->header); }
    if($contents->footer!=null){$theme->setAttribute("footer",$contents->footer); }
    if($contents->stylesheet!=null){$theme->setAttribute("stylesheet",$contents->stylesheet); }
    if($contents->javascript!=null){$theme->setAttribute("javascript",$contents->javascript); }
    if($contents->custom_styles!=null){$theme->setAttribute("custom_styles",$contents->custom_styles);}
    
    if($prop_contents->deployment=="plugin" || $prop_contents->deployment=="extension"):
        $theme_id = \frontend\components\ContentBuilder::getSetting("current_theme");
    else:
        $theme->save(false);
        $theme_id = $theme->getPrimaryKey();
    endif;
    
    //$theme_id=1;
    //lets get the settings in
    $twig = new Twig();
    $twig->setAttribute("theme_id",$theme_id);
    $twig->setAttribute("renderer",$theme_id."_".$ran_folder);
    $twig->setAttribute("renderer_type","Z");
    if($contents->settings!=null){$twig->setAttribute("code","<!--Refer to ".$contents->settings." for content-->");}
    if($contents->filename!=null){$twig->setAttribute("filename",$contents->settings);}
    $twig->save(false); 
    
    //lets check for settings in the system
    if(is_file(realpath(Yii::getAlias('@uploads/uploads/').Yii::$app->session->id ."/".$ran_folder."/settings.xml"))):
        $settings_file = Yii::getAlias('@uploads/uploads/').Yii::$app->session->id ."/".$ran_folder."/settings.xml";
        
        $contents = simplexml_load_string(file_get_contents(realpath($settings_file))); 
        foreach($contents->settings as $a):
            foreach($a->item->custom_setting as $item):
                $setting = new \backend\models\CustomSettings();
                $setting->setAttribute("setting_name",$item->setting);
                $setting->setAttribute("setting_value",$item->value);
                $setting->setAttribute("theme_id",$theme_id);
                $setting_id = md5(rand(1000,10000000));
                $setting->setAttribute("id",$setting_id);
                $setting->save(false);
            endforeach;
        endforeach;
        foreach($contents->settings as $a):
            foreach($a->item->system_setting as $item):
                $setting = new \backend\models\CustomSettings();
                $setting->setAttribute("setting_name",$item->setting);
                $setting->setAttribute("setting_value",$item->value);            
                $setting_id = md5(rand(1000,10000000));
                $setting->setAttribute("id",$setting_id);
                $setting->save(false);
            endforeach;
        endforeach;
    endif;
    
    //lets check for classes in the installation
    if(is_file(realpath(Yii::getAlias('@uploads/uploads/').Yii::$app->session->id ."/".$ran_folder."/classes.xml"))):
        $classes_file = Yii::getAlias('@uploads/uploads/').Yii::$app->session->id ."/".$ran_folder."/classes.xml";
        $contents = simplexml_load_string(file_get_contents(realpath($classes_file))); 
        
        foreach($contents->classes->item as $item):
            $setting = new \backend\models\ClassSetup();
            $setting->setAttribute("id",$item->id);
            $setting->setAttribute("name",$item->name);        
            $setting->setAttribute("alias",$item->alias);
            $setting->setAttribute("parent_id",$item->parent);
            $setting->save(false);
            foreach($item->attributes->item as $attrib):
                $setting = new \backend\models\ClassAttributes();
                $setting->setAttribute("id",$attrib->id);
                $setting->setAttribute("name",$attrib->name);        
                $setting->setAttribute("alias",$attrib->alias);
                $setting->setAttribute("parent_id",$attrib->parent);
                $setting->setAttribute("class_id",$attrib->class);
                $setting->setAttribute("description",$attrib->description);
                $setting->setAttribute("display_order",$attrib->display_order);
                $setting->save(false);
            endforeach;
            foreach($item->elements->item as $elem):
                $setting = new \backend\models\ClassElement();
                $setting->setAttribute("id",$elem->id);
                $setting->setAttribute("name",$elem->name);        
                $setting->setAttribute("alias",$elem->alias);
                $setting->setAttribute("parent_id",$elem->parent);
                $setting->setAttribute("class_id",$elem->class);
                $setting->setAttribute("description",$elem->description);
                $setting->setAttribute("display_order",$elem->display_order);
                $setting->save(false);
            endforeach;
        endforeach;
    endif;
    
    //lets install the twig codes
    
    
    //Install the views
    $path = realpath($install_file);       
    $contents = simplexml_load_string(file_get_contents($path)); 
    foreach($contents->views as $a):
	foreach($a->item as $item):
        if($item->group=="F"):
            $twig = new Twig();
            $twig->setAttribute("theme_id",$theme_id);
            $twig->setAttribute("renderer",$item->system);
            $twig->setAttribute("renderer_type","F");
            $twig->setAttribute("code","<!--Refer to ".$item->custom." for content-->");
            $twig->setAttribute("filename",$item->custom);
            $twig->save(false);  
            
            $model = Forms::find()->where(['id'=>$item->id])->one();
            if($model!=null):
                
            else:
            $form = new Forms();
            $form->setAttribute("id",$item->id);
            $form->setAttribute("name",$item->system);
            $form->setAttribute("title",$item->description);
            $form->setAttribute("form_type",$item->type);
            $form->setAttribute("form_fill_entry_type",$item->fill);
            $form->setAttribute("form_fill_limit",$item->limit);
            $form->setAttribute("published",$item->published);
            $form->setAttribute("show_in_menu",$item->menu);
            $form->save(false);  
            endif;
              
        endif;
        if($item->group=="R"):
            $twig = new Twig();
            $twig->setAttribute("theme_id",$theme_id);
            $twig->setAttribute("renderer",$item->system);
            $twig->setAttribute("renderer_type","R");
            $twig->setAttribute("code","<!--Refer to ".$item->custom." for content-->");
            $twig->setAttribute("filename",$item->custom);
            $twig->save(false);  
            
            //we should check if url exists and if does then amend this url
            $model = Pages::find()->where(['url'=>$item->url])->one();
            if($model!=null):
                $url_rec= $item->url."-".md5(date("YmdHis"));
                $model->setAttribute("url",$url_rec);
                $model->setAttribute("published",$item->published);
                $model->setAttribute("require_login",$item->require_login);
                $model->setAttribute("menu_title",$item->menu_title);
                $model->setAttribute("title",$item->title);
                $model->setAttribute("description",$item->page_desc);
                $model->setAttribute("template",$item->template);
                $model->save(false); 
            else:
                $url_rec= $item->url;
                $form = new Pages();
                $form->setAttribute("id",$item->id);
                $form->setAttribute("url",$url_rec);
                $form->setAttribute("published",$item->published);
                $form->setAttribute("require_login",$item->require_login);
                $form->setAttribute("menu_title",$item->menu_title);
                $form->setAttribute("title",$item->title);
                $form->setAttribute("description",$item->page_desc);
                $form->setAttribute("template",$item->template);
                $form->save(false); 
            endif;
            
        endif;
        if($item->group=="Z"):            
            $twig = new Twig();
            $twig->setAttribute("theme_id",$theme_id);
            $twig->setAttribute("renderer",$item->system);
            $twig->setAttribute("renderer_type","Z");
            $twig->setAttribute("code","<!--Refer to ".$item->custom." for content-->");
            $twig->setAttribute("filename",$item->custom);
            $twig->save(false);  
        endif;
        if($item->group=="V"):
            $twig = new Twig();
            $twig->setAttribute("theme_id",$theme_id);
            $twig->setAttribute("renderer",$item->system);
            $twig->setAttribute("renderer_type","V");
            $twig->setAttribute("code","<!--Refer to ".$item->custom." for content-->");
            $twig->setAttribute("filename",$item->custom);
            $twig->save(false);  
            
            $model = Templates::find()->where(['id'=>$item->id])->one();
            if($model==null):
                $form = new Templates();
                $form->setAttribute("id",$item->id);
                $form->setAttribute("name",$item->name);
                $form->setAttribute("route",$item->route);
                $form->setAttribute("url",$item->url);
                $form->setAttribute("internal_route_stat",$item->route_stat);
                $form->setAttribute("parent_id",$item->parent_id);
                $form->setAttribute("renderer",$item->renderer);
                $form->save(false);
            endif;
        endif;
        if($item->group=="W"):
            $twig = new Twig();
            $twig->setAttribute("theme_id",$theme_id);
            $twig->setAttribute("renderer",$item->system);
            $twig->setAttribute("renderer_type","W");
            $twig->setAttribute("code","<!--Refer to ".$item->custom." for content-->");
            $twig->setAttribute("filename",$item->custom);
            $twig->save(false); 
            $model = Widgets::find()->where(['id'=>$item->id])->one();
            if($model==null):
                $form = new Widgets();            
                $form->setAttribute("short_name",$item->name);
                $form->setAttribute("name",$item->description);
                $form->setAttribute("setting_value",$item->setting);
                $form->setAttribute("template_type",$item->template);
                $form->setAttribute("parent_id",$item->parent_id);
                $form->save(false); 
            endif;
        endif;
        if($item->group=="I"):
            $twig = new Twig();
            $twig->setAttribute("theme_id",$theme_id);
            $twig->setAttribute("renderer",$item->system);
            $twig->setAttribute("renderer_type","I");
            $twig->setAttribute("code","<!--Refer to ".$item->custom." for content-->");
            $twig->setAttribute("filename",$item->custom);
            $twig->save(false);  
            $model = CustomWidget::find()->where(['id'=>$item->id])->one();
            if($model==null):
                $form = new CustomWidget();
                $form->setAttribute("id",$item->id);
                $form->setAttribute("name",$item->system);
                $form->setAttribute("title",$item->description);
                $form->setAttribute("form_id",$item->form);
                $form->save(false); 
            endif;
        endif;
        endforeach;    
    endforeach;
    
    //move folder to the theme directory
    $install_src = Yii::getAlias('@uploads/uploads/').Yii::$app->session->id ."/".$ran_folder;
    $source = realpath($install_src); 
    
    $target_src = Yii::getAlias('@frontend/themes/'.$ran_folder);
    
    //$destination = realpath($target_src);
    //return $destination;
    $this->rcopy($source , $target_src );    
    
    return "Installation Completed";
}

// Function to remove folders and files 
 function rrmdir($dir) {
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file)
                if ($file != "." && $file != "..") rrmdir("$dir/$file");
            rmdir($dir);
        }
        else if (file_exists($dir)) unlink($dir);
 }

    // Function to Copy folders and files       
 function rcopy($src, $dst) {
        if (file_exists ( $dst ))
            $this->rrmdir ( $dst );
        if (is_dir ( $src )) {
            mkdir ( $dst );
            $files = scandir ( $src );
            foreach ( $files as $file )
                if ($file != "." && $file != "..")
                    $this->rcopy ( "$src/$file", "$dst/$file" );
        } else if (file_exists ( $src ))
            copy ( $src, $dst );
    }

}

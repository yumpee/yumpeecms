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
        echo "Successfully Installed";
    }
}


public function actionInstall(){    
    //we need to read the install.xml files
    $ran_folder = Yii::$app->request->get("id");
    $properties_file = Yii::getAlias('@uploads/uploads/').Yii::$app->session->id ."/".$ran_folder."/properties.xml";
    $path = realpath($properties_file);
    $contents = simplexml_load_string(file_get_contents($path)); 
    $theme = new Themes();
    $theme->setAttribute("name",$contents->theme->name);
    $theme->setAttribute("folder",$ran_folder);
    $theme->setAttribute("description",$contents->description);
    
    
    $install_file = Yii::getAlias('@uploads/uploads/').Yii::$app->session->id ."/".$ran_folder."/install.xml";
    $path = realpath($install_file);       
    $contents = simplexml_load_string(file_get_contents($path)); 
    if($contents->header!=null){ $theme->setAttribute("header",$contents->header); }
    if($contents->footer!=null){$theme->setAttribute("footer",$contents->footer); }
    if($contents->stylesheet!=null){$theme->setAttribute("stylesheet",$contents->stylesheet); }
    if($contents->javascript!=null){$theme->setAttribute("javascript",$contents->javascript); }
    if($contents->custom_styles!=null){$theme->setAttribute("custom_styles",$contents->custom_styles);}
    
    $theme->save(false);
    $theme_id = $theme->getPrimaryKey();
    //$theme_id=1;
    //lets get the settings in
    $twig = new Twig();
    $twig->setAttribute("theme_id",$theme_id);
    $twig->setAttribute("renderer",$theme_id."_".$ran_folder);
    $twig->setAttribute("renderer_type","Z");
    if($contents->settings!=null){$twig->setAttribute("code","<!--Refer to ".$contents->settings." for content-->");}
    if($contents->filename!=null){$twig->setAttribute("filename",$contents->settings);}
    $twig->save(false);  
    
    //Install the views
    foreach($contents->views->item as $item):
        $twig = new Twig();
        $twig->setAttribute("theme_id",$theme_id);
        $twig->setAttribute("renderer",$item->system);
        $twig->setAttribute("renderer_type","V");
        $twig->setAttribute("code","<!--Refer to ".$item->custom." for content-->");
        $twig->setAttribute("filename",$item->custom);
        $twig->save(false);        
    endforeach;
    
    //Install the Widgets
    foreach($contents->widgets->item as $item):
        $twig = new Twig();
        $twig->setAttribute("theme_id",$theme_id);
        $twig->setAttribute("renderer",$item->system);
        $twig->setAttribute("renderer_type","W");
        $twig->setAttribute("code","<!--Refer to ".$item->custom." for content-->");
        $twig->setAttribute("filename",$item->custom);
        $twig->save(false);        
    endforeach;
    
    //move folder to the theme directory
    $install_src = Yii::getAlias('@uploads/uploads/').Yii::$app->session->id ."/".$ran_folder;
    $source = realpath($install_src); 
    
    $target_src = Yii::getAlias('@frontend/themes/'.$ran_folder);
    
    //$destination = realpath($target_src);
    //return $destination;
    $this->rcopy($source , $target_src );
    
    //Install the Post
    
    //Install the Summary
    
    //Install the Details
    
    //Install the Form Widgets 
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

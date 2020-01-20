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
use fedemotta\datatables\DataTables;
use backend\models\BackEndMenus;
use backend\models\BackEndMenuRole;
use backend\models\Settings;
use backend\models\Themes;
use backend\models\ClassSetup;
use backend\models\ClassAttributes;
use backend\models\ClassElement;
use backend\models\Twig;
use backend\models\CustomSettings;

class PackageController extends Controller{
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
       $page=[];
       $page['records'] = Themes::find()->orderBy('name')->all();
       $page['classes'] = ClassSetup::find()->orderBy('name')->all();
       return $this->render('index',$page);
}

public function actionGenerate(){
    //this is where the plugin / extension package will be generated
    //create package directory
    $package_folder = Yii::getAlias('@uploads/uploads/').Yii::$app->session->id ."/".md5(rand(100,1000000));
    mkdir($package_folder,0700,true);
    //create properties.xml file
    if(Yii::$app->request->post("package_type")=="Plugin"):
        $ptype="plugin";
    endif;
    if(Yii::$app->request->post("package_type")=="Extension"):
        $ptype="extension";
    endif;
    if(Yii::$app->request->post("package_type")=="Theme"):
        $ptype="theme";
    endif;
    $properties = "<?xml version='1.0'?>
        <yumpee_properties>
            <version>".\frontend\components\ContentBuilder::getVersion()."</version>   
            <deployment>".$ptype."</deployment>
            <".$ptype.">
                <name>".Yii::$app->request->post("package_name")."</name>
                <short>".Yii::$app->request->post("short_description")."</short>
                <description>".Yii::$app->request->post("full_description")."</description>
            </".$ptype.">
	<author>
		<name>".Yii::$app->request->post("author_name")."</name>
		<company>".Yii::$app->request->post("organization")."</company>
		<support_email>".Yii::$app->request->post("support_email")."</support_email>
		<website>".Yii::$app->request->post("support_website")."</website>
		<phone>".Yii::$app->request->post("support_phone")."</phone>
	</author>	
    </yumpee_properties>";
    
    
    //create install.xml file
    $header="";
    $footer="";
    $css="";
    $javascript="";
    $widgets="";
    $forms="";
    $views="";
    $summary="";
    $details="";
    $cwidgets="";
    $custom_setting="";
    $setting_content="";
    $system_setting="";
    $class_content="";
    //header
    $theme_id = Themes::find()->where(['id'=>Yii::$app->request->post("source_theme")])->one();
    $theme_name = $theme_id['folder'];
    if($ptype=="theme"):        
        //lets copy the whole theme directory also
        $src_folder = realpath(Yii::getAlias('@frontend/themes/'.$theme_name));
        $this->rcopy($src_folder , realpath($package_folder));
    endif;
    if($ptype=="theme"):        
        $header ="<header>".$theme_id['header']."</header>";
    endif;
    
    if($ptype=="theme"):        
        $footer ="<footer>".$theme_id['footer']."</footer>";
    endif;
    
    file_put_contents($package_folder."/properties.xml",$properties);
    //create css
    if($theme_id['stylesheet']!=""):
        $css ="<stylesheet>".$theme_id['stylesheet']."</stylesheet>";
        //we should copy the folders accross to the deployment folder
    endif;
    //create javascript    
    if($theme_id['javascript']!=""):        
        $javascript="<javascript>".$theme_id['javascript']. "</javascript>";
        //we should copy the folders accross to the deployment folder
    endif;
    //create twig
    
    
    $twig = Twig::find()->select(['id','renderer','renderer_type','filename'])->with('form','page','templates','customWidget','widget')->asArray()->where(['theme_id'=>$theme_id["id"]])->andWhere('renderer_type IN ("I","W","V","F","R","Z")')->orderBy(['renderer_type'=>SORT_ASC,'renderer'=>SORT_ASC])->all();
     foreach($twig as $record):
         if(Yii::$app->request->post('c'.$record["id"])=="on"):
             if($record["renderer_type"]=="F"):
             $forms.="<item>
                        <id>".$record['form']['id']."</id>
                        <type>".$record['form']['form_type']."</type>
                        <name>".$record['form']['name']."</name>                            
			<description>".$record['form']['title']."</description>
                        <fill>".$record['form']['form_fill_entry_type']."</fill>
                        <limit>".$record['form']['form_fill_limit']."</limit>
                        <menu>".$record['form']['show_in_menu']."</menu>                            
			<published>".$record['form']['published']."</published>
                        <system>".$record['renderer']."</system>
			<custom>".$record['filename']."</custom>
                        <group>".$record['renderer_type']."</group>
		</item>";
                continue;
             endif;
             if($record["renderer_type"]=="R"):
             $summary.="<item>
                        <id>".$record['page']['id']."</id>
                        <url>".$record['page']['url']."</url>
                        <published>".$record['page']['published']."</published>
                        <require_login>".$record['page']['require_login']."</require_login>
                        <menu_title>".$record['page']['menu_title']."</menu_title>
                        <title>".$record['page']['title']."</title>
                        <page_desc>".$record['page']['description']."</page_desc>
                        <template>".$record['page']['template']."</template>
			<name>".$record['page']['title']."</name>
			<description>".$record['page']['title']."</description>
                        <system>".$record['renderer']."</system>
			<custom>".$record['filename']."</custom>
                        <group>".$record['renderer_type']."</group>
		</item>";
                continue;
             endif;
             if($record["renderer_type"]=="Z"):
             $details.="<item>
			<name>".$record['page']['title']."</name>
			<description>".$record['page']['title']."</description>
                        <system>".$record['renderer']."</system>
			<custom>".$record['filename']."</custom>
                        <group>".$record['renderer_type']."</group>
		</item>";
                continue;
             endif;
             
             if($record["renderer_type"]=="V"):
             $views.="<item>
                        <id>".$record['templates']['id']."</id>
                        <url>".$record['templates']['url']."</url>
                        <route>".$record['templates']['route']."</route>
                        <route_stat>".$record['templates']['internal_route_stat']."</route_stat>
                        <parent_id>".$record['templates']['parent_id']."</parent_id>
                        <renderer>".$record['templates']['renderer']."</renderer>
			<name>".$record['templates']['name']."</name>
			<description>".$record['templates']['name']."</description>
                        <system>".$record['renderer']."</system>
			<custom>".$record['filename']."</custom>
                        <group>".$record['renderer_type']."</group>
		</item>";
                continue;
             endif;
             if($record["renderer_type"]=="W"):
             $widgets.="<item>
                        <id>".$record['widget']['id']."</id>
                        <setting>".$record['widget']['setting_value']."</setting>
                        <template>".$record['widget']['template_type']."</template>
                        <parent_id>".$record['widget']['parent_id']."</parent_id>
			<name>".$record['widget']['short_name']."</name>
			<description>".$record['widget']['name']."</description>
                        <system>".$record['renderer']."</system>
			<custom>".$record['filename']."</custom>
                        <group>".$record['renderer_type']."</group>
		</item>";
                continue;
             endif;
             if($record["renderer_type"]=="I"):
             $cwidgets.="<item>
                        <id>".$record['customWidget']['id']."</id>
                        <form>".$record['customWidget']['form_id']."</form>
			<name>".$record['renderer']."</name>
			<description>".$record['customWidget']['title']."</description>
                        <system>".$record['renderer']."</system>
			<custom>".$record['filename']."</custom>
                        <group>".$record['renderer_type']."</group>
		</item>";
                continue;
             endif;
         endif;
     endforeach;    
     
     
    $install="<?xml version='1.0'?>
<yumpee_install>";
    if($header!=""):
	$install = $install.$header;
    endif;
    if($footer!=""):
	$install = $install.$footer;
    endif;
    if($javascript!=""):
	$install = $install.$javascript;
    endif;
    if($css!=""):
	$install = $install.$css;
    endif;
    $install.="<views>";
    if($widgets!=""):
	$install = $install.$widgets;
    endif;
    if($forms!=""):
	$install = $install.$forms;
    endif;
    if($views!=""):
	$install = $install.$views;
    endif;   
    if($details!=""):
	$install = $install.$details;
    endif; 
    if($summary!=""):
	$install = $install.$summary;
    endif; 
    if($cwidgets!=""):
	$install = $install.$cwidgets;
    endif; 
    $install.="</views>";
    $install = $install."</yumpee_install>";
    file_put_contents($package_folder."/install.xml",$install);
    
    if(Yii::$app->request->post("include_custom_setting")=="on"):
    //handle settings custom and theme settings
    $custom_settings = CustomSettings::find()->where(['theme_id'=>Yii::$app->request->post("source_theme")])->all();
    foreach($custom_settings as $setting):
            $setting_content.="<item><setting>".$setting['setting_name']."</setting>"
            . "<value>".$setting['setting_value']."</value></item>";
    endforeach;
    if(count($custom_settings) > 0):
        $custom_setting="<custom_setting>".$setting_content."</custom_setting>";
    endif;
    endif;
    $setting_content="";
    if(Yii::$app->request->post("include_theme_setting")=="on"):
        //handle settings custom and theme settings
        $custom_settings = CustomSettings::find()->where(['theme_id'=>null])->all();
        foreach($custom_settings as $setting):
            $setting_content.="<item><setting>".$setting['setting_name']."</setting>"
            . "<value>".$setting['setting_value']."</value></item>";
        endforeach;
        if(count($custom_settings) > 0):
            $system_setting="<system_setting>".$setting_content."</system_setting>";
        endif;
        
    endif;
    file_put_contents($package_folder."/settings.xml","<?xml version='1.0'?><settings>".$custom_setting.$system_setting."</settings>");
    
    //create class setups
    $class_setup = ClassSetup::find()->orderBy('name')->all();
    $attr_content="";
    $el_content="";
    foreach($class_setup as $cs):
        if(Yii::$app->request->post("cl".$cs["id"])=="on"):
            $class_content.="<item><id>".$cs["id"]."</id><name>".$cs["name"]."</name><alias>".$cs["alias"]."</alias><parent>".$cs["parent_id"]."</parent>";
            $attributes = ClassAttributes::find()->where(['class_id'=>$cs['id']])->all();
            $attr_content="";
            $el_content="";
            foreach($attributes as $ats):
                $attr_content.="<item><id>".$ats["id"]."</id><name>".$ats["name"]."</name><alias>".$ats["alias"]."</alias><class>".$ats["class_id"]."</class><parent_id>".$ats["parent_id"]."</parent_id><description>".$ats["description"]."</description><display_order>".$ats["display_order"]."</display_order></item>";
            endforeach;
            if($attributes!=null):
                $attr_content="<attributes>".$attr_content."</attributes>";
                $class_content.=$attr_content;
            endif;
            
            $elements = ClassElement::find()->where(['class_id'=>$cs['id']])->all();
            foreach($elements as $ats):
                $el_content.="<item><id>".$ats["id"]."</id><name>".$ats["name"]."</name><alias>".$ats["alias"]."</alias><parent_id>".$ats["parent_id"]."</parent_id><description>".$ats["description"]."</description><display_order>".$ats["display_order"]."</display_order></item>";
            endforeach;
            if($elements!=null):
                $el_content="<elements>".$el_content."</elements>";
                $class_content.=$el_content;
            endif;
            $class_content.="</item>";
        endif;
    endforeach;   
    if($class_content!=""):
        file_put_contents($package_folder."/classes.xml","<?xml version='1.0'?><classes>".$class_content."</classes>");
    endif;
    
    //zip what you have created
    // Get real path for our folder
$rootPath = realpath($package_folder);

// Initialize archive object
$zip = new \ZipArchive();
$zip->open(Yii::getAlias('@uploads/uploads/').Yii::$app->request->post("package_type").'-'.$theme_name.'-'.date("YmdH").'.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

// Create recursive directory iterator
/** @var SplFileInfo[] $files */
$files = new \RecursiveIteratorIterator(
    new \RecursiveDirectoryIterator($rootPath),
    \RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file)
{
    // Skip directories (they would be added automatically)
    if (!$file->isDir())
    {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);
        // Add current file to archive
        $zip->addFile($filePath, $relativePath);
    }
}

// Zip archive will be created only after closing object
$zip->close();

    //return file location of zipped file
    return "Package successfully created ".$package_folder;
}

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
   // Function to remove folders and files 
 function rrmdir($dir) {
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file)
                if ($file != "." && $file != "..") $this->rrmdir("$dir/$file");
            rmdir($dir);
        }
        else if (file_exists($dir)) unlink($dir);
 }
}
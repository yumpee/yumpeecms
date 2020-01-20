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
//this will build the site contents based on the URL and put everything into an array format to be navigated through mustache

namespace frontend\components;

use Yii;
use frontend\models\Pages;
use frontend\models\Templates;
use backend\models\Media;
use backend\models\Settings;
use frontend\models\CustomSettings;
use backend\models\Tags;
use backend\models\Themes;
use backend\models\Articles;
use backend\models\ArticlesCategories;
use backend\models\ClassElementAttributes;
use backend\models\MenuPage;
use backend\models\Roles;
use frontend\models\Domains;
use common\models\FormSubmit;

class ContentBuilder {
    const APPLICATION_VERSION ="2.0";
    public static function ArticleFilter($query,$filter){
        $filter_list = explode("|",$filter);
        
        foreach($filter_list as $filter_type):
            list($label,$param)=explode("=",$filter_type);
            if(strtolower(trim($label))=="category_url"):
                $page = ArticlesCategories::find()->where(['url'=>$param])->one();
                $category_id = $page['id'];
                $subQuery = (new \yii\db\Query())->select('articles_id')->from('tbl_articles_category_related')->where('category_id="'.$category_id.'"');
                $query->andWhere(['id'=>$subQuery]);
            endif;
            if(strtolower(trim($label))=="blog_index_url"):
                $page = Pages::find()->where(['url'=>$param])->one();
                $blog_index_id = $page['id'];
                $sql = "SELECT articles_id as id FROM tbl_articles_blog_index WHERE blog_index_id='".$blog_index_id."'";
                $subQuery = (new \yii\db\Query())->select('articles_id')->from('tbl_articles_blog_index')->where('blog_index_id="'.$blog_index_id.'"');
                $query->andWhere(['id'=>$subQuery]);
            endif;
            if(strtolower(trim($label))=="publisher"):
                $query->andWhere('usrname="'.$param.'"');
            endif;
        endforeach;
        return $query;
    }
    public static function getTemplateByURL($url){
        $records = Pages::findOne(['url'=>$url]);
        if($records!=null){
            return $records->template;
        }else{
            return "";
        }
    }
    public static function getScreenContent($text,$tags = '', $invert = FALSE){
        preg_match_all('/{(.+?)[\s]*\/?[\s]*}/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if(is_array($tags) AND count($tags) > 0) {
            if($invert == FALSE) {
                return preg_replace('@{(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?}.*?{/\1}@si', '', $text);
            } else {
                return preg_replace('@{('. implode('|', $tags) .')\b.*?}.*?{/\1}@si', '', $text);
            }
        }elseif($invert == FALSE) {
            return preg_replace('@{(\w+)\b.*?}.*?{/\1}@si', '', $text);
        }
        return $text;
    }
    public static function getURLByRoute($route){
        
        $sql = "SELECT id FROM tbl_templates WHERE route='".$route."'";
        $records = Templates::findBySql($sql)->one();
        
        if($records!=null){
            $template =  $records->id;
        }else{
            return "";
        }
        $sql = "SELECT url FROM tbl_page WHERE template='".$template."'";
        $records = Pages::findBySql($sql)->one();
       
        if($records!=null){
            return $records->url;
        }else{
            return "";
        }
        
    }
    public static function getThemeFolder(){
        $settings = new Themes();
        $my_theme_object=Themes::find()->where(['id'=>$settings->currentTheme])->one();
        return $my_theme_object->folder;
    }
    public static function getTemplateRouteByURL($url,$exact_route=true){
        
        //the exact_route variable is used to return the root route for the template. When set to false will return the absolute route
        if($url=="widget"):
            return "widget/ajax";
        endif;
        if($url=="block"):
            return "widget/block";
        endif;
        if($url=="ajaxsubscription"):
            return "widget/subscription";
        endif;
        if($url=="ajaxblogfeedback"):
            return "widget/comment";
        endif;
        if($url=="ajaxcontact"):
            return "widget/contact";
        endif;
        if($url=="ajaxsearch"):
            return "widget/search";
        endif;
        if($url=="ajaxform"):
            return "forms/save-form";
        endif;
        
        
        if($url=="custom-form-widget"):
            return "widget/custom-form-widget";
        endif;
        if($url=="hook"):
            $uri = substr(Yii::$app->request->url,strlen(Yii::$app->homeUrl));
            $request = explode("/",$uri);
            if(count($request)==0):
                return "hook/index";
            else:
                return "hook/".$request[1];
            endif;
        endif;
        
        if($url=="api"):
            $uri = substr(Yii::$app->request->url,strlen(Yii::$app->homeUrl));
            $request = explode("/",$uri);
            if(count($request)==0):
                return "api/index";
            else:
                return "api/".$request[1];
            endif;
        endif;
        
        $records = Pages::findOne(['url'=>$url]);
        if($records!=null){
            $template= $records->template;
        }else{
            //since its not a page, could it be a tag route?
            $records = Tags::findOne(['url'=>$url]);
            if($records!=null):
                    return "tags/index";
                else:
                    $records = Templates::findOne(['url'=>$url]);
                    if($records!=null):
                        return $records->route;
                    endif;
                //what if its a form details page, then we return the same set of widgets as the view parent
                  $form = FormSubmit::findOne(['url'=>$url]);
                  if($form!=null):
                    $page = Pages::findOne(['form_id'=>$form->form_id]);
                    ContentBuilder::getTemplateRouteByURL($page['url']);
                  endif;
                  
                //what if it is a theme URL transform we can check here                
                /*$url_obj=CustomSettings::find()->where(['setting_value'=>$url])->one();
                if($url_obj!=null):
                    $records = Pages::findOne(['url'=>"{yumpee_setting}".$url_obj['setting_name']."{/yumpee_setting}"]);
                    if($records!=null):
                        $template= $records->template;
                        
                    else:
                        return "blog/details";                        
                    endif;
                else:
                    return "blog/details";
                endif;*/
                    
            return "blog/details";
            endif;
        }
        $records = Templates::findOne(['id'=>$template]);
        
        if($records!=null){
            if(empty($records->parent_id)):
                return $records->route;
            else:
                if($exact_route==false):
                    return $records->route;
                endif;
                $record_parent = Templates::find()->where(['id'=>$records->parent_id])->one();
                return $record_parent->route;
            endif;
        }else{
            
            //we can check if the template id is for a child template
            return "";
        }
    }
    public static function getRoleHomePage(){
            $role_obj = Roles::find()->where(['id'=>Yii::$app->user->identity->role_id])->one();
            if($role_obj!=null):                
                $role_home_page = Pages::find()->where(['id'=>$role_obj['home_page']])->one();
                if($role_home_page!=null):
                    $role_home=ContentBuilder::getSetting("home_url")."/".$role_home_page['url'];   
                    return $role_home;
                endif;
            endif;
            return ContentBuilder::getSetting("home_url");
    }
    public static function getActionURL($url,$index=0){
            $url_arr = explode("/",$url);
            $url_length = count($url_arr);
            if($index > 0):
                $url = $url_arr[$url_length-$index];
            else:
                $url = $url_arr[$url_length-1];
            endif;
            //if $url="" can we check to see if it is a redirect  to home page setting
            if($url==""):
                $url=str_replace("/","",Yii::$app->request->getScriptUrl());
            endif;
            return $url;
    }    
    public static function getImage($id,$dimensions=""){
        $image_obj = Media::find()->where(['id'=>$id])->one();
        $width_dimension="360";
        $height_dimension="200";
        if($dimensions=="details"){
           $width_dimension="848";
           $height_dimension="350";
        }
        if($dimensions=="logo"):
            $width_dimension="100%";
            $height_dimension="100%";
        endif;
        if($dimensions=="thumbnail"):
            $width_dimension="20";
            $height_dimension="20";
        endif;
        if($image_obj!=null):
            $image_path = $image_obj->path;
                return "<img width='".$width_dimension."' height='".$height_dimension."' src=\"".Yii::getAlias("@image_dir")."/".$image_path."\" alt=\"".$image_obj->alt_tag."\" caption=\"".$image_obj->caption."\"></img>";
        else:
            return "";
        endif;
    }
    public static function getSetting($setting_name,$theme_id=0){
        if($setting_name=="yumpee_role_home_page"):
            return ContentBuilder::getRoleHomePage();
        endif;
        
        if(substr($setting_name,0,1)=="~"):
            $setting_name=substr($setting_name,1);
                        if($setting_name=="*"):
                                if($theme_id!=0):
                                        return CustomSettings::find()->where(['theme_id'=>$theme_id])->all();
                                else:
                                        return CustomSettings::find()->where(['theme_id'=>ContentBuilder::getSetting("current_theme")])->all();
                                endif;
                        endif;
			if($theme_id!=0):
				$setting = CustomSettings::find()->where(['setting_name'=>$setting_name])->andWhere('theme_id="'.$theme_id.'"')->one();
			else:
				$setting = CustomSettings::find()->where(['setting_name'=>$setting_name])->andWhere('theme_id="'.ContentBuilder::getSetting("current_theme").'"')->one();
			endif;
            if($setting!=null):
                if($setting_name=="website_home_page"):
                    $page = Pages::find()->where(['url'=>$setting->setting_value])->one();
                    if($page!=null):
                        return $page->id;
                    endif;
                endif;
                return $setting->setting_value;
            endif;
		
        endif;
               
        
        $setting = Settings::find()->where(['setting_name'=>$setting_name])->one();
        if($setting!=null):      
            if($setting_name=="website_home_page"):
                $page = Pages::find()->where(['url'=>$setting->setting_value])->one();
                if($page!=null):
                    return $page->id;
                endif;
            endif;
            
            return $setting->setting_value;  
        endif;
        
        if($setting_name=="*"):
            if($theme_id!=0):
                return CustomSettings::find()->where(['theme_id'=>$theme_id])->all();
            else:
                return CustomSettings::find()->all();
            endif;
        endif;
        
        $setting = CustomSettings::find()->where(['setting_name'=>$setting_name])->one();
        if($setting!=null):
            if($setting_name=="website_home_page"):
                $page = Pages::find()->where(['url'=>$setting->setting_value])->one();
                if($page!=null):
                    return $page->id;
                endif;
            endif;            
            return $setting->setting_value;
        else:
            return "";
        endif;
    }
			
	
public function getWidgets($position){
    $route = $this->getTemplateRouteByURL($this->getActionURL(Yii::$app->request->getAbsoluteUrl()),false);
    if($route==""):
        $route="blog/details";
    endif;
    $route_record = Templates::findOne(['route'=>$route]);
    return Templates::getMyWidgets($route_record->id,$position);
}
public function getCustomWidgets(){
    $route = $this->getTemplateRouteByURL($this->getActionURL(Yii::$app->request->getAbsoluteUrl()),false);
    if($route==""):
        $route="blog/details";
    endif;
    $route_record = Templates::findOne(['route'=>$route]);
    return Templates::getMyWidgets($route_record->id);
}
public function getBlocks(){
    //this is used to fetch the blocks that pertain to this page
    $url = $this->getActionURL(Yii::$app->request->getAbsoluteUrl());
    $page_object = Pages::find()->where(['url'=>$url])->one();
    return $page_object;
}	

public static function getBreadCrumbTrail($page){
    $breadcrumb=array();
    $item=array();
    $home_page = ContentBuilder::getSetting("home_url");
    $page_found=0;
    for($i=0;$i < 5;$i++){
        $page_obj = Pages::find()->where(['url'=>$page])->one();
        if($page_obj!=null):
            if($page_obj['parent_id']!=null && $page_obj['parent_id']!=""):
                $p = Pages::find()->where(['id'=>$page_obj['parent_id']])->one();
                $page = $p['url'];
                $item['title'] = $p['menu_title'];
                $item['link']=$home_page."/".$p['url'];        
                array_push($breadcrumb,$item);
                $page_found=1;
            endif;
        else:
            break;
        endif;        
    }
    if($i >0):
        return $breadcrumb;
    endif;
    $article_found=0;
    $page_obj=\frontend\models\Articles::find()->where(['url'=>$page])->one();
    if($page_obj!=null):        
        foreach($page_obj->blogIndex as $index):
			$item['title'] = $index->page->title;
                        $item['link'] = $home_page."/".$index->page->url;
                        array_push($breadcrumb,$item);              
			break;
	endforeach;
        return $breadcrumb;
    endif;
    $frm_obj=\frontend\models\FormSubmit::find()->where(['url'=>$page])->one();
    $page="";
    if($frm_obj!=null):
    for($i=0;$i < 5;$i++){
        if($page==""):
            $page_obj = Pages::find()->where(['form_id'=>$frm_obj['form_id']])->one();
        else:
            $page_obj = Pages::find()->where(['url'=>$page])->one();
        endif;
        if($page_obj!=null):
            if($page_obj['parent_id']!=null && $page_obj['parent_id']!=""):
                $p = Pages::find()->where(['id'=>$page_obj['parent_id']])->one();
                $page = $p['url'];
                $item['title'] = $p['menu_title'];
                $item['link']=$home_page."/".$p['url'];        
                array_push($breadcrumb,$item);
            endif;
        endif;        
    }
    endif;
   return $breadcrumb;
}

public static function getVersion(){
    return ContentBuilder::APPLICATION_VERSION;
}

public static function getMenus(){
    
    //we need to ensure that registration and login menus do not show when the user is logged in
        if(ContentBuilder::getSetting("allow_multiple_domains")=="Yes"):
            $install_domain = ContentBuilder::getSetting("home_url");
            $curr_domain = Yii::$app->request->hostInfo;
            $menu_profile=0;
            if(strpos($install_domain, $curr_domain)===false):
                $sub_domain = Domains::find()->where(['domain_url'=>$curr_domain])->one();
                if($sub_domain!=null):
                    $menu_profile = $sub_domain->menu_id;
                endif;
            endif;    
            
            if($menu_profile > 0):
                $page_arr = MenuPage::find()->select('menu_id')->where(['profile'=>$menu_profile])->column();
                if (Yii::$app->user->isGuest) :
                    $header_menus = Pages::find()->where(['IN','id',$page_arr])->andWhere('require_login<>"Y"')->orderBy('sort_order')->all();
                    $footer_menus = Pages::find()->where(['show_in_footer_menu'=>'1'])->andWhere('menu_profile="'.$menu_profile.'"')->andWhere('require_login<>"Y"')->orderBy('sort_order')->all();
                else:
                    $header_menus = Pages::find()->where(['IN','id',$page_arr])->andWhere('hideon_login<>"Y"')->orderBy('sort_order')->all();
                    $footer_menus = Pages::find()->where(['show_in_footer_menu'=>'1'])->andWhere('menu_profile="'.$menu_profile.'"')->andWhere('hideon_login<>"Y"')->orderBy('sort_order')->all();
                endif;
                $return["header_menus"] = $header_menus;
                $return["footer_menus"] = $footer_menus;
                return $return;
            
            endif;
        endif;
        
        
        
        
        if (Yii::$app->user->isGuest) :
            $header_menus = Pages::find()->where(['show_in_menu'=>'1'])->andWhere('require_login<>"Y"')->orderBy('sort_order')->all();
            $footer_menus = Pages::find()->where(['show_in_footer_menu'=>'1'])->andWhere('require_login<>"Y"')->orderBy('sort_order')->all();
        else:
            //logged in user has a role
            $role = Yii::$app->user->identity->role_id;
            //logged role has a frontend profile
            
            if($role!=null):
                $menu_rec = Roles::find()->where(['id'=>$role])->one();
                $menu_id = $menu_rec['menu_id'];
                
                //get pages in this frontend profile
                $page_arr = MenuPage::find()->select('menu_id')->asArray()->where(['profile'=>$menu_id])->column();
                if($page_arr!=null):                    
                    $header_menus = Pages::find()->where(['IN','id',$page_arr])->andWhere('hideon_login<>"Y"')->orderBy('sort_order')->all();
                    $footer_menus = Pages::find()->where(['show_in_footer_menu'=>'1'])->andWhere('hideon_login<>"Y"')->orderBy('sort_order')->all();
                    $return["header_menus"] = $header_menus;
                    $return["footer_menus"] = $footer_menus;
                    return $return;
                endif;
            endif;
            $header_menus = Pages::find()->where(['show_in_menu'=>'1'])->andWhere('hideon_login<>"Y"')->orderBy('sort_order')->all();
            $footer_menus = Pages::find()->where(['show_in_footer_menu'=>'1'])->andWhere('hideon_login<>"Y"')->orderBy('sort_order')->all();
        endif;
        $return["header_menus"] = $header_menus;
        $return["footer_menus"] = $footer_menus;
        return $return;
}
}

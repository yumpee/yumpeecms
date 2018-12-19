<?php
//this will build the site contents based on the URL and put everything into an array format to be navigated through mustache

namespace frontend\components;

use Yii;
use backend\models\Pages;
use frontend\models\Templates;
use backend\models\Media;
use backend\models\Settings;
use backend\models\CustomSettings;
use backend\models\Tags;
use backend\models\Themes;
use backend\models\Articles;
use backend\models\ArticlesCategories;
use backend\models\ClassElementAttributes;
class ContentBuilder {
    
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
        if(count($records) > 0){
            $template =  $records->id;
        }else{
            return "";
        }
        $sql = "SELECT url FROM tbl_page WHERE template='".$template."'";
        $records = Pages::findBySql($sql)->one();
        if(count($records) > 0){
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
    public static function getSetting($setting_name){
        $setting = Settings::find()->where(['setting_name'=>$setting_name])->one();
        if($setting!=null):
            return $setting->setting_value;
        endif;
        $setting = CustomSettings::find()->where(['setting_name'=>$setting_name])->one();
        if($setting!=null):
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

	
}

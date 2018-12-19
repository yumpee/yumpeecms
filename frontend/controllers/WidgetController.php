<?php
/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use frontend\components\ContentBuilder;
use frontend\components\ThemeManager;
use frontend\components\SocialShare;
use frontend\models\Twig;
use backend\models\Articles;
use backend\models\ArticlesCategories;
use frontend\models\Pages;
use backend\models\Users;
use backend\models\Menus;
use backend\models\Testimonials;
use backend\models\Tags;
use backend\models\Subscriptions;
use backend\models\Comments;
use backend\models\Settings;
use frontend\models\Templates;
use backend\models\TemplateWidget;
use backend\models\Slider;
use frontend\models\Blocks;
use backend\models\Forms;
use frontend\models\FormSubmit;
use frontend\models\FormData;
use backend\models\CustomWidget;
use backend\models\RatingProfile;
use backend\models\Widgets;
use backend\models\Gallery;
use backend\models\GalleryImage;
use backend\models\Language;
use yii\db\Expression;

class WidgetController extends Controller{
    public function actionAjax(){
        $page=[];
        
        $page['baseURL'] = Yii::$app->request->getBaseUrl();
        //we check initially to be sure it is not a custom widget and if it isn't we then assign custom widget
        $widget = Widgets::find()->where(['short_name'=>Yii::$app->request->get('widget')])->one();
        
        if($widget->parent_id=='0' || $widget->parent_id==NULL):
            $called_widget = Yii::$app->request->get('widget');
            $cw = Yii::$app->request->get('widget');
        else:
            $b = Widgets::find()->where(['id'=>$widget->parent_id])->one();
            $cw = $b->short_name;
            $called_widget = Yii::$app->request->get('widget');
        endif;
        
        switch ($cw){
            //a listing of category types
            case 'widget_category':
                if(Yii::$app->request->get('limit')!=null): //assuming its a call from the web page
                        $limit = Yii::$app->request->get('limit');
                        $page['category_object'] = ArticlesCategories::find()->where(['published'=>'1'])->limit($limit)->all();
                        $page['title']="";
                else:
                        $page['category_object'] = ArticlesCategories::find()->where(['published'=>'1'])->all();
                        $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'),false);
                        $route_id = Templates::find()->where(['route'=>$route])->one();
                        $record = TemplateWidget::find()->where(['widget'=>$called_widget])->andWhere(['page_id'=>$route_id['id']])->one();
                        $settings = json_decode($record->settings);
                        $page['title']=$settings->widget_title;
                endif;
            break;
            
            //testimonials widget
            case 'widget_testimonials':
                if(Yii::$app->request->get('limit')!=null): //assuming its a call from the web page
                        $limit = Yii::$app->request->get('limit');
                        $page['testimonials'] = Testimonials::find()->limit($limit)->orderBy(['id'=>SORT_DESC])->one();
                        $page['title']="";
                else:
                    $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'),false);
                    $route_id = Templates::find()->where(['route'=>$route])->one();
                    $record = TemplateWidget::find()->where(['widget'=>$called_widget])->andWhere(['page_id'=>$route_id['id']])->one();
                    $settings = json_decode($record->settings);                
                    $page['testimonials'] = Testimonials::find()->limit($settings->widget_limit)->orderBy(['id'=>SORT_DESC])->one();
                    $page['title'] = $settings->widget_title;
                endif;
                
            break;
            
            case 'widget_language':
                if(Yii::$app->request->get('limit')!=null): 
                    $page['language'] = Language::find()->orderBy('name')->all();
                else:
                    $page['language'] = Language::find()->orderBy('name')->all();
                endif;
            break;
            
            case 'widget_gallery':
                if(Yii::$app->request->get('limit')!=null): //assuming its a call from the web page
                        $limit = Yii::$app->request->get('limit');
                        $id = Gallery::find()->where(['name'=>$limit])->one();
                        $page['gallery'] = GalleryImage::find()->where(['gallery_id'=>$id['id']])->orderBy(['id'=>SORT_DESC])->all();
                        $page['title']="";
                else:
                    $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'),false);
                    $route_id = Templates::find()->where(['route'=>$route])->one();
                    $record = TemplateWidget::find()->where(['widget'=>$called_widget])->andWhere(['page_id'=>$route_id['id']])->one();
                    $settings = json_decode($record->settings);     
                    $id = Gallery::find()->where(['name'=>$settings->widget_gallery_name])->one();
                    $page['gallery'] = GalleryImage::find()->where(['gallery_id'=>$id['id']])->limit($settings->widget_limit)->orderBy(['id'=>SORT_DESC])->all();
                    $page['title'] = $settings->widget_title;
                endif;
                
            break;
            case 'widget_recent_article':
                if(Yii::$app->request->get('limit')!=null): //assuming its a call from the web page
                        $limit = Yii::$app->request->get('limit');
                        $page['articles'] = Articles::find()->where(['published'=>'1'])->limit($limit)->orderBy(['updated'=>SORT_DESC])->all();
                        $page['title']="";
                    else:
                        $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'),false);
                        $route_id = Templates::find()->where(['route'=>$route])->one();
                        $record = TemplateWidget::find()->where(['widget'=>$called_widget])->andWhere(['page_id'=>$route_id['id']])->one();
                        $settings = json_decode($record->settings);
                        $page['articles'] = Articles::find()->where(['<>','url',Yii::$app->request->get('page_id')])->andWhere(['published'=>'1'])->limit($settings->widget_limit)->all();
                        $page['title'] = $settings->widget_title;
                endif;
            break;            
        
            case 'widget_feature_page':                
                if(Yii::$app->request->get('limit')!=null): //assuming its a call from the web page
                        $limit = Yii::$app->request->get('limit');
                        $page['articles'] = Articles::find()->where(['published'=>'1'])->limit($limit)->orderBy(['updated'=>SORT_DESC])->all();
                        $page['title']="";
                    else:
                        $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'),false);
                        $route_id = Templates::find()->where(['route'=>$route])->one();
                        $record = TemplateWidget::find()->where(['widget'=>$called_widget])->andWhere(['page_id'=>$route_id['id']])->one();
                        $settings = json_decode($record->settings);
                        $page['articles'] = Articles::find()->where(['<>','url',Yii::$app->request->get('page_id')])->andWhere(['published'=>'1'])->limit($settings->widget_limit)->all();
                        $page['title'] = $settings->widget_title;
                endif;
            break;
            
            case 'widget_blog_article':
                $query = Articles::find();
                
                if(Yii::$app->request->get('limit')!=null): //assuming its a call from the web page
                        $limit = Yii::$app->request->get('limit');
                        $query->where(['published'=>'1'])->limit($limit)->orderBy(['updated'=>SORT_DESC]);
                        //call the filter method to help add the filter options
                        if(Yii::$app->request->get('filter')!=null && Yii::$app->request->get('filter')!="0"):
                            $query = ContentBuilder::ArticleFilter($query,Yii::$app->request->get('filter')); 
                        endif;
                        $page['records'] = $query->all();
                        $page['title'] = "";
                    else:
                        $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'),false);
                        $route_id = Templates::find()->where(['route'=>$route])->one();
                        $record = TemplateWidget::find()->where(['widget'=>$called_widget])->andWhere(['page_id'=>$route_id['id']])->one();
                        $settings = json_decode($record->settings);
                        $page['records'] = $query->where(['published'=>'1'])->limit($settings->widget_limit)->orderBy(['updated'=>SORT_DESC])->all();
                        $page['title'] = $settings->widget_title;
                endif;                
            break;
            
            case 'widget_articles':
                if(Yii::$app->request->get('limit')!=null): //assuming its a call from the web page
                        $limit = Yii::$app->request->get('limit');
                        $page['articles'] = Articles::find()->where(['published'=>'1'])->limit($limit)->orderBy(new Expression('rand()'))->all();
                        $page['title']="";
                    else:
                        $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'),false);
                        $route_id = Templates::find()->where(['route'=>$route])->one();
                        $record = TemplateWidget::find()->where(['widget'=>$called_widget])->andWhere(['page_id'=>$route_id['id']])->one();
                        $settings = json_decode($record->settings);
                        $page['articles'] = Articles::find()->where(['<>','url',Yii::$app->request->get('page_id')])->andWhere(['published'=>'1'])->limit($settings->widget_limit)->all();
                        $page['title'] = $settings->widget_title;
                endif;
            break;
            
            case 'widget_tag_cloud':
                if(Yii::$app->request->get('limit')!=null): //assuming its a call from the web page
                        $limit = Yii::$app->request->get('limit');
                        $page['tag_object'] = Tags::find()->limit($limit)->orderBy('name')->all();
                        $page['title']="";
                else:
                        $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'),false);
                        $route_id = Templates::find()->where(['route'=>$route])->one();
                        $record = TemplateWidget::find()->where(['widget'=>$called_widget])->andWhere(['page_id'=>$route_id['id']])->one();
                        $settings = json_decode($record->settings);
                        $page['tag_object'] = Tags::find()->limit($settings->widget_limit)->orderBy('name')->all();
                        $page['title'] = $settings->widget_title;
                endif;
            break;
        
            case 'widget_archives':
                    $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'),false);
                    $route_id = Templates::find()->where(['route'=>$route])->one();
                    $record = TemplateWidget::find()->where(['widget'=>$called_widget])->andWhere(['page_id'=>$route_id['id']])->one();
                    $settings = json_decode($record->settings);
                    $page['archive_object'] = Articles::find()->select('archive')->distinct()->limit('5')->all();
                    $page['title'] = $settings->widget_title;
            break;
            
            case 'widget_video':
                if(Yii::$app->request->get('video_url')!=null): //assuming its a call from the web page
                        $page['video_url'] = Yii::$app->request->get('video_url');
                        $page['video_width']=Yii::$app->request->get('video_width');
                        $page['video_height']=Yii::$app->request->get('video_height');                        
                        $page['title']="";
                else:
                        $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'),false);
                        $route_id = Templates::find()->where(['route'=>$route])->one();
                        $record = TemplateWidget::find()->where(['widget'=>$called_widget])->andWhere(['page_id'=>$route_id['id']])->one();
                        $settings = json_decode($record->settings);
                        $page['video_url'] = $settings->widget_url;
                        $page['video_width']=$settings->widget_width;
                        $page['video_height']=$settings->widget_height;
                        $page['title'] = $settings->widget_title;
                endif;
            break;
            case 'widget_audio':
                if(Yii::$app->request->get('audio_url')!=null): //assuming its a call from the web page
                        $page['audio_url'] = Yii::$app->request->get('audio_url');
                        $page['audio_width']=Yii::$app->request->get('audio_width');
                        $page['audio_height']=Yii::$app->request->get('audio_height');                        
                        $page['title']="";
                else:
                        $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'),false);
                        $route_id = Templates::find()->where(['route'=>$route])->one();
                        $record = TemplateWidget::find()->where(['widget'=>$called_widget])->andWhere(['page_id'=>$route_id['id']])->one();
                        $settings = json_decode($record->settings);
                        $page['audio_url'] = $settings->widget_url;
                        $page['audio_width']=$settings->widget_width;
                        $page['audio_height']=$settings->widget_height;
                        $page['title'] = $settings->widget_title;
                endif;
            break;
            case 'widget_youtube':
                if(Yii::$app->request->get('audio_url')!=null): //assuming its a call from the web page
                        $page['video_id'] = Yii::$app->request->get('video_id');
                        $page['video_width']=Yii::$app->request->get('video_width');
                        $page['video_height']=Yii::$app->request->get('video_height');                        
                        $page['title']="";
                else:
                        $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'),false);
                        $route_id = Templates::find()->where(['route'=>$route])->one();
                        $record = TemplateWidget::find()->where(['widget'=>$called_widget])->andWhere(['page_id'=>$route_id['id']])->one();
                        $settings = json_decode($record->settings);
                        $page['video_id'] = $settings->widget_url;
                        $page['video_width']=$settings->widget_width;
                        $page['video_height']=$settings->widget_height;
                        $page['title'] = $settings->widget_title;
                endif;
            break;
            case 'widget_contact':
                $page['contact_object'] = Settings::find()->where(['setting_name'=>'contact_us_address'])->one();
                $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'));
                    $route_id = Templates::find()->where(['route'=>$route])->one();
                    $record = TemplateWidget::find()->where(['widget'=>$called_widget])->andWhere(['page_id'=>$route_id['id']])->one();
                    $settings = json_decode($record->settings);
                $page['title']=$settings->widget_title;
            break;
            case 'widget_subscription':
                $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'),false);
                $route_id = Templates::find()->where(['route'=>$route])->one();
                $record = TemplateWidget::find()->where(['widget'=>'widget_subscription'])->andWhere(['page_id'=>$route_id['id']])->one();
                $settings = json_decode($record->settings);
                $page['title'] = $settings->widget_title;
            break;
        
            case 'widget_html':
                if(Yii::$app->request->get('limit')!=null): //assuming its a call from the web page
                        $limit = Yii::$app->request->get('limit');
                        $html = Blocks::find()->where(['name'=>$limit])->one();
                        $page['contents'] = $html['content'];
                        $page['title']="";
                else:
                    $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'),false);
                    $route_id = Templates::find()->where(['route'=>$route])->one();
                    $record = TemplateWidget::find()->where(['widget'=>$called_widget])->andWhere(['page_id'=>$route_id['id']])->one();
                    $settings = json_decode($record->settings);
                    if($settings!=null):
                        $html = Blocks::find()->where(['id'=>$settings->widget_limit])->one();
                        $page['contents'] = $html['content'];
                        $page['title'] = $settings->widget_title;
                    else:
                        return "";
                    endif;
                endif;
            break;
            
            case 'widget_form':
                    $form = Forms::find()->where(['name'=>Yii::$app->request->get('limit')])->one();
                    $pages = ArticlesCategories::getMyEventsCategories();
                    $page_map =  yii\helpers\ArrayHelper::map($pages, 'id', 'name');
                    $page_arr="";
                    $metadata['category'] = \yii\helpers\Html::checkboxList("category",$page_arr,$page_map);
                    $pages = Pages::getBlogIndex();
                    $index_arr="";
                    $page_map =  yii\helpers\ArrayHelper::map($pages, 'id', 'title');
                    $metadata['blog_index'] = \yii\helpers\Html::checkboxList("blog_index",$index_arr,$page_map);
                    $metadata['param'] = Yii::$app->request->csrfParam;
                    $metadata['token'] = Yii::$app->request->csrfToken;
                    switch($form['form_type']):
                        case "form-article":
                            $article = new Pages();  
                            if((Yii::$app->user->isGuest)):
                                    $metadata['rs']= new Users();
                                else:
                                    $metadata['rs'] = Users::find()->where(['username'=>Yii::$app->user->identity->username])->one();
                            endif;
                            $metadata['submit_url'] = Yii::$app->request->getBaseUrl()."/".$article['url'];
                            $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');
                        break;
                        case "form-profile":
                            $article = new Pages();                              
                            if((Yii::$app->user->isGuest)):
                                    $metadata['rs']= new Users();
                                else:
                                    $metadata['rs'] = Users::find()->where(['username'=>Yii::$app->user->identity->username])->one();
                            endif;
                            $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');
                        break;
                        case "form-feedback":
                            $article = new Pages();                            
                            if((Yii::$app->user->isGuest)):
                                    $metadata['rs']= new Users();
                                else:
                                    $metadata['rs'] = Users::find()->where(['username'=>Yii::$app->user->identity->username])->one();
                            endif;
                            $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');
                            //if its a form feedback and contains a feedback type and target ID, we include it here
                            if(Yii::$app->request->get('filter')!=null && Yii::$app->request->get('filter')!="0"):
                                $filter_list = explode("|",$filter);
                                foreach($filter_list as $filter_type):
                                    list($label,$param)=explode("=",$filter_type);
                                    if(strtolower(trim($label))=="feedback_type"):
                                        $metadata['feedback_type']=$param;
                                    endif;
                                    if(strtolower(trim($label))=="target_id"):
                                        $metadata['target_id']=$param;
                                    endif;
                                endforeach;
                            endif;
                            
                        break;
                        case "form-twig":
                            $article = new Pages();                            
                            if((Yii::$app->user->isGuest)):
                                    $metadata['rs']= new Users();
                                else:
                                    $metadata['rs'] = Users::find()->where(['username'=>Yii::$app->user->identity->username])->one();
                            endif;
                            $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');
                        break;
                    endswitch;
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$form->id,'renderer_type'=>'F'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'], ['form'=>$form,'page'=>$article,'metadata'=>$metadata]);
                            return $this->renderPartial('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
                return $this->renderPartial('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/forms/'.$form['form_type'],['form'=>$form,'page'=>$article,'metadata'=>$metadata],false,false);
            break;
            
            case 'widget_menu':
                $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'),false);
                $route_id = Templates::find()->where(['route'=>$route])->one();
                $record = TemplateWidget::find()->where(['widget'=>$called_widget])->andWhere(['page_id'=>$route_id['id']])->one();
                $settings = json_decode($record->settings);
                if($settings!=null):
                    $page['header_menus'] = Menus::getProfileMenus($settings->widget_limit);
                    $page['header_baseURL'] = $header_baseURL = Yii::$app->request->getBaseUrl();
                else:
                    return "";
                endif;
            break;
        
            case 'widget_social':
                if(Yii::$app->request->get('limit')!=null): //assuming its a call from the web page
                    
                        $limit = Yii::$app->request->get('limit');
                        $html = Blocks::find()->where(['name'=>$limit])->one();
                        $page['contents'] = $html['content'];
                        $page['title']="";
                else:
                    $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'),false);
                    $route_id = Templates::find()->where(['route'=>$route])->one();
                    $record = TemplateWidget::find()->where(['widget'=>$called_widget])->andWhere(['page_id'=>$route_id['id']])->one();
                    $settings = json_decode($record->settings);
                    $networks=[];
                    if($settings!=null):
                        if(!empty($settings->facebook)):
                            array_push($networks,"facebook");
                        endif;
                        if(!empty($settings->twitter)):
                            array_push($networks,"twitter");
                        endif;
                        if(!empty($settings->googleplus)):
                            array_push($networks,"googleplus");
                        endif;
                        if(!empty($settings->linkedin)):
                            array_push($networks,"linkedin");
                        endif;
                    endif;
                endif;
                $page['social_link'] = SocialShare::widget([
                'style'=>'horizontal',
                'networks' => $networks,
                'data_via'=>'', //twitter username (for twitter only, if exists else leave empty)
                ]);
            break;
        
            case 'widget_comment':
                $url = Yii::$app->request->get('page_id');
                $page['url'] = $url;				
                $route = ContentBuilder::getTemplateRouteByURL(Yii::$app->request->get('page_id'),false);
                $route_id = Templates::find()->where(['route'=>$route])->one();
                $record = TemplateWidget::find()->where(['widget'=>'widget_comment'])->andWhere(['page_id'=>$route_id['id']])->one();
                $settings = json_decode($record->settings);
                $page['article_object'] = Articles::find()->where(['url'=>$page['url']])->andWhere('disable_comments<>"Y"')->one();
                if($page['article_object']==null):
                    return "";
					
					//$page['article_object'] = new Articles();
                endif;
                $page['title'] = $settings->widget_title;
            break;
        
            case 'widget_slider':
                $url = Yii::$app->request->get('limit');
                $page['slider_object'] = Slider::find()->where(['id'=>$url])->one();
                if($page['slider_object']==null):
                    $page['slider_object'] = Slider::find()->where(['name'=>$url])->one();
                endif;
            break;
            ///////////////////////////////////////Process rating Widgets Here/////////////////////////////////////////////////////////
            case 'widget_rating':
                $page=[];
                $page['rating_method']="set";
                    $page_url =  Templates::find()->where(['route'=>'tags/rating'])->one();
                    $page['rating_submit_url'] = Yii::$app->request->getBaseUrl()."/".$page_url['url'];
                    $page['param'] = Yii::$app->request->csrfParam;
                    $page['token'] = Yii::$app->request->csrfToken;
                   
                    $page['rating_value']="0";
                    //we now set the rating type here - do we want to set a rating or are we interested in getting overall rating value?
                    if(Yii::$app->request->get('filter')!=null && Yii::$app->request->get('filter')!="0"):
                            $filter_list = explode("|",Yii::$app->request->get('filter'));
                            foreach($filter_list as $filter_type):
                                    list($label,$param)=explode("=",$filter_type);
                                    if(strtolower(trim($label))=="rating_method"):
                                        $page['rating_method']=$param;                                        
                                    endif;
                                    if(strtolower(trim($label))=="rating_val"):
                                       $page['rating_value']=$param; 
                                    endif;
                            endforeach;
                    endif;
                    if((Yii::$app->user->isGuest) && $page['rating_method']=="set"):
                            return "";
                    endif;
                    if(Yii::$app->request->get('limit')==null):
                        //if the rating_name is not specified, then choose the first one that is in use
                            $page['rating_profile'] = RatingProfile::find()->one(); //consider using a Settings to define default rating profile
                        else:
                            $page['rating_profile'] = RatingProfile::find()->where(['name'=>Yii::$app->request->get('limit')])->one();
                    endif;
                    
                    $article = Articles::find()->where(['url'=>Yii::$app->request->get('page_id')])->one(); //this should become set to what type of class we are rating
                    $page['rating_article_id'] = $article['id'];
                    if($page['rating_value']==null):
                        $page['rating_value'] = $article['rating'];
                    endif;
            break;
            /////////////////////////////////////////////END OF RATING WIDGETS /////////////////////////////////////////////////////////
            case 'widget_login':
                    if (!Yii::$app->user->isGuest) {
                        return "";
                    } 
                    $page=[];
                    $page_url =  ContentBuilder::getURLByRoute("accounts/login");
                    $article = Pages::find()->where(['url'=>$page_url])->one();   
                    $page['login_url'] = Yii::$app->request->getBaseUrl()."/".$article['url'];
                    $page['message']="";
                    $page['callback']="";
                    $page['param'] = Yii::$app->request->csrfParam;
                    $page['token'] = Yii::$app->request->csrfToken;
            break;           
            
            
        }
        if(ContentBuilder::getSetting("twig_template")=="Yes"):
            //we handle the loading of twig template if it is turned on
            $theme_id = ContentBuilder::getSetting("current_theme");
            $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>Yii::$app->request->get('widget'),'renderer_type'=>'W'])->one();
            if($codebase==null):
                $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>Yii::$app->request->get('widget'),'renderer_type'=>'I'])->one();
            endif;
            if(($codebase!=null)&& ($codebase['code']<>"")):
                $loader = new Twig();
                $twig = new \Twig_Environment($loader);
                $page['app'] = Yii::$app;
                return $twig->render($codebase['filename'],$page);                 
            endif;
        endif;
		$page['settings'] = new Settings();
        return $this->renderPartial(ThemeManager::getWidget('@frontend/themes/'.ContentBuilder::getThemeFolder().'/widgets/'.$called_widget,$called_widget),$page);
        
    } 
    public function actionSubscription(){
        
        $model = Subscriptions::find()->where(['email'=>Yii::$app->request->get("email")])->one();
            if($model!=null):
                return "This email has been previously registered";
            else:
                $subscriptions =  new Subscriptions();
                $subscriptions->setAttribute('name',Html::encode(Yii::$app->request->get("name","")));
                $subscriptions->setAttribute('email',Html::encode(Yii::$app->request->get("email")));
                $subscriptions->save();
                return "Your subscription request has been successfully registered";
            endif;
    }
    public function actionComment(){
        $url = ContentBuilder::getActionURL(Yii::$app->request->getReferrer());
        $target = Articles::find()->where(['url'=>$url])->one();
        $comment = new Comments();
        $comment->setAttribute('target_id',$target->id);
        $comment->setAttribute('comment_type','article');
        if(Yii::$app->user->identity!=null):
            $comment->setAttribute('author',Yii::$app->user->identity->username);
        endif;
        
        $comment->setAttribute('commentor',Html::encode(Yii::$app->request->get("name")));
        $comment->setAttribute('comment',Html::encode(Yii::$app->request->get("comments")));
        $comment->setAttribute('date_commented',date("Y-m-d H:i:s"));
        
        if(ContentBuilder::getSetting("auto_approve_comments")=="on"):
            $comment->setAttribute('status','Y');
        else:
            $comment->setAttribute('status','N');
        endif;
        $comment->setAttribute('ip_address',Yii::$app->request->getUserIP());
        $comment->setAttribute('email',Html::encode(Yii::$app->request->get("email")));
        $comment->setAttribute('website',Html::encode(Yii::$app->request->get("website")));
        $comment->save();
        return "Your comment has been successfully submitted";
        
    }
    public function actionContact(){
        //send to email here
        
        return "Thanks for contacting us. Someone will contact you shortly";
    }
    public function actionSearch(){
        $search_obj = Templates::find()->where(['route'=>'tags/search'])->one();
        return $search_obj->url;
    }
    public function actionBlock(){
        $block_ref = Yii::$app->request->get('id');
        echo html_entity_decode(Blocks::find()->where(['name'=>$block_ref])->one()->content);
        
    }
    public function actionCustomFormWidget(){
        //this method is used to handle custom widget calls
        $page['baseURL'] = Yii::$app->request->getBaseUrl();
        $widget =  CustomWidget::find()->where(['name'=>Yii::$app->request->get('widget')])->one();
        //we process the filters here before sending back
        //let's handle some filter here just incase we are filtering based on some defined field parameters
            if(Yii::$app->request->get('filter')!=null && Yii::$app->request->get('filter')!="0"):
                            $subQuery = FormData::find()->select('form_submit_id')->where(['<>','form_submit_id','0']);
                            $filter_list = explode("|",Yii::$app->request->get('filter'));
                            foreach($filter_list as $filter_type):
                                    list($param,$param_val)=explode("=",$filter_type);
                                    $subQuery->andWhere("`param`='".$param."'");
                                    $subQuery->andWhere("`param_val`='".$param_val."'");
                            endforeach;
                
                if(Yii::$app->request->get('limit')!=null): //assuming its a call from the web page
                        $limit = Yii::$app->request->get('limit');
                        $page['records'] = FormSubmit::find()->where(['form_id'=>$widget['form_id']])->andWhere(['IN','id',$subQuery])->limit($limit)->orderBy(['id'=>SORT_DESC])->all();
                else:
                        $page['records'] = FormSubmit::find()->where(['form_id'=>$widget['form_id']])->andWhere(['IN','id',$subquery])->orderBy(['id'=>SORT_DESC])->all();
                endif;
            else:
                if(Yii::$app->request->get('limit')!=null): //assuming its a call from the web page
                        $limit = Yii::$app->request->get('limit');
                        $page['records'] = FormSubmit::find()->where(['form_id'=>$widget['form_id']])->limit($limit)->orderBy(['id'=>SORT_DESC])->all();
                else:
                        $page['records'] = FormSubmit::find()->where(['form_id'=>$widget['form_id']])->orderBy(['id'=>SORT_DESC])->all();
                endif;
            endif;
        
        $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');
        $metadata['param'] = Yii::$app->request->csrfParam;
        $metadata['token'] = Yii::$app->request->csrfToken;
        $page['metadata'] = $metadata;
        
        if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>Yii::$app->request->get('widget'),'renderer_type'=>'I'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'],$page);
                            return $this->renderPartial('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
        endif;
        
    }
    
}

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

/**
 * Description of FormsController
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
use frontend\models\FeedbackFiles;
use frontend\models\WebHook;
use frontend\models\Domains;
use frontend\models\Themes;

use yii\helpers\FileHelper;
use yii\db\Expression;

class FormsController extends Controller{
    
	public static function allowedDomains()
{
	if(ContentBuilder::getSetting("allow_multiple_domains")=="Yes"):
		return Domains::find()->select('domain_url')->column();
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
    /*This class is used to display custom forms, its data and its search results    
     * actionDisplay method is used to render forms whether they be twig, profile, user profile and feedback forms
     * actionView is used to display the summary and details view of the forms
     * actionSearch is used to display search result data whether through a widget or a JSON format
     * actionSaveForm is used to save the information submitted to a form as well as the attached files associated with forms
     * */
     
    public function actionDisplay(){
       $page =[];
       $page_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
            if (strpos($page_url, '?') !== false):
                list($page_url,$search)= explode("?",$page_url);
            endif;
       $article = Pages::find()->where(['url'=>$page_url])->one();
       
       //if it requires log in and we are not logged in then redirect to login page
       if(($article['require_login']=="Y")&&(Yii::$app->user->isGuest)):       
            $page =[];
            $form['callback'] = $page_url;
            $page_url =  ContentBuilder::getURLByRoute("accounts/login");
            $article = Pages::find()->where(['url'=>$page_url])->one();          
            $form['login_url'] = Yii::$app->request->getBaseUrl()."/".$article['url'];
            $form['message'] = "You have to login to view this page";
            $form['param'] = Yii::$app->request->csrfParam;
            $form['token'] = Yii::$app->request->csrfToken;
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
							$themes = new Themes();
							$theme_id=$themes->dataTheme;
							if($theme_id=="0"):
								$theme_id = ContentBuilder::getSetting("current_theme");
							endif;
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>'accounts/login','renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'], ['form'=>$form,'page'=>$article,'app'=>Yii::$app]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
                         
                    return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/account/login',['form'=>$form,'page'=>$article]);
                    
       endif;
        
        if(ContentBuilder::getTemplateRouteByURL($page_url)=="forms/display"):
            
            $form = Forms::find()->where(['id'=>$article->form_id])->one();
            $pages = ArticlesCategories::getMyEventsCategories();
            $page_map =  yii\helpers\ArrayHelper::map($pages, 'id', 'name');
            $page_arr="";
            $metadata['category'] = \yii\helpers\Html::checkboxList("category",$page_arr,$page_map);
            $pages = Pages::getBlogIndex();
            $index_arr="";
            $page_map =  yii\helpers\ArrayHelper::map($pages, 'id', 'title');
            $metadata['blog_index'] = \yii\helpers\Html::checkboxList("blog_index",$index_arr,$page_map);
            $metadata['submit_url'] = Yii::$app->request->getBaseUrl()."/".$article['url'];
            $metadata['param'] = Yii::$app->request->csrfParam;
            $metadata['token'] = Yii::$app->request->csrfToken;
            switch($form['form_type']):
                case "form-article":
                    $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');
                    $metadata['form_type']="form-article";
                break;
                case "form-profile":
                    $metadata['rs'] = Users::find()->with('details')->where(['username'=>Yii::$app->user->identity->username])->one();
                    $metadata['details'] = ProfileDetails::find()->where(['profile_id'=>Yii::$app->user->identity->id])->all();
                    $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');                    
                break;
                case "form-feedback":
                    $metadata['rs'] = Users::find()->where(['username'=>Yii::$app->user->identity->username])->one();
                    $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');
                    
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
                    $article = Pages::find()->where(['url'=>$page_url])->one();    
                    $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');
                    $metadata['form_id'] = $article->form_id;
                    if(Yii::$app->request->get("id")!=null && !empty($search)): //incase the user is trying to edit a record
                        $metadata['rs'] = FormSubmit::find()->where(['usrname'=>Yii::$app->user->identity->username])->andWhere('id="'.Yii::$app->request->get("id").'"')->one();                        
                    endif;
                break;
            endswitch;
                    
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):                        
                        //we handle the loading of twig template if it is turned on
                        $themes = new Themes();
							$theme_id=$themes->dataTheme;
							if($theme_id=="0"):
								$theme_id = ContentBuilder::getSetting("current_theme");
							endif;
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$article->form_id,'renderer_type'=>'F'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'], ['form'=>$form,'page'=>$article,'metadata'=>$metadata,'app'=>Yii::$app]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
            return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/forms/'.$form['form_type'],['form'=>$form,'page'=>$article,'metadata'=>$metadata]);
        endif;
        
                        $error404 = ContentBuilder::getSetting("error_page");
                        $header_image="";
                            if($error404!=""):
                                $article = Pages::find()->where(['id'=>$error404])->one();
                                if($article==null):
                                    return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/standard/error',['page'=>$article,'header_image'=>$header_image]);
                                endif;
                            else:
                                return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/standard/error',['page'=>$article,'header_image'=>$header_image]);
                            endif;
    }
    
    public function actionView(){
        //this function is used to display forms that are created with the Form View Template. The form selected when setting this page up, is then used
        //to get information from the form submit and form data tables and passed to the respective renders of this method
       $page =[];
       $page_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
       if (strpos($page_url, '?') !== false):
          list($page_url,$search)= explode("?",$page_url);
       endif;
       $article = Pages::find()->where(['url'=>$page_url])->one();
       if($article==null):
           $parent_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl(),2);
           $article = Pages::find()->where(['url'=>$parent_url])->one();
       endif;
     
       //if it requires log in and we are not logged in then redirect to login page
       if(($article['require_login']=="Y")&&(Yii::$app->user->isGuest)):       
            $page =[];
            $form['callback'] = $page_url;
            $page_url =  ContentBuilder::getURLByRoute("accounts/login");
            $article = Pages::find()->where(['url'=>$page_url])->one(); 
            
            $form['login_url'] = Yii::$app->request->getBaseUrl()."/".$article['url'];
            $form['message'] = "You have to login to view this page";
            $form['param'] = Yii::$app->request->csrfParam;
            $form['token'] = Yii::$app->request->csrfToken;
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>'accounts/login','renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            
                            $content= $twig->render($codebase['filename'], ['form'=>$form,'page'=>$article,'app'=>Yii::$app]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
                         
                    return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/account/login',['form'=>$form,'page'=>$article]);
                    
       endif;
       //////////////////////////////////LOGIN taken care off in the above section //////////////////////////////////////////////////////
       
       
       if(ContentBuilder::getTemplateRouteByURL($page_url)=="forms/view"):
           //we get the form ID used for this template
            $page['article'] = $article;
            $form = Forms::find()->where(['id'=>$article->form_id])->one();    
            
                    $page['records'] = FormSubmit::find()->where(['form_id'=>$article->form_id])->andWhere('published="1"')->all();
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
						$themes = new Themes();
						$theme_id=$themes->dataTheme;
						if($theme_id=="0"):
							$theme_id = ContentBuilder::getSetting("current_theme");
						endif;
						
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$article->id,'renderer_type'=>'R'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'],['page'=>$page,'app'=>Yii::$app]); 
                            if(Yii::$app->request->isAjax):
                                return $this->renderAjax('@frontend/views/layouts/html',['data'=>$content]);
                            else:
                                return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                            endif;
                        endif;
                    endif;
            return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/forms/form-view-list',$page);
        else:
            //we check to see if this is a request for a specific record to be displayed
            $page['records'] = FormSubmit::find()->where(['url'=>$page_url])->one();
            
            if($page['records']==null): //well if its not a specifc record request then its probably a user filter 
                //we should probably check if its a call to view items posted by this user                    
                    $parent_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl(),2);                
                    $article = Pages::find()->where(['url'=>$parent_url])->one();
                    $page['records'] = FormSubmit::find()->where(['usrname'=>$page_url])->all();
                    $page['article']= $article;
                    if($page['records']!=null):
                        if(ContentBuilder::getSetting("twig_template")=="Yes"):
                            //we handle the loading of twig template if it is turned on
                            $themes = new Themes();
							$theme_id=$themes->dataTheme;
							if($theme_id=="0"):
								$theme_id = ContentBuilder::getSetting("current_theme");
							endif;
							
                            $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$article->id,'renderer_type'=>'R'])->one();
                            if(($codebase!=null)&& ($codebase['code']<>"")):
                                $loader = new Twig();
                                $twig = new \Twig_Environment($loader);
                                $content= $twig->render($codebase['filename'],['page'=>$page,'app'=>Yii::$app]);
                                return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                            endif;
                        endif;
                        return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/forms/form-view-list',$page);
                    endif;
            endif;
            
            if($page['records']==null):
                        $error404 = ContentBuilder::getSetting("error_page");
                        
                        $header_image="";
                            if($error404!=""):
                                $article = Pages::find()->where(['id'=>$error404])->one();
                                if($article==null):
                                    return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/standard/error',['page'=>$article,'header_image'=>$header_image]);
                                else:
                                        $content = $article->description;
                                        return $this->render('@frontend/views/layouts/html',['data'=>$content]);                                    
                                endif;
                            else:
                                return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/standard/error',['page'=>$article,'header_image'=>$header_image]);
                            endif;
            endif;
                            $views = $page['records']->no_of_views + 1;
                            $page['records']->setAttribute('no_of_views', $views);
                            $page['records']->update(false);
                            $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');
            if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $themes = new Themes();
							$theme_id=$themes->dataTheme;
							if($theme_id=="0"):
								$theme_id = ContentBuilder::getSetting("current_theme");
							endif;
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$page['records']->form_id,'renderer_type'=>'D'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'],['page'=>$page,'app'=>Yii::$app,'metadata'=>$metadata]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
            return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/forms/form-view-details',$page);
        endif;
    }
    
    public function actionSearch(){
        /* 
         * This routine is used to search custom forms within the system. If the return-type is set as AJAX, then the result is just returned
         * If however the return_type is not set, it renders the result based on the theme's view renderer
         * Yii::$app->request->get('form-post') - this is the form post to search on
         * Yii::$app->request->get('search-field') - this is an array of fields to search on in the form post
         * Yii::$app->request->get('form-widget') - this is the form-widget to be called to render the results
         * Yii::$app->request->get('return-type') - if set to json then the renderer is not called after the result but rather an ajax is returned
         * Yii::$app->request->get('exclude') - this is the set of records to exclude from the search
         * Yii::$app->request->get('search-type') - if this is feedback then we call the feedback function to return feedback
         * Yii::$app->request->get('params') - if this is set we pass the name=value pairs to the called widget e.g name1=val1&name2=val2 etc
         */
		 
        if(Yii::$app->request->get('search-type')=="feedback"):		
			return FormsController::actionFeedback();      
        endif;
        $article = Forms::find()->where(['name'=>Yii::$app->request->get('form-post')])->one();
        if($article==null):
            if(Yii::$app->request->get('return-type')=="json"):
                return Yii::$app->api->sendSuccessResponse(["Invalid object request"]);
            endif;
            return $this->renderPartial('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/standard/error');
        endif;
        $query = FormSubmit::find();
        //apply filter on no of records
        
        
            
        
        if(Yii::$app->request->get('limit')!=null):
            $query->limit(Yii::$app->request->get('limit'));
        endif;
		
        
        
              
        
        //apply filter for order
        if(Yii::$app->request->get('order')!=null):
                                $order=Yii::$app->request->get('order');
                                $order_arr= explode(" ",$order);
                                $ordering="";
                                $order_sorted=0;
                                if(sizeof($order_arr) > 1):
                                    $ordering = $order_arr[1];
                                    $order=$order_arr[0];
                                endif;
                                if($order=="random"):
                                    $query->orderBy(new Expression('rand()'));
                                    $order_sorted=1;
                                endif;
                                if($order=="last"):
                                    $query->orderBy(['date_stamp'=>SORT_DESC]);
                                    $order_sorted=1;
                                endif;
                                if($order=="first"):
                                    $query->orderBy(['date_stamp'=>SORT_ASC]);
                                    $order_sorted=1;
                                endif;
                                if($order=="views"):                                    
                                    $query->orderBy(['no_of_views'=>SORT_DESC]);
                                    if($ordering=="ASC"):
                                        $query->orderBy(['no_of_views'=>SORT_ASC]);
                                    endif;
                                    $order_sorted=1;
                                endif;
                                if($order=="user"):
                                    $query->orderBy(['usrname'=>SORT_ASC]);
                                    if($ordering=="DESC"):
                                        $query->orderBy(['no_of_views'=>SORT_DESC]);
                                    endif;
                                    $order_sorted=1;
                                endif;
                                if($order=="rating"):
                                    $query->orderBy(['rating'=>SORT_DESC]);
                                    if($ordering=="ASC"):
                                        $query->orderBy(['rating'=>SORT_ASC]);
                                    endif;
                                    $order_sorted=1;
                                endif; 
                                if($order_sorted==0):                   
                                    if(trim($ordering)=="DESC"):
                                        $submit_arr = FormData::find()->select('form_submit_id')->where(['param'=>$order])->orderBy(['param_val'=>SORT_DESC])->asArray()->column();
                                    else:
                                        $submit_arr = FormData::find()->select('form_submit_id')->where(['param'=>$order])->orderBy(['param_val'=>SORT_ASC])->asArray()->column();  
                                    endif;
                                    $query->andWhere(['in','id',$submit_arr])->orderBy(new Expression('FIND_IN_SET (id,:form_submit_id)'))->addParams([':form_submit_id'=>implode(",",$submit_arr)]);
                                    
                                endif;
        endif;
        //apply filter on random fetch
        if(Yii::$app->request->get('random')=="true"):
            $query->orderBy(new Expression('rand()'));
        endif;
        //apply offset filter
        if(Yii::$app->request->get('offset')!=null):
            $query->offset(Yii::$app->request->get('offset'));
        endif;
        //get records for only a page if the page parameter is passed through
        if(Yii::$app->request->get('page')!=null && Yii::$app->request->get('page') >0):
            if(Yii::$app->request->get('limit')!=null):
                $offset = (Yii::$app->request->get('page') - 1) * Yii::$app->request->get('limit');
                $query->offset($offset);
            endif;
        endif;
        $criteria_found=0;
        if(Yii::$app->request->get('search-field')!=null):            
            $data_query = FormData::find()->select('form_submit_id');            
            $search_params=explode("|",urldecode(Yii::$app->request->get('search-field')));
            foreach($search_params as $param):
                list($p,$v)=explode("=",$param);
                
                //this is used to search based on submit id
                if($p=="form_submit_id"):
                        $data_query->orWhere('form_submit_id="'.$v.'"');
                    continue;
                endif;
                
                if(count($search_params)==1):
                        $data_query->andWhere('param="'.$p.'"')->orWhere(['like','param_val',$v])->andFilterCompare('param_val',$v);
                else:                    
                        $data_query->andWhere('param="'.$p.'"')->orWhere(['like','param_val',$v]);                    
                endif;
            endforeach;
            $search_params=explode("&",urldecode(Yii::$app->request->get('search-field')));
            foreach($search_params as $param):
                list($p,$v)=explode("=",$param);
                
                //this is used to search based on submit id
                if($p=="form_submit_id"):
                        //$data_query->orWhere('form_submit_id="'.$v.'"');
                    continue;
                endif;
                
                if(count($search_params)==1):
                    //$data_query->andWhere('param="'.$p.'"')->andWhere(['like','param_val',$v]);
                else:
                    //$data_query->andWhere('param="'.$p.'"')->andWhere(['like','param_val',$v]);
                endif;
            endforeach;            
            $criteria_found=1;            
        endif;
        if(Yii::$app->request->get('excludes')!=null):  
            if($criteria_found < 1):
                $data_query = FormData::find()->select('form_submit_id');            
            endif;
            $search_params=explode("|",urldecode(Yii::$app->request->get('excludes')));
            foreach($search_params as $param):
                list($p,$v)=explode("=",$param);
                $d[] = $v;
                
                if($p=="form_submit_id"):
                        $data_query->andWhere('form_submit_id<>"'.$v.'"');
                    continue;
                endif;
                
                if(count($search_params)==1):
                    $data_query->andWhere('param="'.$p.'"')->andWhere(['not in','param_val',$d]);
                else:
                    $data_query->andWhere('param="'.$p.'"')->andWhere(['not in','param_val',$d]);
                endif;
            endforeach;
            $criteria_found=1;            
        endif;
        
        if($criteria_found > 0):
            $data_query->all();
        endif;
        
        
        if(Yii::$app->request->get('search-field')!=null):
                $query->with('data','data.element','data.elementVal','data.property','data.propertyVal','data.setupVal','data.setup','file','ratingdetails','user','user.displayImage')->asArray()->where(['IN','id',$data_query])->andWhere('form_id="'.$article->id.'"');
            else:
                $query->with('data','data.element','data.elementVal','data.property','data.propertyVal','data.setupVal','data.setup','file','ratingdetails','user','user.displayImage')->asArray()->where(['form_id'=>$article->id]);
        endif;
        if(Yii::$app->request->get('published')==null):
            $query->andWhere('published="1"');
        endif;
        if(Yii::$app->request->get('published')=="0"):
            $query->andWhere('published="0"');
        endif;
        if(Yii::$app->request->get('form_submit_id')!=null):
            $query->andWhere('id="'.Yii::$app->request->get('form_submit_id').'"');
        endif;
        if (Yii::$app->request->get('logged')=="true"):
            $query->andWhere('usrname="'.Yii::$app->user->identity->username.'"');            
        endif;
		if (Yii::$app->request->get('logged')=="false"):
            $query->andWhere('usrname<>"'.Yii::$app->user->identity->username.'"');            
        endif;
		if(Yii::$app->request->get('url')!=null):
            $query->andWhere('url="'.Yii::$app->request->get('url').'"');
        endif;
        if(Yii::$app->request->get('return-type')=="count"):
            return \yii\helpers\Json::encode($query->count());
        endif;
		
        
        if(Yii::$app->request->get('return-type')=="json"):
            $data = $query->all();
            return \yii\helpers\Json::encode($data);
        endif;
                $page['records'] = $query->all();
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $themes = new Themes();
							$theme_id=$themes->getDataTheme();
							if($theme_id=="0"):
								$theme_id = ContentBuilder::getSetting("current_theme");
							endif;
							
                        //$render = 
                        //since we may get the widget we want to use to display the result
                        $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');
                        $metadata['param'] = Yii::$app->request->csrfParam;
                        $metadata['token'] = Yii::$app->request->csrfToken;
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>Yii::$app->request->get('form-widget'),'renderer_type'=>'I'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            if(Yii::$app->request->get('params')!=null):
                                parse_str(Yii::$app->request->get('params'), $params);
                                $content= $twig->render($codebase['filename'],['page'=>$page,'app'=>Yii::$app,'metadata'=>$metadata,'params'=>$params]);
                            else:
                                $content= $twig->render($codebase['filename'],['page'=>$page,'app'=>Yii::$app,'metadata'=>$metadata]);
                            endif;
                            
                            return $this->renderPartial('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
            return $this->renderPartial('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/forms/form-view-list',$page);
        
    }
    
    
    public function actionSaveForm(){

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
                                                $frmFiles->setAttribute("doc_name",$k);
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
                                                    $frmFiles->setAttribute("doc_name",$k);
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
                    
                    
                       
                        foreach($_POST as $key => $value)
                        {
                                //if there are more fields in this form, we should extend the information and store in the data model
                                $a = ProfileDetails::deleteAll(['profile_id'=>Yii::$app->user->identity->id,'param'=>$key]);
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
                                                $frmFiles->setAttribute("doc_name",$k);
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
                                                    $frmFiles->setAttribute("doc_name",$k);
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
                            
                       
                        foreach($_POST as $key => $value)
                        {
                                if($value<>""):
                                    $a = FeedbackDetails::deleteAll(['feedback_id'=>$id,'param'=>$key]);
                                    $feedback_data = new FeedbackDetails();
                                    $feedback_data->setAttribute("feedback_id",$id);
                                    $feedback_data->setAttribute("param",$key);
                                    $feedback_data->setAttribute("param_val",$value);
                                    $feedback_data->save();
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
                                                $frmFiles = new FeedbackFiles();
                                                $frmFiles->setAttribute("feedback_id",Yii::$app->user->identity->id);
                                                $frmFiles->setAttribute("file_name",$v['name']);
                                                $frmFiles->setAttribute("file_path",$session ."/".$fileName);
                                                $frmFiles->setAttribute("file_type",$v['type']);
                                                $frmFiles->setAttribute("doc_name",$k);
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
                                                    $frmFiles = new FeedbackFiles();
                                                    $frmFiles->setAttribute("feedback_id",Yii::$app->user->identity->id);
                                                    $frmFiles->setAttribute("file_name",$v['name'][$counter]);
                                                    $frmFiles->setAttribute("file_path",$session ."/".$fileName);
                                                    $frmFiles->setAttribute("file_type",$v['type'][$counter]);
                                                    $frmFiles->setAttribute("doc_name",$k);
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
                    return "Feedback Saved";
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
                            $form_submit->setAttribute("token",Yii::$app->request->post("_csrf-frontend"));
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
                            $record->setAttribute("token",Yii::$app->request->post("_csrf-frontend"));
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
                        //$a = FormData::deleteAll(['form_submit_id'=>$id]);
                       
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
                                            $a = FormData::deleteAll(['form_submit_id'=>$id,'param'=>$key]);
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
                                                $frmFiles->setAttribute("doc_name",$k);
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
                                                    $frmFiles->setAttribute("doc_name",$k);
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
    
public static function actionFeedback(){
    /*
     * This function is used to return the feedback details on forms as stored in the feedback* database objects
     * This function can directly sift through the feedback details without going through the Form Submit IDs.
     * return-type=json will return the feedback in JSON format
     * feedback-widget will return a widget of the feedback data
     * owner=true means fetching feedbacks given by logged in user
     * publisher=username means all feedbacks given to items published by user
     * logged=true means all feedbacks given to items published by logged in user
     */
    
    $query = Feedback::find()->with('details');
    
    if(Yii::$app->request->get('publisher')!=null):
            $fsubmit=FormSubmit::find()->select('id')->where(['usrname'=>Yii::$app->request->get('publisher')])->column();
            $query->andFilterWhere('IN','target_id',$fsubmit);
    endif;
    if(Yii::$app->request->get('logged')=="true"):
            $fsubmit=FormSubmit::find()->select('id')->where(['usrname'=>Yii::$app->user->identity->username])->column();
            $query->andFilterWhere('IN','target_id',$fsubmit);
    endif;
    if(Yii::$app->request->get('form_id')!=null):
            $query->andWhere('form_id="'.Yii::$app->request->get('form_id').'"');
    endif;
    if(Yii::$app->request->get('form_submit_id')!=null):
            $query->andWhere('target_id="'.Yii::$app->request->get('form_submit_id').'"');
    endif;
    if (Yii::$app->request->get('owner')=="true"):
            $query->andWhere('usrname="'.Yii::$app->user->identity->username.'"');            
    endif;
    if(Yii::$app->request->get('return-type')=="count"):
            return \yii\helpers\Json::encode($query->count());
    endif;
    if(Yii::$app->request->get('return-type')=="json"):
            $data = $query->all();
            return \yii\helpers\Json::encode($data);
    endif;
    $page['records'] = $query->all();
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        //$render = 
                        //since we may get the widget we want to use to display the result
                        $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');
                        $metadata['param'] = Yii::$app->request->csrfParam;
                        $metadata['token'] = Yii::$app->request->csrfToken;
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>Yii::$app->request->get('feedback-widget'),'renderer_type'=>'I'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'],['page'=>$page,'app'=>Yii::$app,'metadata'=>$metadata]);
                            return $content;
                            //return $this->renderPartial('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
            return $this->renderPartial('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/forms/form-view-list',$page);
} 
}

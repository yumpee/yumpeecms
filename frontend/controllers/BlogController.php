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
use frontend\components\ContentBuilder;
use frontend\models\Articles;
use frontend\models\ArticleDetails;
use frontend\models\Templates;
use backend\models\ArticlesCategories;
use backend\models\Pages;
use backend\models\Users;
use frontend\models\Twig;
use backend\models\Forms;
use frontend\models\Domains;
use frontend\models\Settings;
use frontend\models\Themes;

class BlogController extends Controller{
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
     //we check to see what page is being called
            if (strpos($news_url, '?') !== false):
                list($news_url,$search)= explode("?",$news_url);
            endif;
     if(ContentBuilder::getTemplateRouteByURL($news_url)!="blog/index"):
                    $article = Articles::find()->where(['url'=>$news_url])->one();
                            //if it requires log in and we are not logged in then redirect to login page
                            if(($article['require_login']=="Y")&&(Yii::$app->user->isGuest)):       
                                    $page =[];                                    
                                    $page_url =  ContentBuilder::getURLByRoute("blog/index");
                                    $form['callback'] = $page_url."/".$article['url'];
                                    $page_url =  ContentBuilder::getURLByRoute("accounts/login");
                                    $article = Pages::find()->where(['url'=>$page_url])->one(); 
                                    $form['login_url'] = Yii::$app->request->getBaseUrl()."/".$article['url'];
                                    $form['message'] = "You have to login to view this article";
                                    $form['param'] = Yii::$app->request->csrfParam;
                                    $form['token'] = Yii::$app->request->csrfToken;
                                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                                            //we handle the loading of twig template if it is turned on
                                            $theme_id = ContentBuilder::getSetting("current_theme");
                                            $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>'accounts/login','renderer_type'=>'V'])->one();
                                            if(($codebase!=null)&& ($codebase['code']<>"")):
                                                $loader = new Twig();
                                                $twig = new \Twig_Environment($loader);
                                                $content= $twig->render($codebase['filename'], ['form'=>$form,'page'=>$article]);
                                                return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                                            endif;
                                    endif;                         
                                    return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/account/login',['form'=>$form,'page'=>$article]);                    
                            endif;       
                            //end require login
                    //protect certain content of the page depending if a user is logged in or not
                    if((Yii::$app->user->isGuest)):
                        $hider = array("{yumpee_hide_on_login}", "{/yumpee_hide_on_login}");
                        $description = str_replace($hider,"",$article->body_content);
                        $article->setAttribute("body_content",ContentBuilder::getScreenContent($description,"{yumpee_login_to_view}",TRUE));                        
                    endif;
                    if((!Yii::$app->user->isGuest)):
                        $hider = array("{yumpee_login_to_view}", "{/yumpee_login_to_view}");
                        $description = str_replace($hider,"",$article->body_content);
                        $article->setAttribute("body_content",ContentBuilder::getScreenContent($description,"{yumpee_hide_on_login}",TRUE)); 
                    endif;
                    //
                            
                    $user_profile = Users::findOne(['username'=>$article->usrname]);
                    if(isset($article->displayImage)):
                        $header_image= ContentBuilder::getImage($article->displayImage->id,"details");
                    else:
                        $header_image="";
                    endif;
                    
                    //check and set the breadcrumbs
                    if(ContentBuilder::getSetting("breadcrumbs")=="on"):
                        $blog_index_url = ContentBuilder::getURLByRoute("blog/index");
                        $p = Pages::findOne(['url'=>$blog_index_url]);
                        $this->view->params['breadcrumbs'][] = ['label' => $p->menu_title, 'url' => Yii::$app->request->getBaseUrl().'/'.$blog_index_url];
                        $this->view->params['breadcrumbs'][] = $article->title;
                    endif;
                    
                            //if there is a feedback form attached to this article then prepare the data for translation
                            $feedback_arr = Forms::find()->where(['id'=>$article->feedback])->one();
                            if($feedback_arr!=null):
                                if((Yii::$app->user->isGuest)):
                                    $metadata['rs']= new Users();
                                else:
                                    $metadata['rs'] = Users::find()->where(['username'=>Yii::$app->user->identity->username])->one();
                                endif;
                                $pages = new Pages();
                                $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');
                                $metadata['param'] = Yii::$app->request->csrfParam;
                                $metadata['token'] = Yii::$app->request->csrfToken;
                                $metadata['feedback_type']="articles";
                                $metadata['target_id']= $article->id;
                            endif;
                            //update views here
                            $views = $article->no_of_views + 1;
                            $article->setAttribute('no_of_views', $views);
                            $article->update(false);
                     
                     
                     //$article->save();
                     //render through twig if available       
                    
                    $preferred_template = Templates::find()->where(['id'=>$article->render_template])->one();                    
                    if($preferred_template!=null):
                        $renderer=$preferred_template->route;
                    else:
                        $renderer="blog/details";
                    endif;
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            if($feedback_arr!=null):                                
                                $code_arr = Twig::find()->where(['renderer'=>$feedback_arr->id])->one();
                                if(($code_arr!=null)&& ($code_arr['code']<>"")):
                                    //if the feedback form is been rendered by Twig                                    
                                    $loader = new Twig();
                                    $twig_feedback = new \Twig_Environment($loader);
                                    $c= $twig_feedback->render($code_arr['filename'], ['form'=>$feedback_arr,'page'=>$article,'metadata'=>$metadata,'app'=>Yii::$app]);
                                    //$c = $code_arr['code'];
                                    $article['body_content'] = $article['body_content']."<p>".$c;
                                else:
                                    //if no Twig code is available then fetch the default view for feedback forms
                                    $c = $this->renderPartial('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/forms/'.$feedback_arr['form_type'],['form'=>$feedback_arr,'page'=>$pages,'metadata'=>$metadata]); 
                                    $article['body_content'] = $article['body_content']."<p>".$c;
                                endif;
                                
                            endif; 
                            //render your Twig page
                            $content= $twig->render($codebase['filename'], ['page'=>$article,'blogger'=>$user_profile,'header_image'=>$header_image]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                        
                    endif;
                    if($feedback_arr!=null):
                                $code_arr = Twig::find()->where(['renderer'=>$feedback_arr->id])->one();
                                if(($code_arr!=null)&& ($code_arr['code']<>"")):
                                    //if the feedback form is been rendered by Twig                                    
                                    $loader = new Twig();
                                    $twig_feedback = new \Twig_Environment($loader);
                                    $c= $twig_feedback->render($code_arr['filename'], ['form'=>$feedback_arr,'page'=>$article,'metadata'=>$metadata,'app'=>Yii::$app]);
                                    //$c = $code_arr['code'];
                                    $article['body_content'] = $article['body_content']."<p>".$c;
                                else:
                            //incase we attached a form to the article and it wasn't rendered with Twig then render through the form type view
                                $c = $this->renderPartial('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/forms/'.$feedback_arr['form_type'],['form'=>$feedback_arr,'page'=>$pages,'metadata'=>$metadata]); 
                                $article['body_content'] = $article['body_content']."<p>".$c;
                                endif;
                    endif;
                    $settings = new Settings();
                    return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$renderer,['page'=>$article,'blogger'=>$user_profile,'header_image'=>$header_image,'settings'=>$settings]); 
      else:
          $pagination=[];
          $pagination['total_page_count']=0;
          $pagination['active_page']=1;
          $page_size = ContentBuilder::getSetting("page_size");  //we need to know what a page size is
          
          $page = Pages::findOne(['url'=>$news_url]);
          
          $a = new Articles();
          //this block shows where pagination has been applied
          if(Yii::$app->request->get('p')==null):
            $records = $a->getIndexItems($page->id,"blog");
          else:
             $records = $a->getIndexItems($page->id,"blog",Yii::$app->request->get('p')); 
             $pagination['active_page'] = Yii::$app->request->get('p');
          endif;
          
          if($page_size > 0):
            $pagination['total_page_count'] = ceil($a->getIndexItemsCount($page->id, "blog") / $page_size) ;
          endif;
          //pagination application ends here
          
          //breadcrumbs addition
          if(ContentBuilder::getSetting("breadcrumbs")=="on"):
            $this->view->params['breadcrumbs'][] = $page->menu_title;
          endif;
          //render through twig if available
          $renderer="blog/index";
          $template = Templates::find()->where(['id'=>$page->template])->one();
          if(!empty($template->parent_id)):
              $renderer = $template->renderer;
          endif;
          
          if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'], ['page'=>$page,'records'=>$records,'pagination'=>$pagination,'app'=>Yii::$app]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
           endif;
          return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$renderer,['page'=>$page,'records'=>$records,'pagination'=>$pagination]);  
      endif;
    }
    public function actionCategory(){
     $page =[];
     $news_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
     if (strpos($news_url, '?') !== false):
                list($news_url,$search)= explode("?",$news_url);
     endif;
     $pagination=[];
     $pagination['total_page_count']=0;
     $pagination['active_page']=1;
     $page_size = ContentBuilder::getSetting("page_size");  //we need to know what a page size is
     
       if(ContentBuilder::getTemplateRouteByURL($news_url)!="blog/category"):
                    
                    $page = ArticlesCategories::findOne(['url'=>$news_url]);
                    $a = new Articles();
                    
                    if(Yii::$app->request->get('p')==null):
                        $records = $a->getIndexItems($page->id,"category");
                    else:
                        $records = $a->getIndexItems($page->id,"category",Yii::$app->request->get('p')); 
                        $pagination['active_page'] = Yii::$app->request->get('p');
                    endif;
          
                    if($page_size > 0):
                        $pagination['total_page_count'] = ceil($a->getIndexItemsCount($page->id, "category") / $page_size) ;
                    endif;
                    
                    //check and set the breadcrumbs
                    if(ContentBuilder::getSetting("breadcrumbs")=="on"):
                        $blog_index_url = ContentBuilder::getURLByRoute("blog/category");
                        $p = Pages::findOne(['url'=>$blog_index_url]);
                        $this->view->params['breadcrumbs'][] = ['label' => $p->menu_title, 'url' => Yii::$app->request->getBaseUrl().'/'.$blog_index_url];
                        $this->view->params['breadcrumbs'][] = $page->name;
                    endif;
                    //render through twig if available
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>'blog/index','renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'], ['page'=>$page,'records'=>$records,'pagination'=>$pagination]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
                    return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/blog/index-category',['page'=>$page,'records'=>$records,'pagination'=>$pagination]); 
      else:
          $page = Pages::findOne(['url'=>$news_url]);
          //breadcrumbs addition
          if(ContentBuilder::getSetting("breadcrumbs")=="on"):
            $this->view->params['breadcrumbs'][] = $page->menu_title;
          endif;
          $ci = new ArticlesCategories();
          $records = $ci->getBlogCategoryIndex($page->id);
          
          $renderer="blog/category";
          $template = Templates::find()->where(['id'=>$page->template])->one();
          if(!empty($template->parent_id)):
               $renderer = $template->renderer;
          endif;
          
          
          //render through twig if available
          if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'], ['page'=>$page,'records'=>$records,'pagination'=>$pagination]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
           endif;
          return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$renderer,['page'=>$page,'records'=>$records,'pagination'=>$pagination]);
      endif; 
      
    }
    public function actionSearch(){
        /* 
         * This routine is used to search articles within the system. If the return-type is set as json, then the result is just returned
         * If however the return_type is not set, it renders the result based on the theme's view renderer
         * Yii::$app->request->get('route') - this is used to determine the articles' filter type. If not set, it assumes that all articles will be returned
         * Yii::$app->request->get('search-field') - this is an array of fields to search on in the article post
         * Yii::$app->request->get('form-widget') - this is the form-widget to be called to render the results
         * Yii::$app->request->get('return-type') - if set to json then the renderer is not called after the result but rather an ajax is returned
         * Yii::$app->request->get('exclude') - this is the set of records to exclude from the search
         * Yii::$app->request->get('search-type') - if this is feedback then we call the feedback function to return feedback
         * Yii::$app->request->get('params') - if this is set we pass the name=value pairs to the called widget e.g name1=val1&name2=val2 etc
         */
		 
        if(Yii::$app->request->get('search-type')=="feedback"):		
			return BlogController::actionFeedback();      
        endif;
            
        
        $query = Articles::find();
        $page=[];
        $page['title']="";        
         //if route is set then check to be sure it exists
        if(Yii::$app->request->get('route')!=null):
                list($v,$p)=explode("=",Yii::$app->request->get('route'));
                if($v=="index"):                                        
                    $pge = \backend\models\Pages::find()->where(['url'=>$p])->one();
                    $blog_index_articles = \backend\models\ArticlesBlogIndex::find()->select('articles_id')->where(['blog_index_id'=>$pge['id']])->column();                                        
                    $query->where(['IN','id',$blog_index_articles]);
                    $page['page'] = $pge;
                endif;
                if($v=="category"):
                    $pge = \backend\models\ArticlesCategories::find()->where(['url'=>$p])->one();
                    $blog_index_articles = \backend\models\ArticlesCategoryRelated::find()->select('articles_id')->where(['category_id'=>$pge['id']])->column();
                    $query->where(['IN','id',$blog_index_articles]);
                    $page['page'] = $pge;
                endif;
                if($pge==null):
                    if(Yii::$app->request->get('return-type')=="json"):
                        return Yii::$app->api->sendSuccessResponse(["Invalid route object request"]);
                    endif;
                    return $this->renderPartial('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/standard/error');
                endif;
                
        endif;
        $renderer="blog/index";
        //let us handle a request based on renderers
        if(Yii::$app->request->get('renderer')!=null):
            $temp_arr = Templates::find()->where(['route'=>Yii::$app->request->get('renderer')])->one();
            $query->andWhere('render_template="'.$temp_arr['id'].'"');            
        endif;
        
            
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
                                    $query->orderBy(['date'=>SORT_DESC]);
                                    $order_sorted=1;
                                endif;
                                if($order=="first"):
                                    $query->orderBy(['date'=>SORT_ASC]);
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
                                    $offset=0;
                                    if(Yii::$app->request->get('page')!=null && Yii::$app->request->get('page') >0):
                                        if(Yii::$app->request->get('limit')!=null):
                                            $offset = (Yii::$app->request->get('page') - 1) * Yii::$app->request->get('limit');                                            
                                        endif;
                                    endif;    
                                    if(trim($ordering)=="DESC"):
                                        if($offset!=0):
                                            $submit_arr = ArticleDetails::find()->select('article_id')->where(['param'=>$order])->orderBy(['param_val'=>SORT_DESC])->offset($offset)->asArray()->column();
                                        else:
                                            $submit_arr = ArticleDetails::find()->select('article_id')->where(['param'=>$order])->orderBy(['param_val'=>SORT_DESC])->asArray()->column();
                                        endif;
                                    else:
                                        if($offset!=0):
                                            $submit_arr = ArticleDetails::find()->select('article_id')->where(['param'=>$order])->orderBy(['param_val'=>SORT_ASC])->offset($offset)->asArray()->column();  
                                        else:
                                            $submit_arr = ArticleDetails::find()->select('article_id')->where(['param'=>$order])->orderBy(['param_val'=>SORT_ASC])->asArray()->column();  
                                        endif;
                                    endif;
                                    $query->andWhere(['in','id',$submit_arr])->orderBy(new Expression('FIND_IN_SET (id,:article_id)'))->addParams([':article_id'=>implode(",",$submit_arr)]);
                                    
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
            $data_query = ArticleDetails::find()->select('article_id');            
            $search_params=explode("|",urldecode(Yii::$app->request->get('search-field')));
            $search_succeed=0;
            if(sizeof($search_params) > 1):
            foreach($search_params as $param):
                list($p,$v)=explode("=",$param);                
                //this is used to search based on submit id
                if($p=="form_submit_id"):
                        $data_query->orWhere('article_id="'.$v.'"');
                    continue;
                endif;
                
                if(count($search_params)==1):
                        $data_query->andWhere('param="'.$p.'"')->orWhere(['like','param_val',$v])->andFilterCompare('param_val',$v);
                else:                    
                        $data_query->andWhere('param="'.$p.'"')->orWhere(['like','param_val',$v]);                    
                endif;
            endforeach;
            $search_succeed=1;
            endif;
            
            if($search_succeed < 1):
            $search_params=explode("&",urldecode(Yii::$app->request->get('search-field')));
            if(sizeof($search_params) > 1):
                $form_arr= array();
                $int_count=0;
                foreach($search_params as $param):                    
                    $pn = explode("=",$param);
                    if(count($pn) > 1):
                        list($p,$v)=explode("=",$param);
                    endif;
                    $pl=explode("<",$param);
                    $pg=explode(">",$param);
                    $plq=explode("<=",$param);
                    $pgq=explode(">=",$param);
                    //this is used to search based on submit id
                    if($p=="article_id"):
                            $data_query->andWhere('article_id="'.$v.'"');
                        continue;
                    endif;
                    if(count($pgq) > 1):
                        $form_arr[$int_count] = ArticleDetails::find()->select('article_id')->where(['param'=>$pgq[0]])->andWhere('param_val >="'.$pgq[1].'"')->column();
                        $int_count++;
                        continue;
                    endif;
                    if(count($plq) > 1):
                        $form_arr[$int_count] = ArticleDetails::find()->select('article_id')->where(['param'=>$plq[0]])->andWhere('param_val <="'.$plq[1].'"')->column();
                        $int_count++;
                        continue;
                    endif;
                    if(count($pg) > 1):
                        $form_arr[$int_count] = ArticleDetails::find()->select('article_id')->where(['param'=>$pg[0]])->andWhere('param_val >"'.$pg[1].'"')->column();
                        $int_count++;
                        continue;
                    endif;
                    if(count($pl) > 1):
                        $form_arr[$int_count] = ArticleDetails::find()->select('article_id')->where(['param'=>$pl[0]])->andWhere('param_val <"'.$pl[1].'"')->column();
                        $int_count++;
                        continue;
                    endif;
                    $form_arr[$int_count] = ArticleDetails::find()->select('article_id')->where(['param'=>$p])->andWhere(['like','param_val',$v])->column();
                    $int_count++;
                endforeach;
                    $form_submit_arr = $form_arr[0];
                    foreach ($form_arr as $form_submit_item):
                        $form_submit_arr = array_intersect($form_submit_arr,$form_submit_item);
                    endforeach;
                    $data_query->where(['IN','article_id',$form_submit_arr]);
                $search_succeed=1;
            endif;            
            endif;
            if($search_succeed < 1):
                foreach($search_params as $param):
                list($p,$v)=explode("=",$param);                
                //this is used to search based on submit id
                if($p=="article_id"):
                        $data_query->orWhere('article_id="'.$v.'"');
                    continue;
                endif;
                
                if(count($search_params)==1):
                        $data_query->andWhere('param="'.$p.'"')->orWhere(['like','param_val',$v])->andFilterCompare('param_val',$v);
                else:                    
                        $data_query->andWhere('param="'.$p.'"')->orWhere(['like','param_val',$v]);                    
                endif;
                endforeach;
            endif;
            $criteria_found=1;            
        endif;
        if(Yii::$app->request->get('excludes')!=null):  
            if($criteria_found < 1):
                $data_query = ArticleDetails::find()->select('article_id');            
            endif;
            $search_params=explode("|",urldecode(Yii::$app->request->get('excludes')));
            foreach($search_params as $param):
                list($p,$v)=explode("=",$param);
                $d[] = $v;
                
                if($p=="article_id"):
                        $data_query->andWhere('article_id<>"'.$v.'"');
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
                $query->with('details','file','author','author.displayImage')->asArray();
            else:
                $query->with('details','file','author','author.displayImage')->asArray();
        endif;
        if(Yii::$app->request->get('published')==null):
            $query->andWhere('published="1"');
        endif;
        if(Yii::$app->request->get('published')=="0"):
            $query->andWhere('published="0"');
        endif;
        if(Yii::$app->request->get('article_id')!=null):
            $query->andWhere('id="'.Yii::$app->request->get('article_id').'"');
        endif;
        if (Yii::$app->request->get('logged')=="true"):
            $query->andWhere('usrname="'.Yii::$app->user->identity->username.'"');            
        endif;
	if (Yii::$app->request->get('logged')=="false"):
            $query->andWhere('usrname<>"'.Yii::$app->user->identity->username.'"');            
        endif;
        if(Yii::$app->request->get('user_id')!=null):
            $user_arr = Users::find()->where(['id'=>Yii::$app->request->get('user_id')])->one();
            if($user_arr!=null):
                $query->andWhere('usrname="'.$user_arr['username'].'"');
            endif;
        endif;
	if(Yii::$app->request->get('url')!=null):
            $query->andWhere('url="'.Yii::$app->request->get('url').'"');
        endif;
        if(Yii::$app->request->get('date_stamp')!=null):
            $query->andWhere(['LIKE','date',Yii::$app->request->get('date_stamp')]);
        endif;
        if(Yii::$app->request->get('between')!=null):
            list($start_from,$end_at) = explode(",",Yii::$app->request->get('between'));
            if($start_from!=""):
                $query->andWhere('date>="'.$start_from.'"');
            endif;
            if($end_at!=""):
                $query->andWhere('date<="'.$end_at.'"');
            endif;
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
                        //since we may get the form widget we want to use to display the result
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
                        
                        //we process article-widget here
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>Yii::$app->request->get('article-widget'),'renderer_type'=>'W'])->one();
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
            return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$renderer,['page'=>$page['page'],'records'=>$page['records']]); 


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
            $fsubmit=Articles::find()->select('id')->where(['usrname'=>Yii::$app->request->get('publisher')])->column();
            $query->andFilterWhere('IN','target_id',$fsubmit);
    endif;
    if(Yii::$app->request->get('logged')=="true"):
            $fsubmit=Articles::find()->select('id')->where(['usrname'=>Yii::$app->user->identity->username])->column();
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
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
use frontend\models\Twig;
use frontend\models\Templates;
use backend\models\Pages;
use backend\models\Roles;
use common\models\LoginForm;
use common\models\User;
use frontend\models\Domains;
use frontend\models\ProfileDetails;


class AccountsController extends Controller{
    //put your code here
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
        
     return $this->render('index',$page);   
    }
    public function actionLogin(){
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if(Yii::$app->request->post()):
            $model = new LoginForm();
            $model->username=Yii::$app->request->post("username");            
            if(Yii::$app->request->post("login-type")=="email"):
				$email_arr = \backend\models\Users::find()->where(['email'=>Yii::$app->request->post("email")])->one();
				if($email_arr!=null):
					$model->username=$email_arr->username;
				else:
					Yii::$app->session->setFlash('error', "Invalid username or password");                                        
				endif;
            endif;
            $model->password=Yii::$app->request->post("password");
            
        endif;        
        if (Yii::$app->request->post() && $model->login()) {
            //if the callback was set for this login then take user to the callback URL
                           
            if(Yii::$app->request->post("callback")<>""):
                return $this->redirect(Yii::$app->request->post("callback"),302)->send();
            endif;
            //if there is meant to be a return type of JSON, then do it here
            if(Yii::$app->request->post("return-type")=="json"):
                    return \yii\helpers\Json::encode(["message"=>"success"]);
            endif;
            
            //here we check if the home page for this role is setup
            $role_obj = Roles::find()->where(['id'=>Yii::$app->user->identity->role_id])->one();
            if($role_obj!=null):                
                $role_home_page = Pages::find()->where(['id'=>$role_obj['home_page']])->one();
                if($role_home_page!=null):
                    $role_home=ContentBuilder::getSetting("home_url")."/".$role_home_page['url'];   
                    return $this->redirect($role_home,302)->send();
                endif;
            endif;
            
            
            return $this->goHome();
        } else {        
            if(Yii::$app->request->post("return-type")=="json"):
                    return \yii\helpers\Json::encode(["message"=>"error"]);
            endif;
        $page =[];
        $page_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
        $page_url = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        if (strpos($page_url, '?') !== false):
                list($page_url,$search)= explode("?",$page_url);
        endif;
        
        $article = Pages::find()->where(['url'=>$page_url])->one();                  
        $form['login_url'] = Yii::$app->request->getBaseUrl()."/".$article['url'];
        $form['message']="";
        
        if(Yii::$app->request->get("callback")!=null):
            $form['callback']=Yii::$app->request->get("callback");
        elseif(Yii::$app->request->post("callback")!=null):
            $form['callback']=Yii::$app->request->post("callback");
        else:
            $form['callback']=""; 
        endif;
        
        
        $form['param'] = Yii::$app->request->csrfParam;
        $form['token'] = Yii::$app->request->csrfToken;
        
        $renderer="account/login";
        $view_renderer="accounts/login";
                    if($article==null):
                        $temp_arr = Templates::find()->where(['route'=>'accounts/login'])->one();
                        $article = Pages::find()->where(['template'=>$temp_arr->id])->one();                        
                    endif;
                    $template = Templates::find()->where(['id'=>$article->template])->one();
                    if(!empty($template->parent_id)):
                        $renderer = $template->renderer;
                    endif;
                    
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$view_renderer,'renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'], ['form'=>$form,'page'=>$article]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
                    return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$renderer,['form'=>$form,'page'=>$article,'app'=>Yii::$app]);
    }
    }
    public function actionLogout(){
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            $page =[];
        $page_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
        $article = Pages::find()->where(['url'=>$page_url])->one();
        $form['param'] = Yii::$app->request->csrfParam;
        $form['token'] = Yii::$app->request->csrfToken;
        
        $renderer="account/logout";
        $view_renderer="accounts/logout";
                    $template = Templates::find()->where(['id'=>$article->template])->one();
                    if(!empty($template->parent_id)):
                        $renderer = $template->renderer;
                    endif;
        
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$view_renderer,'renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'], ['form'=>$form,'page'=>$article]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
                    return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$renderer,['form'=>$form,'page'=>$article]);
        }else{
            return $this->goHome();
        }
    }
    public function actionPassword(){
        $page =[];
        $page_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
        $article = Pages::find()->where(['url'=>$page_url])->one();
        $form=[];
        $form["message"]="";
        
        if(Yii::$app->request->get("token")):
            //if the token is not in the system that flag as an error 404 page
            $user = User::find()->where(['password_reset_token'=>Yii::$app->request->get("token")])->andWhere('email="'.Yii::$app->request->get("email").'"')->one();
            if($user==null):
                throw new \yii\web\HttpException(404, 'The page you are requesting for does not exist.');
            endif;
            $form['param'] = Yii::$app->request->csrfParam;
            $form['token'] = Yii::$app->request->csrfToken;
            $form['reset_token'] = Yii::$app->request->get("token");
            if (Yii::$app->request->post() && Yii::$app->request->post("password")!=null):
                $user->setAttribute('password_hash',Yii::$app->security->generatePasswordHash(Yii::$app->request->post("password")));
                $user->save(false);
                Yii::$app->session->setFlash('success', "Your new password has been saved. You may now log into your account");  
                $form["message"] = "Your new password has been saved. You may now log into your account";
            endif;
            return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/account/reset-password',['form'=>$form,'page'=>$article]);
        endif;
        
        if (Yii::$app->request->post()) {
            //check that the email exists
            $user_record = User::find()->where(['email'=>Yii::$app->request->post("email")])->one();
            if($user_record==null):
                Yii::$app->session->setFlash('error', "There is no record of this email in the database");                
            else:
                //generate a token for this user
                $email = Yii::$app->request->post("email");
                $token = md5(date("YmdHis").Yii::$app->request->post("email").rand(1000,100000));
                $user_record->setAttribute("password_reset_token",$token);
                $user_record->save(false);
                $message="Hi, <br><br>Click on the link below to reset your lost password.<br><a href='".Yii::$app->request->getAbsoluteUrl()."?token=".$token."&email=".$email."'>".Yii::$app->request->getAbsoluteUrl()."?token=".$token."&email=".$email."</a>";
                $from_email = ContentBuilder::getSetting("smtp_sender_email");
                Yii::$app->mailer->compose()
            ->setFrom($from_email)
            ->setTo(Yii::$app->request->post("email"))
            ->setSubject("Password reset information")
            ->setHtmlBody($message)
            ->send();
                Yii::$app->session->setFlash('success', "An email has been set with a link to reset your password");
                $form["message"] = "An email has been set with a link to reset your password";
                //we need to finish off the password reset here after SMTP has been completed
            endif;
            
            
        }
        $form['param'] = Yii::$app->request->csrfParam;
        $form['token'] = Yii::$app->request->csrfToken;
        
     $renderer="accounts/password";
     $view_file="account/forgot-password";
      $template = Templates::find()->where(['id'=>$article->template])->one();
      if(!empty($template->parent_id)):
            $renderer = $template->renderer;
            $view_file="account/".$renderer;
      endif;
        
        
        if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'], ['form'=>$form,'page'=>$article]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
        endif;
        return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$view_file,['form'=>$form,'page'=>$article,'app'=>Yii::$app]);
    }
    
    public function actionSearch(){
        /* 
         * This routine is used to search users within the system. If the return-type is set as json, then the result is just returned
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
			return AccountsController::actionFeedback();      
        endif;
            
        
        $query = \frontend\models\Users::find();
        $page=[];
        $page['title']="";        
         //if route is set then check to be sure it exists
        if(Yii::$app->request->get('role')!=null):
                $pge = Roles::find()->where(['name'=>Yii::$app->request->get('role')])->one();
                if($pge==null):
                    if(Yii::$app->request->get('return-type')=="json"):
                        return Yii::$app->api->sendSuccessResponse(["Invalid route object request"]);
                    endif;
                    return $this->renderPartial('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/standard/error');
                endif;
        endif;
       
        $renderer="roles/index";
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
                                    $query->orderBy(['created_at'=>SORT_DESC]);
                                    $order_sorted=1;
                                endif;
                                if($order=="first"):
                                    $query->orderBy(['created_at'=>SORT_ASC]);
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
                                    $query->orderBy(['username'=>SORT_ASC]);
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
                                            $submit_arr = ProfileDetails::find()->select('profile_id')->where(['param'=>$order])->orderBy(['param_val'=>SORT_DESC])->offset($offset)->asArray()->column();
                                        else:
                                            $submit_arr = ProfileDetails::find()->select('profile_id')->where(['param'=>$order])->orderBy(['param_val'=>SORT_DESC])->asArray()->column();
                                        endif;
                                    else:
                                        if($offset!=0):
                                            $submit_arr = ProfileDetails::find()->select('profile_id')->where(['param'=>$order])->orderBy(['param_val'=>SORT_ASC])->offset($offset)->asArray()->column();  
                                        else:
                                            $submit_arr = ProfileDetails::find()->select('profile_id')->where(['param'=>$order])->orderBy(['param_val'=>SORT_ASC])->asArray()->column();  
                                        endif;
                                    endif;
                                    $query->andWhere(['in','id',$submit_arr])->orderBy(new Expression('FIND_IN_SET (id,:profile_id)'))->addParams([':profile_id'=>implode(",",$submit_arr)]);
                                    
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
            $data_query = ProfileDetails::find()->select('profile_id');            
            $search_params=explode("|",urldecode(Yii::$app->request->get('search-field')));
            $search_succeed=0;
            if(sizeof($search_params) > 1):
            foreach($search_params as $param):
                list($p,$v)=explode("=",$param);                
                //this is used to search based on submit id
                if($p=="form_submit_id"):
                        $data_query->orWhere('profile_id="'.$v.'"');
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
                    if($p=="profile_id"):
                            $data_query->andWhere('profile_id="'.$v.'"');
                        continue;
                    endif;
                    if(count($pgq) > 1):
                        $form_arr[$int_count] = ProfileDetails::find()->select('profile_id')->where(['param'=>$pgq[0]])->andWhere('param_val >="'.$pgq[1].'"')->column();
                        $int_count++;
                        continue;
                    endif;
                    if(count($plq) > 1):
                        $form_arr[$int_count] = ProfileDetails::find()->select('profile_id')->where(['param'=>$plq[0]])->andWhere('param_val <="'.$plq[1].'"')->column();
                        $int_count++;
                        continue;
                    endif;
                    if(count($pg) > 1):
                        $form_arr[$int_count] = ProfileDetails::find()->select('profile_id')->where(['param'=>$pg[0]])->andWhere('param_val >"'.$pg[1].'"')->column();
                        $int_count++;
                        continue;
                    endif;
                    if(count($pl) > 1):
                        $form_arr[$int_count] = ProfileDetails::find()->select('profile_id')->where(['param'=>$pl[0]])->andWhere('param_val <"'.$pl[1].'"')->column();
                        $int_count++;
                        continue;
                    endif;
                    $form_arr[$int_count] = ProfileDetails::find()->select('profile_id')->where(['param'=>$p])->andWhere(['like','param_val',$v])->column();
                    $int_count++;
                endforeach;
                    $form_submit_arr = $form_arr[0];
                    foreach ($form_arr as $form_submit_item):
                        $form_submit_arr = array_intersect($form_submit_arr,$form_submit_item);
                    endforeach;
                    $data_query->where(['IN','profile_id',$form_submit_arr]);
                $search_succeed=1;
            endif;            
            endif;
            if($search_succeed < 1):
                foreach($search_params as $param):
                list($p,$v)=explode("=",$param);                
                //this is used to search based on submit id
                if($p=="article_id"):
                        $data_query->orWhere('profile_id="'.$v.'"');
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
                $data_query = ProfileDetails::find()->select('article_id');            
            endif;
            $search_params=explode("|",urldecode(Yii::$app->request->get('excludes')));
            foreach($search_params as $param):
                list($p,$v)=explode("=",$param);
                $d[] = $v;
                
                if($p=="profile_id"):
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
                $query->with('details')->asArray();
            else:
                $query->with('details')->asArray();
        endif;
         
        if(Yii::$app->request->get('status')==null):
            $query->andWhere('status="10"');
        endif;
        if(Yii::$app->request->get('status')=="0"):
            $query->andWhere('status="0"');
        endif;
        if (Yii::$app->request->get('logged')=="true"):
            $query->andWhere('username="'.Yii::$app->user->identity->username.'"');            
        endif;
	if (Yii::$app->request->get('logged')=="false"):
            $query->andWhere('username<>"'.Yii::$app->user->identity->username.'"');            
        endif;
        if(Yii::$app->request->get('user_id')!=null):
            $user_arr = Users::find()->where(['id'=>Yii::$app->request->get('user_id')])->one();
            if($user_arr!=null):
                $query->andWhere('username="'.$user_arr['username'].'"');
            endif;
        endif;
        if(Yii::$app->request->get('date_stamp')!=null):
            $query->andWhere(['LIKE','created_at',Yii::$app->request->get('date_stamp')]);
        endif;
        if(Yii::$app->request->get('between')!=null):
            list($start_from,$end_at) = explode(",",Yii::$app->request->get('between'));
            if($start_from!=""):
                $query->andWhere('created_at>="'.$start_from.'"');
            endif;
            if($end_at!=""):
                $query->andWhere('created_at<="'.$end_at.'"');
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
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>Yii::$app->request->get('user-widget'),'renderer_type'=>'W'])->one();
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
            $fsubmit=Users::find()->select('id')->where(['usrname'=>Yii::$app->request->get('publisher')])->column();
            $query->andFilterWhere('IN','target_id',$fsubmit);
    endif;
    if(Yii::$app->request->get('logged')=="true"):
            $fsubmit=Users::find()->select('id')->where(['usrname'=>Yii::$app->user->identity->username])->column();
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

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
use common\models\LoginForm;
use common\models\User;
use frontend\models\Domains;

class AccountsController extends Controller{
    //put your code here
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
        
        $form['callback']="";
        
        
        $form['param'] = Yii::$app->request->csrfParam;
        $form['token'] = Yii::$app->request->csrfToken;
        
        $renderer="account/login";
        $view_renderer="accounts/login";
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
        return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$view_file,['form'=>$form,'page'=>$article]);
    }
    
    public function actionSearch(){
        /* 
         * This routine is used to search custom forms within the system. If the return-type is set as AJAX, then the result is just returned
         * If however the return_type is not set, it renders the result based on the theme's view renderer
         * Yii::$app->request->get('search-field') - this is an array of fields to search on in the form post
         * Yii::$app->request->get('form-widget') - this is the form-widget to be called to render the results
         * Yii::$app->request->get('return-type') - if set to json then the renderer is not called after the result but rather an ajax is returned
         * Yii::$app->request->get('exclude') - this is the set of records to exclude from the search
         * If no custom widget is used, the the User Index widget is used
         */
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
                                if($order=="random"):
                                    $query->orderBy(new Expression('rand()'));
                                endif;
                                if($order=="last"):
                                    $query->orderBy(['date_stamp'=>SORT_DESC]);
                                endif;
                                if($order=="first"):
                                    $query->orderBy(['date_stamp'=>SORT_ASC]);
                                endif;
                                if($order=="views"):
                                    $query->orderBy(['no_of_views'=>SORT_DESC]);
                                endif;
                                if($order=="user"):
                                    $query->orderBy(['usrname'=>SORT_ASC]);
                                endif;
                                if($order=="rating"):
                                    $query->orderBy(['rating'=>SORT_DESC]);
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
                //this is used to search based on submit id
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
        
        if(Yii::$app->request->get('return-type')=="json"):
            if(Yii::$app->request->get('search-field')!=null):
                $data = $query->with('data','file')->asArray()->where(['IN','id',$data_query])->andWhere('form_id="'.$article->id.'"')->all();
            else:
                $data = $query->with('data','file')->asArray()->where(['form_id'=>$article->id])->all();
            endif;
            return \yii\helpers\Json::encode($data);
            //return Yii::$app->api->sendSuccessResponse($data);
        endif;
        if(Yii::$app->request->get('search-field')!=null):
                $query->with('data','file')->asArray()->where(['IN','id',$data_query])->andWhere('form_id="'.$article->id.'"');
            else:
                $query->with('data','file')->asArray()->where(['form_id'=>$article->id]);
        endif;
        if(Yii::$app->request->get('published')==null):
            $query->andWhere('published="1"');
        endif;
        if(Yii::$app->request->get('published')=="0"):
            $query->andWhere('published="0"');
        endif;
        if(Yii::$app->request->get('return-type')=="count"):
            return \yii\helpers\Json::encode($query->count());
        endif;
                $page['records'] = $query->all();
                
        
        
        
                    if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $render = 
                        //since we may get the widget we want to use to display the result
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>Yii::$app->request->get('form-widget'),'renderer_type'=>'I'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'],['page'=>$page,'app'=>Yii::$app]);
                            return $this->renderPartial('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
            return $this->renderPartial('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/forms/form-view-list',$page);
    }
}

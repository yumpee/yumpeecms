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
 * Description of ServicesController
 *
 * @author Peter
 */


use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\ServicesIncoming;
use backend\models\ServicesOutgoing;
use backend\models\BackEndMenus;
use backend\models\BackEndMenuRole;
use backend\models\Settings;

class ServicesController extends Controller{
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

    public function actionIncoming(){
        $page=[];
        $page['id']= Yii::$app->request->get('id',null);
        if($page['id']!=null):
                $page['rs'] = ServicesIncoming::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = new ServicesIncoming();
        endif;
        $page['records'] = ServicesIncoming::find()->orderBy('name')->all();
        return $this->render('incoming',$page);
    }
    public function actionOutgoing(){
        $page=[];
        $page['id']= Yii::$app->request->get('id',null);
        if($page['id']!=null):
                $page['rs'] = ServicesOutgoing::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = new ServicesOutgoing();
        endif;
        $page['records'] = ServicesOutgoing::find()->orderBy('name')->all();
        return $this->render('outgoing',$page);
    }
    public function actionResource(){
        $page=[];
        
        $page['records']=[];
        $page['resource_type']="profile";
        return $this->render('resource',$page);
    }
    public function actionEmulator(){
        $page=[];
        
        if(Yii::$app->request->post("authentication")=="0"):
            $ch = curl_init(Yii::$app->request->post('url')); 
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, Yii::$app->request->post("ptype"));                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, Yii::$app->request->post("body"));                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if(Yii::$app->request->post("header")!=""):
                    $post_arr = explode(",",Yii::$app->request->post("header"));
                    $nr = array();
                    $nr = $post_arr;
                    $count = count($post_arr);
                    if(Yii::$app->request->post("format_type")=="json"):
                        $nr[$count + 1]='Content-Type: application/json';                    
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $nr );
                    endif;                   
            else:
                    if(Yii::$app->request->post("format_type")=="json"):
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen(Yii::$app->request->post("body")), ) );
                    endif;
            endif;
            $result = curl_exec($ch);
            if(curl_exec($ch) === false)
            {
                return 'Curl error: ' . curl_error($ch);
            }
            else
            {
                return $result;
            }
        endif;
        if(Yii::$app->request->post("authentication")=="Basic"):                
                $encoded_credentials= base64_encode(Yii::$app->request->post('client_id').":".Yii::$app->request->post('client_key'));                 
                $ch = curl_init(Yii::$app->request->post('url')); 
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, Yii::$app->request->post("ptype"));   
                curl_setopt($ch, CURLOPT_POST,true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_POSTFIELDS, Yii::$app->request->post("body"));
                if(Yii::$app->request->post("use_passwd")=="on"):
                    curl_setopt($ch, CURLOPT_USERPWD,Yii::$app->request->post('client_id').":".Yii::$app->request->post('client_key'));
                endif;
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
                if(Yii::$app->request->post("format_type")=="json"):
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                    'Content-Type: application/json',                                                                                
                    'Content-Length: ' . strlen(Yii::$app->request->post("body")),
                    'Authorization: Basic '.$encoded_credentials,
                )                                                                       
                ); 
                endif;
                if(Yii::$app->request->post("header")!=""):
                    $post_arr = explode(",",Yii::$app->request->post("header"));
                    $nr = array();
                    $nr = $post_arr;
                    $count = count($post_arr);
                    if(Yii::$app->request->post("format_type")=="json"):
                        $nr[$count + 1]='Content-Type: application/json';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $nr);
                    endif;
                else:
                    if(Yii::$app->request->post("format_type")=="json"):
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen(Yii::$app->request->post("body")), ) );
                    endif;
                    
                endif;
                
                $result = curl_exec($ch);
                if(curl_exec($ch) === false):
                        return 'Curl error: ' . curl_error($ch);
                    else:
                        return $result;
                endif;
        endif;
        
        return $this->render('emulator',$page);
    }
    public function actionLogs(){
        $page=[];
        $page['frontend'] = file_get_contents("../../frontend/runtime/logs/app.log");        
        return $this->render('logs',$page);
    }
    public function actionOutgoingSave(){
        $model = ServicesOutgoing::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->setAttribute('name',Yii::$app->request->post('name'));
                $model->setAttribute('client_id',Yii::$app->request->post('client_id'));
                $model->setAttribute('client_key',Yii::$app->request->post('client_key'));
                $model->setAttribute('header',Yii::$app->request->post('header'));
                $config = array('authentication'=>Yii::$app->request->post('authentication'),
                    'encryption'=>Yii::$app->request->post('encryption'),'auth_url'=>Yii::$app->request->post('auth_url'),
                    'body_content'=>Yii::$app->request->post('body_content'),'authenticate_method'=>Yii::$app->request->post('authenticate_method'),
                    'bearer_token'=>Yii::$app->request->post('bearer_token')   
                        );
                $model->setAttribute('config',json_encode($config));
                $model->save();
                return "Web service successfully updated";
            else:
                $model =  new ServicesOutgoing();
                $model->setAttribute('id',md5(date('YmHid').rand(1000,10000)));
                $model->setAttribute('name',Yii::$app->request->post('name'));
                $model->setAttribute('client_id',Yii::$app->request->post('client_id'));
                $model->setAttribute('client_key',Yii::$app->request->post('client_key'));
                $model->setAttribute('header',Yii::$app->request->post('header'));
                $config = array('authentication'=>Yii::$app->request->post('authentication'),
                    'encryption'=>Yii::$app->request->post('encryption'),'auth_url'=>Yii::$app->request->post('auth_url'),
                    'body_content'=>Yii::$app->request->post('body_content'),'authenticate_method'=>Yii::$app->request->post('authenticate_method'),
                     'bearer_token'=>Yii::$app->request->post('bearer_token')   
                      );
                $model->setAttribute('config',json_encode($config));
                $model->save();
                return "New web service created";
            endif;
    }
    public function actionIncomingSave(){
        $model = ServicesIncoming::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                
                $model->setAttribute('name',Yii::$app->request->post('name'));
                $model->setAttribute('client_id',Yii::$app->request->post('client_id'));
                $model->setAttribute('client_key',Yii::$app->request->post('client_key'));
                $model->setAttribute('ip_address',Yii::$app->request->post('ip_address'));
                $model->setAttribute('rate_limit',Yii::$app->request->post('rate_limit'));
                $model->save();
                return "Web service successfully updated";
            else:
                $model =  new ServicesIncoming();
                $model->setAttribute('id',md5(date('YmHid').rand(1000,10000)));
                $model->setAttribute('name',Yii::$app->request->post('name'));
                $model->setAttribute('client_id',Yii::$app->request->post('client_id'));
                $model->setAttribute('client_key',Yii::$app->request->post('client_key'));
                $model->setAttribute('ip_address',Yii::$app->request->post('ip_address'));
                $model->setAttribute('rate_limit',Yii::$app->request->post('rate_limit'));
                $model->save();
                return "New service created";
            endif;
    }
    public function actionOutgoingDelete(){
        ServicesOutgoing::deleteAll(['id'=>Yii::$app->request->get('id')]);
    }
    public function actionIncomingDelete(){
        ServicesIncoming::deleteAll(['id'=>Yii::$app->request->get('id')]);
    }
}

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

class ServicesController extends Controller{
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
                    $nr[$count + 1]='Content-Type: application/json';                    
                   curl_setopt($ch, CURLOPT_HTTPHEADER, $nr );
                  //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen(Yii::$app->request->post("body")),'NETOAPI_USERNAME:NetoAPI','Accept:application/json','NETOAPI_KEY:o1lZClvfWa4jePs7SZ4bT5LbBDD8BYm4','NETOAPI_ACTION:GetOrder'));
            else:
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen(Yii::$app->request->post("body")), ) );
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
                curl_setopt($ch, CURLOPT_POSTFIELDS, Yii::$app->request->post("body"));                                                                  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                    'Content-Type: application/json',                                                                                
                    'Content-Length: ' . strlen(Yii::$app->request->post("body")),
                    'Authorization: Basic '.$encoded_credentials,
                )                                                                       
                ); 
                if(Yii::$app->request->post("header")!=""):
                    $post_arr = explode(",",Yii::$app->request->post("header"));
                    $nr = array();
                    $nr = $post_arr;
                    $count = count($post_arr);
                    $nr[$count + 1]='Content-Type: application/json';
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $nr);
                else:
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen(Yii::$app->request->post("body")), ) );
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

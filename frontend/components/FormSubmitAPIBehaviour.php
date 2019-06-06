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
namespace frontend\components;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use Yii;
use backend\models\ServicesOutgoing;

class FormSubmitAPIBehaviour extends Behavior{
    public function events()
    {
        return [
            // after find event
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',            
        ];
    }
    
    public function connect($webhook,$post){
        $web_return=[];
        
        $pattern = "/{yumpee_hook}(.*?){\/yumpee_hook}/";  //use this to capture form elements submitted
        $pattern_setting= "/{yumpee_setting}(.*?){\/yumpee_setting}/"; //use this to capture settings value in the settings page
        $pattern_record= "/{yumpee_record}(.*?){\/yumpee_record}/"; //use this for the client id and secret   
        $json_data = preg_replace_callback($pattern,function ($matches) {
                            $request = Yii::$app->request;
                            $replacer="";
                            $replacer=$request->getBodyParam($matches[1]);
                            return $replacer;
                    },$webhook->json_data);
                    
            $json_data = preg_replace_callback($pattern_setting,function ($matches) {
                            $replacer = ContentBuilder::getSetting($matches[1]);                            
                            return $replacer;
                    },$json_data);
                    
            $web_endpoint = preg_replace_callback($pattern,function ($matches) {
                            $request = Yii::$app->request;
                            $replacer="";
                            $replacer=$request->getBodyParam($matches[1]);
                            return $replacer;
                    },$webhook->end_point);
                    
            $web_endpoint = preg_replace_callback($pattern_setting,function ($matches) {
                            $replacer = ContentBuilder::getSetting($matches[1]);                            
                            return $replacer;
                    },$web_endpoint);
                    
          
        if($webhook->post_type=="P"):
            $ptype="POST";
        endif;
        if($webhook->post_type=="G"):
            $ptype="GET";
        endif;
        if($webhook->post_type=="T"):
            $ptype="PUT";
        endif;
        if($webhook->post_type=="D"):
            $ptype="DELETE";
        endif;
        $body="";
        $headers="";
                                                                   
        if($webhook->client_profile!==null && $webhook->client_profile!=""):
            $client_obj = ServicesOutgoing::find()->where(['id'=>$webhook->client_profile])->one();
            $config = json_decode($client_obj['config']);
            $client_header = preg_replace_callback($pattern,function ($matches) {
                            $request = Yii::$app->request;
                            $replacer="";
                            $replacer=$request->getBodyParam($matches[1]);
                            return $replacer;
                    },$client_obj["header"]);
                    
            $client_header = preg_replace_callback($pattern_setting,function ($matches) {
                            $replacer = ContentBuilder::getSetting($matches[1]);                            
                            return $replacer;
                    },$client_header);
            if($config->authentication=="none"):
                $ch = curl_init($web_endpoint); 
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $ptype);                                                                     
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);                                                                  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                if($client_header!=""):
                    $post_arr = explode(",",$client_header);
                    $nr = array();
                    $nr = $post_arr;
                    $count = count($post_arr);
                    $nr[$count]='Content-Type: application/json';  
                    $nr[$count + 1]='Content-Length: '. strlen($json_data); 
                   curl_setopt($ch, CURLOPT_HTTPHEADER, $nr );
                   //return $json_data;
                  //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen(Yii::$app->request->post("body")),'NETOAPI_USERNAME:NetoAPI','Accept:application/json','NETOAPI_KEY:o1lZClvfWa4jePs7SZ4bT5LbBDD8BYm4','NETOAPI_ACTION:GetOrder'));
                else:
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($json_data), ) );
                endif;
                $result = curl_exec($ch);
                if(curl_exec($ch) === false):
                        return 'Curl error: ' . curl_error($ch);
                else:
                        return $result;
                endif;
            endif;
            if($config->authentication=="basic"):                
                $encoded_credentials= base64_encode($client_obj['client_id'].":".$client_obj['client_key']); 
                $ch = curl_init($web_endpoint); 
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $ptype);                                                                     
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);                                                                  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                    'Content-Type: application/json',                                                                                
                    'Content-Length: ' . strlen($json_data),
                    'Authorization: Basic '.$encoded_credentials,
                )                                                                       
                );                
                $result = curl_exec($ch);
                if(curl_exec($ch) === false):
                        return 'Curl error: ' . curl_error($ch);
                    else:
                        return $result;
                endif;
            endif;
            
            if($config->authentication=="bearer"):
                //we need to authenticate first and then get a bearer token
                
                if($config->auth_url==""):
                    return 'Empty Auth URL';
                endif;                
                $ch = curl_init($config->auth_url); //we set the authentication URL                
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $config->authenticate_method);
                if($config->body_content!=""):
                    $body = preg_replace_callback($pattern_record,function ($matches) use ($client_obj){
                                    if($matches[1]=="client_id"):
                                        return $client_obj['client_id'];
                                    endif;
                                    if($matches[1]=="client_key"):
                                        return $client_obj['client_key'];
                                    endif;
                    },$config->body_content);                   
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $body); 
                endif;
                
                
                if(trim($client_header)!=""):
                    $headers = preg_replace_callback($pattern_record,function ($matches) use($client_obj){
                                    if($matches[1]=="client_id"):
                                        return $client_obj['client_id'];
                                    endif;
                                    if($matches[1]=="client_key"):
                                        return $client_obj['client_key'];
                                    endif;
                    },$client_header); 
                    
                    $headers = preg_replace_callback($pattern_setting,function ($matches) {
                            $replacer = ContentBuilder::getSetting($matches[1]);                            
                            return $replacer;
                    },$headers);
                    
                    $headers = preg_replace_callback($pattern,function ($matches) {
                            $request = Yii::$app->request;
                            $replacer="";
                            $replacer=$request->getBodyParam($matches[1]);
                            return $replacer;
                    },$headers);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($body), $headers) );
                else:
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($body), ) );
                endif;
                curl_setopt($ch, CURLOPT_POSTFIELDS,$body); 
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result_arr = curl_exec($ch);
                //return $body;
                if(curl_exec($ch) === false):
                        return 'Curl error: '.curl_error($ch);
                    else:
                                                
                        $response = json_decode($result_arr);                        
                        if($config->bearer_token!=""):
                            if(isset($response->{$config->bearer_token})):
                                $token = implode($response->{$config->bearer_token});
                            else:
                                $token = $result_arr;
                            endif;
                        else:                            
                            $token = $result_arr;
                        endif;
                    //return $token;
                    $chu = curl_init($web_endpoint);
                    curl_setopt($chu, CURLOPT_HTTPHEADER, array(                                                                          
                    'Content-Type: application/json',                                                                                
                    'Content-Length: ' . strlen($json_data),
                    'Authorization: Bearer '.$token,
                    )                                                                       
                    );
                    curl_setopt($chu, CURLOPT_CUSTOMREQUEST, $ptype);                                                                     
                    curl_setopt($chu, CURLOPT_POSTFIELDS, $json_data);                                                                  
                    curl_setopt($chu, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($chu); 
                    return $result;
                endif;
            endif;
            
              if($config->authentication=="oauth"):
                //we need to authenticate first and then get an authorization token
                
                if($config->auth_url==""):
                    return 'Empty Auth URL';
                endif;                
                $ch = curl_init($config->auth_url); //we set the authentication URL                
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $config->authenticate_method);
                if($config->body_content!=""):
                    $body = preg_replace_callback($pattern_record,function ($matches) use ($client_obj){
                                    if($matches[1]=="client_id"):
                                        return $client_obj['client_id'];
                                    endif;
                                    if($matches[1]=="client_key"):
                                        return $client_obj['client_key'];
                                    endif;
                    },$config->body_content);                   
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $body); 
                endif;
                
                
                if(trim($client_obj['header'])!=""):
                    $headers = preg_replace_callback($pattern_record,function ($matches) use($client_obj){
                                    if($matches[1]=="client_id"):
                                        return $client_obj['client_id'];
                                    endif;
                                    if($matches[1]=="client_key"):
                                        return $client_obj['client_key'];
                                    endif;
                    },$client_obj['header']);  
                    $headers = preg_replace_callback($pattern_setting,function ($matches) {
                            $replacer = ContentBuilder::getSetting($matches[1]);                            
                            return $replacer;
                    },$headers);
                    
                    $headers = preg_replace_callback($pattern,function ($matches) {
                            $request = Yii::$app->request;
                            $replacer="";
                            $replacer=$request->getBodyParam($matches[1]);
                            return $replacer;
                    },$headers);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($body), $headers) );
                else:
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($body), ) );
                endif;
                curl_setopt($ch, CURLOPT_POSTFIELDS,$body); 
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result_arr = curl_exec($ch);
                //return $body;
                if(curl_exec($ch) === false):
                        return 'Curl error: '.curl_error($ch);
                    else:
                                                
                        $response = json_decode($result_arr);                        
                        if($config->bearer_token!=""):
                            if(isset($response->{$config->bearer_token})):
                                $token = implode($response->{$config->bearer_token});
                            else:
                                $token = $result_arr;
                            endif;
                        else:                            
                            $token = $result_arr;
                        endif;
                    //return $token;
                    //we need to insert this token into the json data    
                    $json_data = str_replace("yumpee_authorize_token",$token,$json_data) ;                     
                    $chu = curl_init($web_endpoint);
                    curl_setopt($chu, CURLOPT_HTTPHEADER, array(                                                                          
                    'Content-Type: application/json',                                                                                
                    'Content-Length: ' . strlen($json_data),
                    'Authorization: Bearer '.$token,
                    )                                                                       
                    );
                    curl_setopt($chu, CURLOPT_CUSTOMREQUEST, $ptype);                                                                     
                    curl_setopt($chu, CURLOPT_POSTFIELDS, $json_data);                                                                  
                    curl_setopt($chu, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($chu); 
                    return $result;
                endif;
            endif;
        endif;
           
        $ch = curl_init($web_endpoint); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $ptype);                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if(curl_exec($ch) === false)
        {
            return 'Curl error: ' . curl_error($ch);
        }
        else
        {
            return $result;
        }
        
    }
}

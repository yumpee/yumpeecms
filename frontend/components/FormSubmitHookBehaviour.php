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
use backend\models\WebHook;
use backend\models\Forms;
use frontend\components\ContentBuilder;
use frontend\models\FormSubmit;
use frontend\models\FormData;

class FormSubmitHookBehaviour extends Behavior{
    public function events()
    {
        return [
            // after find event
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',            
        ];
    }
    public function afterSave(){
        $form_id = $this->owner->form_id;
        $webhooks= WebHook::find()->where(['form_id'=>$form_id])->andWhere('hook_type="I"')->all();
        $from_email = ContentBuilder::getSetting("smtp_sender_email");
        $pattern = "/{yumpee_hook}(.*?){\/yumpee_hook}/";  //use this to capture form elements submitted
        $pattern_setting= "/{yumpee_setting}(.*?){\/yumpee_setting}/"; //use this to capture settings value in the settings page
        if(Yii::$app->request->post("yumpee_ignore_hook")=="true"):
            return;
        endif;
        
        foreach($webhooks as $webhook):
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
                    
                    //we now need to decipher the endpoint
            $endpoint_obj = explode("/",$web_endpoint);
            $usrname=(Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->username);
            if(count($endpoint_obj) > 2 && $endpoint_obj[0]=="forms" ):
                $form_id_obj = Forms::find()->where(['name'=>$endpoint_obj[1]])->one();
                if($endpoint_obj[2]!=null && $form_id_obj!=null):               
                
                switch($endpoint_obj[2]):
                    case "create":
                            $form_submit = new FormSubmit();
                            if($form_id_obj->published=="Y"):
                                $form_submit->setAttribute('published',"1");
                            else:
                                $form_submit->setAttribute('published',"0");
                            endif;
                            $form_submit->setAttribute("form_id",$form_id_obj->id);
                            $form_submit->setAttribute("usrname",$usrname);
                            $form_submit->setAttribute("token",Yii::$app->request->getBodyParam("_csrf-frontend"));
                            $form_submit->setAttribute("date_stamp",date("Y-m-d H:i:s"));
                            $form_submit->setAttribute("ip_address",Yii::$app->getRequest()->getUserIP());
                            $form_submit->setAttribute("url",$usrname.md5(Yii::$app->getRequest()->getUserIP().date('YmdHiis')));
                            $form_submit->save();
                            $id = $form_submit->id;
                            //we map the json data
                            
                            $jsonArray = json_decode($json_data,true);
                            foreach($jsonArray as $key => $value):
                            
                                if($value<>""):
                                    $form_data = new FormData();
                                    $form_data->setAttribute("form_submit_id",$id);
                                    $form_data->setAttribute("param",$key);
                                    $form_data->setAttribute("param_val",$value);
                                    $form_data->save();
                                endif;
                            endforeach;
                    break;
                    case "update":
                        if($endpoint_obj[3]!=null):
                            $jsonArray = json_decode($json_data,true);
                            foreach($jsonArray as $key => $value):
                            
                                if($value<>""):
                                    $form_data = FormData::find()->where(['form_submit_id'=>$endpoint_obj[3]])->andWhere('param="'.$key.'"')->one();
                                    if($form_data!=null):
                                        $form_data->setAttribute('param_val',$value);
                                        $form_data->save();
                                    else:
                                        $form_data = new FormData();
                                        $form_data->setAttribute("form_submit_id",$endpoint_obj[3]);
                                        $form_data->setAttribute("param",$key);
                                        $form_data->setAttribute("param_val",$value);
                                        $form_data->save();
                                    endif;
                                    
                                endif;
                            endforeach;
                        endif;
                        
                    break;
                    case "delete":
                        if($endpoint_obj[3]!=null):
                            FormSubmit::deleteAll(['id'=>$endpoint_obj[3]]);
                            FormData::deleteAll(['form_submit_id'=>$endpoint_obj[3]]);
                        endif;
                    break;
                endswitch;
                endif;
            endif;
            
        endforeach;
        
        
        
    }
}

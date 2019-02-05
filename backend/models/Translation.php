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
namespace backend\models;
use Yii;
use backend\models\TranslationMessage;

class Translation extends \yii\db\ActiveRecord
{
   public static function tableName()
    {
        return 'sourcemessage';
    }
    
    public static function getTranslation($id){
        return TranslationMessage::find()->where(['id'=>$id])->one();
        
    }
    public static function saveTranslation(){
        $session = Yii::$app->session;
        $mydatabase = $session['mydatabase'];
        $query = new \yii\db\Query;
        $records = Yii::$app->db->createCommand("SELECT id FROM sourcemessage WHERE id='".Yii::$app->request->post('id')."'")->queryAll();
        $rec=1;
        
        if(count($records)>0){               
            $id = Yii::$app->request->post("id");            
            Yii::$app->db->createCommand()->update('sourcemessage',[  
               'category'=>Yii::$app->request->post("category"),
               'message'=>Yii::$app->request->post("message")
           ],'id="'.$id.'"')->execute();
            
           //we save into the message here
            if(Yii::$app->request->post("translation")==""):
                $translation = Yii::$app->request->post("message");
            else:
                $translation = Yii::$app->request->post("translation");
            endif;
            $query = new \yii\db\Query;
            $records = Yii::$app->db->createCommand("SELECT id FROM message WHERE id='".$id."'")->queryAll();
            if(count($records)>0):                
                Yii::$app->db->createCommand()->update('message',[ 
               'translation'=>$translation,
               'language'=>Yii::$app->request->post("language")
                ],'id="'.$id.'"')->execute();
             else:
                Yii::$app->db->createCommand()->insert('message',[ 
               'id'=>$id,
               'translation'=>$translation,
               'language'=>Yii::$app->request->post("language")
                ])->execute();
            endif;
            return "Updates successfully made";
        }else{           
            
           Yii::$app->db->createCommand()->insert('sourcemessage',[
               'category'=>Yii::$app->request->post("category"),
               'message'=>Yii::$app->request->post("message") 
           ])->execute();
           
           //we save into the message here
           $id = Yii::$app->db->getLastInsertID();
           
            if(Yii::$app->request->post("translation")==""):
                $translation = Yii::$app->request->post("message");
            else:
                $translation = Yii::$app->request->post("translation");
            endif;
            $query = new \yii\db\Query;
            $records = Yii::$app->db->createCommand("SELECT id FROM message WHERE id='".$id."'")->queryAll();
            if(count($records)>0):                
                Yii::$app->db->createCommand()->update('message',[ 
               'translation'=>$translation,
               'language'=>Yii::$app->request->post("language")
                ],'id="'.$id.'"')->execute();
             else:
                Yii::$app->db->createCommand()->insert('message',[ 
               'id'=>$id,
               'translation'=>$translation,
               'language'=>Yii::$app->request->post("language")
                ])->execute();
            endif;
           
            return "New translation successfully created";
        }
    }
}

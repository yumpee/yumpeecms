<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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

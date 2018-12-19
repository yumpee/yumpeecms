<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace backend\models;
use Yii;
class Job extends \yii\db\ActiveRecord
{
   public static function tableName()
    {
        return 'tbl_job_type';
    }
    
    public static function saveGroup(){
        $session = Yii::$app->session;
        $query = new \yii\db\Query;
        $records = Yii::$app->db->createCommand("SELECT id FROM tbl_job_type  WHERE id='".Yii::$app->request->post('id')."'")->queryAll();
        $rec=1;
        $published=0;
        
        if(count($records)>0){               
            $id = Yii::$app->request->post("id");            
            Yii::$app->db->createCommand()->update('tbl_job_type',[  
               'name'=>Yii::$app->request->post("name")
           ],'id="'.$id.'"')->execute();
            
            return "Updates successfully made";
        }else{           
            
           Yii::$app->db->createCommand()->insert('tbl_job_type',[
               'name'=>Yii::$app->request->post("name")
           ])->execute();
           
            return "New job type successfully created";
        }
    }
    
    
}
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace backend\models;
use Yii;
class Categories extends \yii\db\ActiveRecord
{
   public static function tableName()
    {
        return 'tbl_categories';
    }
    
    public static function saveGroup(){
        $records = Categories::find()->where(['id'=>Yii::$app->request->post('id')]);
        if($records !=null){       
            $records->setAttribute('name',Yii::$app->request->post("name"));
            $records->save();
            return "Updates successfully made";
        }else{           
           $c = new Categories();
           $c->setAttribute('name',Yii::$app->request->post("name"));
           $c->save();
            return "New category successfully created";
        }
    }
    
    public static function getList(){
        return Categories::find()->orderBy('name')->all();
    }
}
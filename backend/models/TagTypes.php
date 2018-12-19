<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace backend\models;
use Yii;

class TagTypes extends \yii\db\ActiveRecord
{
   public static function tableName()
    {
        return 'tbl_tag_types';
    }
    
    public static function getSelectedTags(){
        $id = Yii::$app->request->get("id");
        $session = Yii::$app->session;
        $mydatabase = $session['mydatabase'];
        $query = new \yii\db\Query;
        return Yii::$app->db->createCommand("SELECT x.tags_id as id,y.name as name FROM tbl_tags_index x,tbl_tags y WHERE x.tags_id=y.id AND x.index_id='$id'")->queryAll();
    }
}

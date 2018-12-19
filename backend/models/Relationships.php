<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

/**
 * Description of Relationships
 *
 * @author Peter
 */
use Yii;
class Relationships extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName(){
        return 'tbl_relationships';
    }
    public function getRelationCount(){
        return $this->hasMany(RelationshipDetails::className(),['relationship_id'=>'id'])->count();
    }
    public function getSource(){
        return $this->hasOne(Forms::className(),['name'=>'source_id']);
    }
    public function getTarget(){
        return $this->hasOne(Forms::className(),['name'=>'target_id']);
    }
}

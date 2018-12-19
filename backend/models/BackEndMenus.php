<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

use Yii;
class BackEndMenus extends \yii\db\ActiveRecord {
    public static function tableName(){
        return 'tbl_backend_menu';
    }
    public function getParent(){
        return $this->hasOne(BackEndMenus::classname(),['id'=>'parent_id']);
    }
    public function getName(){
        if($this->parent !=null):
            return $this->label."(".$this->parent->name.")";
        else:
            return $this->label;
        endif;
    }
    public function getChild(){
        return $this->hasMany(BackEndMenus::className(),['parent_id'=>'id']);
    }
    public function getAssignedChild(){
        $role_obj = BackEndMenuRole::find()->select('menu_id')->where(['role_id'=>Yii::$app->user->identity->role_id])->column();
        return $this->hasMany(BackEndMenus::className(),['parent_id'=>'id'])->where(['IN','id',$role_obj]);
    }
}

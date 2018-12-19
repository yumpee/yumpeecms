<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace frontend\models;

use Yii;

class FormData extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tbl_form_data';
    } 
    public function getBaseURL(){
        return Yii::$app->request->getBaseUrl();
    }
    public function getElement(){
        return $this->hasOne(\backend\models\ClassElement::className(),['name'=>'param']);
    }
    public function getProperty(){
        return $this->hasOne(\backend\models\ClassAttributes::className(),['name'=>'param']);
    }
    public function getElementVal(){
        return $this->hasOne(\backend\models\ClassElement::className(),['name'=>'param_val']);
    }
    public function getPropertyVal(){
        return $this->hasOne(\backend\models\ClassAttributes::className(),['name'=>'param_val']);
    }
    public function getSetup(){
        return $this->hasOne(\backend\models\ClassSetup::className(),['name'=>'param']);
    }
    public function getSetupVal(){
        return $this->hasOne(\backend\models\ClassSetup::className(),['name'=>'param_val']);
    }
    
}

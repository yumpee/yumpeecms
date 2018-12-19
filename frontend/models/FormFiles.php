<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
namespace frontend\models;

use Yii;

class FormFiles extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tbl_form_files';
    } 
    public function getBaseURL(){
        return Yii::$app->request->getBaseUrl();
    }

}
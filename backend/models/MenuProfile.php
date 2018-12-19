<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

use Yii;


class MenuProfile extends \yii\db\ActiveRecord
{
   
   public static function tableName()
    {
        return 'tbl_menu';
    }
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['id'],'safe'],
            [['name'], 'string', 'max' => 50],
            [['description'],'string'],
        ];
    }
}
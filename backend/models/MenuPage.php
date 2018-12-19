<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

use Yii;


class MenuPage extends \yii\db\ActiveRecord
{
   
   public static function tableName()
    {
        return 'tbl_menu_page';
    }
    public function rules()
    {
        return [
            [['menu_id','profile'], 'required'],
            [['id'],'safe'],
            [['profile'],'integer'],
            [['menu_id'], 'string', 'max' => 50],
            
        ];
    }
}
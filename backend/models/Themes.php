<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\models;

/**
 * Description of Themes
 *
 * @author Peter
 */
use backend\models\Settings;

class Themes extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName()
    {
        return 'tbl_themes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'is_default','folder'], 'required'],
            [['is_default'], 'integer'],
            [['id','stylesheet','javascript'],'safe'],
            [['name'], 'string', 'max' => 50],
            [['description','header','footer','custom_styles'],'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name of theme',
            'folder'=>'Name of theme folder',
            'is_default' => 'Is Default',
            'description'=>'Theme description'
        ];
    }
    public function getCurrentTheme(){
        //this returns the current theme from the Settings Model class
         $theme = Settings::findOne(['setting_name'=>'current_theme']);
         if($theme['setting_value']!=null):
             return $theme['setting_value'];
         else:
             return '0';
         endif;
         
    }
    
}

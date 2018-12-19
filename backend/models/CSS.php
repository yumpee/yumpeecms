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

class CSS extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName()
    {
        return 'tbl_css';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['id'],'safe'],
            [['name'], 'string', 'max' => 50],
            [['description','css'],'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name of CSS',
            'description'=>'CSS description'
        ];
    }
    
    
}

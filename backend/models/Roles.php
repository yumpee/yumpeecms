<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

/**
 * Description of Themes
 *
 * @author Peter
 */


class Roles extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName()
    {
        return 'tbl_roles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['id','access_type','menu_id','parent_role_id'],'safe'],
            [['name'], 'string', 'max' => 50],
            [['description'],'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name of role',
            'description'=>'Theme description'
        ];
    }
    
    
}
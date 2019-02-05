<?php

/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Project Site : http://www.yumpeecms.com


 * YumpeeCMS is a Content Management and Application Development Framework.
 *  Copyright (C) 2018  Audmaster Technologies, Australia
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.

 */
namespace backend\models;

/**
 * Description of Themes
 *
 * @author Peter
 */
use backend\models\Settings;
use Yii;
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
        
         $theme = Settings::findOne(['setting_name'=>'current_theme']);
         if($theme['setting_value']!=null):
             return $theme['setting_value'];
         else:
             return '0';
         endif;
         
    }
    public function getHasContents(){
        $renderer = $this->id."_".$this->folder;
        return $this->hasOne(Twig::className(),['theme_id'=>'id'])->andWhere('renderer="'.$renderer.'"');
        if(Yii::$app->request->get("reload")=="true"):
            return $this->hasOne(Twig::className(),['theme_id'=>'id'])->andWhere('renderer="'.$renderer.'"');
        else:
            return $this->hasOne(Twig::className(),['renderer'=>'name'])->andWhere('theme_id="'.\frontend\components\ContentBuilder::getSetting("current_theme").'"');;
        endif;
    }
    
}

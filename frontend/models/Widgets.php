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
namespace frontend\models;
use Yii;


class Widgets extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName()
    {
        return 'tbl_widgets';
    }
    public function getRoutes(){
        return $this->hasMany(Templates::className(),['id'=>'page_id'])->viaTable('tbl_page_widget',['widget'=>'short_name'],function ($query) {
        $query->onCondition(['display_order' => '2']);});
    }
    public function getParent(){
        return $this->hasOne(Widgets::className(),['id'=>'parent_id']);
    }
    public function getHasContents(){
        if(Yii::$app->request->get("reload")=="true"):
            return $this->hasOne(Twig::className(),['renderer'=>'short_name'])->andWhere('theme_id="'.Yii::$app->request->get("theme").'"');
        else:
            return $this->hasOne(Twig::className(),['renderer'=>'short_name'])->andWhere('theme_id="'.\frontend\components\ContentBuilder::getSetting("current_theme").'"');;
        endif;
    }
}

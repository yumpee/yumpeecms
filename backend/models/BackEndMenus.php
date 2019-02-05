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

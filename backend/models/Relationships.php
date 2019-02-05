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
 * Description of Relationships
 *
 * @author Peter
 */
use Yii;
class Relationships extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName(){
        return 'tbl_relationships';
    }
    public function getRelationCount(){
        return $this->hasMany(RelationshipDetails::className(),['relationship_id'=>'id'])->count();
    }
    public function getSource(){
        return $this->hasOne(Forms::className(),['name'=>'source_id']);
    }
    public function getTarget(){
        return $this->hasOne(Forms::className(),['name'=>'target_id']);
    }
}

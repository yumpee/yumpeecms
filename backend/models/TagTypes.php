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

class TagTypes extends \yii\db\ActiveRecord
{
   public static function tableName()
    {
        return 'tbl_tag_types';
    }
    
    public static function getSelectedTags(){
        $id = Yii::$app->request->get("id");
        $session = Yii::$app->session;
        $mydatabase = $session['mydatabase'];
        $query = new \yii\db\Query;
        return Yii::$app->db->createCommand("SELECT x.tags_id as id,y.name as name FROM tbl_tags_index x,tbl_tags y WHERE x.tags_id=y.id AND x.index_id='$id'")->queryAll();
    }
}

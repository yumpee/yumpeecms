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
class Skills extends \yii\db\ActiveRecord
{
   public static function tableName()
    {
        return 'tbl_skills';
    }
    
    public static function saveGroup(){
        $session = Yii::$app->session;
        $query = new \yii\db\Query;
        $records = Yii::$app->db->createCommand("SELECT id FROM tbl_skills  WHERE id='".Yii::$app->request->post('id')."'")->queryAll();
        $rec=1;
        $published=0;
        
        if(count($records)>0){               
            $id = Yii::$app->request->post("id");            
            Yii::$app->db->createCommand()->update('tbl_skills',[  
               'name'=>Yii::$app->request->post("name")
           ],'id="'.$id.'"')->execute();
            
            return "Updates successfully made";
        }else{           
            
           Yii::$app->db->createCommand()->insert('tbl_skills',[
               'name'=>Yii::$app->request->post("name")
           ])->execute();
           
            return "New skill successfully created";
        }
    }
    
    
}
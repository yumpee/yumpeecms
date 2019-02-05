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
use backend\models\BlockPage;

class Blocks extends \yii\db\ActiveRecord
{
   public static function tableName()
    {
        return 'tbl_block';
    }
    
    public static function saveBlocks(){
        $records = Blocks::find()->where(['id'=>Yii::$app->request->post('id')])->one();
        $permissions = Yii::$app->request->post("permissions");
        $perm_val="";
            if(!empty($permissions)){
               $counter=0;
                // Loop to store and display values of individual checked checkbox.
                foreach($permissions as $selected){                    
                    $perm_val = $perm_val." ".$selected;       
                }
            }
        if($records !=null){ 
            $records->setAttribute('title',Yii::$app->request->post("title"));
            $records->setAttribute('name',Yii::$app->request->post("name"));
            $records->setAttribute('content',Yii::$app->request->post("content"));
            $records->setAttribute('position',Yii::$app->request->post("position"));
            $records->setAttribute('sort_order',Yii::$app->request->post("sort_order"));
            $records->setAttribute('show_title',Yii::$app->request->post("show_title"));
            $records->setAttribute('title_level',Yii::$app->request->post("title_level"));
            $records->setAttribute('published',Yii::$app->request->post("published"));
            $records->setAttribute('editable',Yii::$app->request->post("editable"));
            $records->setAttribute('permissions',$perm_val);
            $records->setAttribute('master_content','1');
            $records->setAttribute('require_login',Yii::$app->request->post("require_login"));
            $records->setAttribute('widget',Yii::$app->request->post("widget"));
            $records->save();
            $id = Yii::$app->request->post('id');
            BlockPage::deleteAll(['block_id'=>$id]);            
           
           $pages = Yii::$app->request->post("pages");
           
           if(!empty($pages)){
               $counter=0;
                // Loop to store and display values of individual checked checkbox.
                foreach($pages as $selected){
                    $insert_id = md5(date("Hims").$counter);
                    $counter++;
                    $c = new BlockPage();
                    $c->setAttribute('block_id',$id);
                    $c->setAttribute('page_id',$selected);
                    $c->setAttribute('master_content','1');
                    $c->setAttribute('id',$insert_id);
                    $c->save();                
                
                }
                
            }
            
            
            return "Updates successfully made";
        }else{           
           $id = md5(date('Ymdis'));
           $records = new Blocks();
           $records->setAttribute('id',$id);
           $records->setAttribute('title',Yii::$app->request->post("title"));
            $records->setAttribute('name',Yii::$app->request->post("name"));
            $records->setAttribute('content',Yii::$app->request->post("content"));
            $records->setAttribute('position',Yii::$app->request->post("position"));
            $records->setAttribute('sort_order','100');
            $records->setAttribute('show_title',Yii::$app->request->post("show_title"));
            $records->setAttribute('title_level',Yii::$app->request->post("title_level"));
            $records->setAttribute('published',Yii::$app->request->post("published"));
            $records->setAttribute('editable',Yii::$app->request->post("editable"));
            $records->setAttribute('master_content','1');
            $records->setAttribute('permissions',$perm_val);
            $records->setAttribute('require_login',Yii::$app->request->post("require_login"));
            $records->setAttribute('widget',Yii::$app->request->post("widget"));
            $records->save();
           
     
           
           BlockPage::deleteAll(['block_id'=>$id]);
           $pages = Yii::$app->request->post("pages");
           
           //add the page relation to blocks
           if(!empty($pages)){
               $counter=0;
                // Loop to store and display values of individual checked checkbox.
                foreach($pages as $selected){
                    $insert_id = md5(date("Hims").$counter);
                    $counter++;
                    $c = new BlockPage();
                    $c->setAttribute('block_id',$id);
                    $c->setAttribute('page_id',$selected);
                    $c->setAttribute('master_content','1');
                    $c->setAttribute('id',$insert_id);
                    $c->save();      
                }
                
            }
            
           
            return "New block successfully created";
        }
    }
    
    public function getBlockPages(){
        return BlockPage::find()->where(['block_id'=>$this->id])->all();
    }
    
    

}

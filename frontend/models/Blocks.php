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
use backend\models\BlockPage;
use common\components\GUIBehavior;

class Blocks extends \yii\db\ActiveRecord
{
   private $fields = array('content');
    public function behaviors() {
        
        return [
                   
            ['class'=>GUIBehavior::className(),                
                'fields'=>$this->fields,
            ],          
            
        ];
    }
   public static function tableName()
    {
        return 'tbl_block';
    }
    public function getBlockPages(){
        return BlockPage::find()->where(['block_id'=>$this->id])->all();
    }
    
    public function afterFind(){
        //$this->content="Hellos";
        if($this->require_login=="Y"):
            if(Yii::$app->user->isGuest):
                $this->content="";
            elseif (strpos($this->permissions,Yii::$app->user->identity->role_id) === false) :
                    $this->content="";                
            endif;
        endif;
         
         
        return parent::afterFind();
    }
}

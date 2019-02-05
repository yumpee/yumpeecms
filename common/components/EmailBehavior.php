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
namespace common\components;

use yii\db\ActiveRecord;
use yii\db\Expression;
use Yii;
use yii\base\Behavior;
use backend\models\Subscriptions;

class EmailBehavior extends Behavior
{    
   public $fields;
   public $gui_type; //this can be select , checkbox, radio buttons

    public function events()
    {
        return [
            // after find event
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',            
        ];
    }
    public function afterFind(){
        $pattern = "/{yumpee_subscription}(.*?){\/yumpee_subscription}/"; 
        
        foreach($this->owner->fields as $field):
        $content = $this->owner->{$field};
        $content = preg_replace_callback($pattern,function ($matches) {
                            $email_arr = Subscriptions::find()->select('email')->asArray()->column();  
                            return implode(",",$email_arr);
                    },$content); 
                    
        $this->owner->{$field}=$content;  
        endforeach;
      
    }
}
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

namespace frontend\components;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use backend\models\Users;
use backend\models\Blocks;

class BlockComponent extends Widget{
   public $position_type;
   public $pages_id;
   
    public function init()
    {
        parent::init();
        $position_type='left';
        $pages_id = "";
        $nameonly='false';
    }
   public function run(){
       $position_type = $this->position_type;
       $page_id = $this->pages_id;
       $content="";
       $subquery = (new \yii\db\Query())->select('block_id')->from('tbl_block_page')->where(['page_id'=>$page_id]);
       $blocks = Blocks::find()->where(['position'=>$position_type])->andWhere(['IN','id',$subquery])->orderBy('title_level')->all();
       foreach($blocks as $record):
         $content.=$record['title']."<div>".$record['content']."</div>";
       endforeach;
       return $content;
   }
    
    
    
}

?>
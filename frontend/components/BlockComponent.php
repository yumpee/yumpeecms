<?php

/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Each line should be prefixed with  * 
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
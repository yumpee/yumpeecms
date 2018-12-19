<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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

}

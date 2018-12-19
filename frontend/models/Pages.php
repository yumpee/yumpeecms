<?php
namespace frontend\models;
use Yii;
use common\components\GUIBehavior;
use backend\models\Settings;

class Pages extends \backend\models\Pages
{
    private $fields = array('description','meta_description','title');
    public function behaviors() {
        
        return [
                   
            ['class'=>GUIBehavior::className(),                
                'fields'=>$this->fields,
            ],          
            
        ];
    }
	public function getBlocks(){
        return $this->hasMany(\frontend\models\Blocks::className(),['id'=>'block_id'])->viaTable('tbl_block_page',['page_id'=>'id']);
    }	
	public function getSettings(){
		return new Settings();
	}
}
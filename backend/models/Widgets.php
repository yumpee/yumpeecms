<?php
/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
namespace backend\models;
use Yii;

class Widgets extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName()
    {
        return 'tbl_widgets';
    }
    public function getRoutes(){
        return $this->hasMany(Templates::className(),['id'=>'page_id'])->viaTable('tbl_page_widget',['widget'=>'short_name'],function ($query) {
        $query->onCondition(['display_order' => '2']);});
    }
    public function getParent(){
        return $this->hasOne(Widgets::className(),['id'=>'parent_id']);
    }
    public function getHasContents(){
        if(Yii::$app->request->get("reload")=="true"):
            return $this->hasOne(Twig::className(),['renderer'=>'short_name'])->andWhere('theme_id="'.Yii::$app->request->get("theme").'"');
        else:
            return $this->hasOne(Twig::className(),['renderer'=>'short_name'])->andWhere('theme_id="'.\frontend\components\ContentBuilder::getSetting("current_theme").'"');;
        endif;
    }
    
}

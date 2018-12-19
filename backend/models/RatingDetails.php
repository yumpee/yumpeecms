<?php
namespace backend\models;
/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

class RatingDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_rating_details';
    }
    public function getRatedValue(){
        return $this->hasOne(RatingProfileDetails::className(),['id'=>'rating_id']);
        
    }
}
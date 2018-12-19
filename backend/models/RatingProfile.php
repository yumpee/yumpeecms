<?php
namespace backend\models;
/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

class RatingProfile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_rating_profile';
    }
    public function getDetails(){
        return $this->hasMany(RatingProfileDetails::className(),['profile_id'=>'id'])->orderBy(['rating_value'=>SORT_DESC]);
    }
    public function getStarsCount(){
        return $this->hasMany(RatingProfileDetails::className(), ['profile_id' => 'id'])->count();
    }
}
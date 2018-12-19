<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
namespace backend\models;

/**
 * Description of Gallery
 *
 * @author Peter
 */
use Yii;
class FeedbackDetails extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName()
    {
        return 'tbl_feedback_details';
    }
   
}

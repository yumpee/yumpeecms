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
namespace backend\models;

use Yii;
use backend\models\Articles;
use backend\models\FormSubmit;

class RatingProfileDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_rating_profile_details';
    }
    public function getProfile(){
        return $this->hasOne(RatingProfile::className(),['id'=>'profile_id']);
    }
    public static function updateRating($rating_profile,$target_id){
        //select the maximum score available for this profile
        $isForm=0;
        $max_rating_value = (new \yii\db\Query())->SELECT([new \yii\db\Expression('MAX(rating_value) as max_value')])->from('tbl_rating_profile_details')->where(['profile_id'=>$rating_profile])->one();
        $total_star_count = (new \yii\db\Query())->SELECT([new \yii\db\Expression('COUNT(rating_value) as total_star_count')])->from('tbl_rating_profile_details')->where(['profile_id'=>$rating_profile])->one();
        $url_list = Articles::find()->where(['id'=>$target_id])->one();
        if($url_list==null):
            $url_list = FormSubmit::find()->where(['url'=>$target_id])->one();
            $isForm=1;
        endif;
        $subQuery = (new \yii\db\Query())->select('rating_id')->from('tbl_rating_details')->where('target_id="'.$target_id.'"');
        $total_rating_value = (new \yii\db\Query())->SELECT([new \yii\db\Expression('SUM(rating_value) as total')])->from('tbl_rating_profile_details')->where(['IN','id',$subQuery])->one();
        $total_rating_value_count = (new \yii\db\Query())->SELECT([new \yii\db\Expression('COUNT(id) as record_count')])->from('tbl_rating_details')->where('target_id="'.$target_id.'"')->one();
        
        
        if($total_rating_value!=null):
            $expected_sum = $total_rating_value_count['record_count'] * $max_rating_value['max_value'];
            $average_score = ($total_rating_value['total'] / $expected_sum) * $total_star_count['total_star_count'];
            $average_score = round($average_score / 0.5) * 0.5; //we round up to the nearest .5 rating
            if($isForm==0):
                Yii::$app->db->createCommand()->update('tbl_articles',[  
                'rating'=>$average_score
            ],'url="'.$url_list['url'].'"')->execute();
            else:
                Yii::$app->db->createCommand()->update('tbl_form_submit',[  
                'rating'=>$average_score
            ],'url="'.$target_id.'"')->execute();
            endif;
        endif;
    }
    
}
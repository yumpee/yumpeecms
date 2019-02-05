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

/**
 * This is the model class for table "tbl_comments".
 *
 * @property integer $id
 * @property string $target_id
 * @property string $comment_type
 * @property string $author
 * @property string $commentor
 * @property string $comment
 * @property string $date_commented
 * @property string $status
 * @property string $ip_address
 * @property string $email
 * @property string $website
 */
class Comments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['target_id', 'comment_type', 'comment', 'ip_address'], 'required'],
            [['comment'], 'string'],
            [['date_commented'], 'safe'],
            [['target_id', 'author', 'commentor'], 'string', 'max' => 50],
            [['comment_type'], 'string', 'max' => 15],
            [['status'], 'string', 'max' => 1],
            [['ip_address'], 'string', 'max' => 20],
            [['email', 'website'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'target_id' => 'Target ID',
            'comment_type' => 'Comment Type',
            'author' => 'Author',
            'commentor' => 'Commentor',
            'comment' => 'Comment',
            'date_commented' => 'Date Commented',
            'status' => 'Status',
            'ip_address' => 'Ip Address',
            'email' => 'Email',
            'website' => 'Website',
        ];
    }
    public function getArticle(){
        return $this->hasOne(Articles::className(),['id'=>'target_id']);
    }
    public function getResponse(){
        return $this->hasMany(Comments::className(),['parent_id'=>'id']);
    }
	public function getPublishDate(){
      //we get the date format type from settings and then use it to return the Publish Date
      $date_obj = Settings::findOne(['setting_name'=>'date_format']);
      return Yii::$app->formatter->asDate($this->date_commented, 'php:'.$date_obj->setting_value);
      
      }
}

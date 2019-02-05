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
namespace frontend\models;

use Yii;
use frontend\models\Users;

/**
 * This is the model class for table "tbl_media".
 *
 * @property string $id
 * @property string $name
 * @property integer $media_type
 * @property string $alt_tag
 * @property integer $author
 * @property string $description
 * @property string $upload_date
 * @property integer $size
 */
class Media extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_media';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'media_type', 'author', 'upload_date', 'size','path'], 'required'],
            [['media_type', 'author', 'size'], 'integer'],
            [['description'], 'string'],
            [['upload_date','upload_file'], 'safe'],
            [['id'], 'string', 'max' => 50],
            [['name', 'alt_tag','caption'], 'string', 'max' => 100],
            [['path'], 'string', 'max' => 200],
        ];
    }
    public function getPublisher(){
        return $this->hasOne(Users::className(),['id'=>'author']);
    }
    public function getUploadDir(){
        //this function is used to get the upload directory defined in Yumpee. check @app/common/config/params-local.php for definitions
        return Yii::getAlias("@image_dir");
    }
    

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'media_type' => 'Media Type',
            'alt_tag' => 'Alt Tag',
            'author' => 'Author',
            'description' => 'Description',
            'upload_date' => 'Upload Date',
            'size' => 'Size',
        ];
    }
}

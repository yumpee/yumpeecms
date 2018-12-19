<?php

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

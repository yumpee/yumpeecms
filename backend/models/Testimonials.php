<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "tbl_testimonials".
 *
 * @property integer $id
 * @property string $name
 * @property string $content
 * @property string $company
 * @property string $author
 * @property string $author_position
 */
class Testimonials extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_testimonials';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'content','author'], 'required'],
            [['content'], 'string'],
            [['id'],'safe'],
            [['name', 'author', 'author_position'], 'string', 'max' => 100],
            [['company'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'content' => 'Content',
            'company' => 'Company',
            'author' => 'Author',
            'author_position' => 'Author Position',
        ];
    }
}

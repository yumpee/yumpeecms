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

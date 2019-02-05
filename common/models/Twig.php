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
namespace common\models;

use Yii;
use frontend\components\ContentBuilder;


class Twig extends \yii\db\ActiveRecord implements \Twig_LoaderInterface
{
   
   public static function tableName()
    {
        return 'tbl_twig';
    }
    public function getCacheKey($name){
        return $name;
    }
    public function isFresh($name,$time){
        if (false === $lastModified = $this->getValue('last_modified', $name)) {
            return false;
        }
        return $lastModified <= $time;

    }
    protected function getValue($column,$name){               
        $a =$this->find()->where(['filename'=>$name])->one();
        //here if the file does not exist in the database then try checking if it exists in the twig folder
        return $a['code'];        
    }
    public function exists($name){
        return $name === $this->getValue('name', $name);

    }
    public function getSourceContext($name){
        if (false === $source = $this->getValue('code', $name)) {
            throw new Twig_Error_Loader(sprintf('Template "%s" does not exist.', $name));
        }
        return new \Twig_Source($source, $name);
    }
}

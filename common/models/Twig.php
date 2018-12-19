<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
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

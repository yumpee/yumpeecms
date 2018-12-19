<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace frontend\models;
use Yii;
use frontend\models\TemplateWidget;

class Templates extends \yii\db\ActiveRecord
{
   public static function tableName()
    {
        return 'tbl_templates';
    }
    public static function getMyWidgets($id,$position=""){
        $query = TemplateWidget::find()->where(['page_id'=>$id]);
        if($position!=""):            
            $query->andWhere('position="'.$position.'"');
        else:
            $query->andWhere('position<>"bottom"')->andWhere('position<>"side"');
        endif;
        return $query->orderBy("display_order")->all();
        
        
    }
}
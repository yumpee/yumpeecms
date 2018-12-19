<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This Class is used to determine the current theme set for the application. If not default system theme, this Class checks to see if the theme files exist and then returns the reference to the renderer
 *
 * @author Peter Odon
 */
namespace frontend\components;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\BaseYii;
use backend\models\Themes;


class ThemeManager {
    //put your code here
    public static function getHeader($defaultViewer){
        $header = $defaultViewer;
        $theme = new Themes();
        $current_theme = $theme->getCurrentTheme();
        $theme_folder_obj = Themes::findOne(['id'=>$current_theme]);
        if($theme_folder_obj!=null):
            $header="@app/themes/".$theme_folder_obj['folder']."/views/layouts/header.php";
        else:
            $header="@app/themes/0/views/layouts/header.php";
        endif;
        if(file_exists(Yii::getAlias($header))):
            return $header;
        else:
            return $defaultViewer;
        endif;
        
    }
    public static function getFooter($defaultViewer){
        $footer = $defaultViewer;
        $theme = new Themes();
        $current_theme = $theme->getCurrentTheme();
        $theme_folder_obj = Themes::findOne(['id'=>$current_theme]);
        if($theme_folder_obj!=null):
            $footer="@app/themes/".$theme_folder_obj['folder']."/views/layouts/footer.php";
        else:
            $footer="@app/themes/0/views/layouts/footer.php";
        endif;
        
        
        if(file_exists(Yii::getAlias($footer))):
            return $footer;
        else:
            return $defaultViewer;
        endif;
    }
    public static function getWidget($defaultViewer,$widget){
        $widget = $defaultViewer;
        $theme = new Themes();
        $current_theme = $theme->getCurrentTheme();
        $theme_folder_obj = Themes::findOne(['id'=>$current_theme]);
        if($theme_folder_obj!=null):
            $widget="@app/themes/".$theme_folder_obj['folder']."/widgets/".$widget.".php";
        else:
            $widget="@app/themes/0/widgets/layouts/footer.php";
        endif;
        
        if(file_exists(Yii::getAlias($widget))):
            return $widget;
        else:
            return $defaultViewer;
        endif;
    }
}

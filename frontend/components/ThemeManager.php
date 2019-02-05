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


namespace frontend\components;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\BaseYii;
use frontend\models\Themes;


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

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

namespace common\components;


class CSVManager{   
    
    public static function save(){        
    
        $model = new Import();

        if ($model->load(Yii::$app->request->post()) ) {

            $model->file = UploadedFile::getInstance($model, 'file');

            if ( $model->file )
                {
                    $time = time();
                    $model->file->saveAs('csv/' .$time. '.' . $model->file->extension);
                    $model->file = 'csv/' .$time. '.' . $model->file->extension;

                     $handle = fopen($model->file, "r");
                     while (($fileop = fgetcsv($handle, 1000, ",")) !== false) 
                     {
                        $name = $fileop[0];
                        $age = $fileop[1];
                        $location = $fileop[2];
                        // print_r($fileop);exit();
                        $sql = "INSERT INTO details(name, age, location) VALUES ('$name', '$age', '$location')";
                        $query = Yii::$app->db->createCommand($sql)->execute();
                     }

                     if ($query) 
                     {
                        echo "data upload successfully";
                     }

                }

            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    
}
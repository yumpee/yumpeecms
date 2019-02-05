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

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use common\models\User;

use common\models\AuthorizationCodes;
use common\models\AccessTokens;

/**
 * Class for common API functions
 */
class Api extends Component
{

    public function sendFailedResponse($message)
    {
        $this->setHeader(400);

        echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $message), JSON_PRETTY_PRINT);

        Yii::$app->end();
    }

    public function sendSuccessResponse($data = false,$additional_info = false)
    {

        $this->setHeader(200);

        $response = [];
        $response['status'] = 1;

        if (is_array($data))
            $response['data'] = $data;

        if ($additional_info) {
            $response = array_merge($response, $additional_info);
        }

        $response = Json::encode($response, JSON_PRETTY_PRINT);


        if (isset($_GET['callback'])) {
            /* this is required for angularjs1.0 client factory API calls to work */
            $response = $_GET['callback'] . "(" . $response . ")";

            echo $response;
        } else {
            echo $response;
        }

        Yii::$app->end();

    }

    protected function setHeader($status)
    {

        $text = $this->_getStatusCodeMessage($status);

        Yii::$app->response->setStatusCode($status, $text);

        $status_header = 'HTTP/1.1 ' . $status . ' ' . $text;
        $content_type = "application/json; charset=utf-8";


        header($status_header);
        header('Content-type: ' . $content_type);
        header('X-Powered-By: ' . "Yumpee CMS <www.yumpeecms.com>");
        header('Access-Control-Allow-Origin:*');


    }

    protected function _getStatusCodeMessage($status)
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

    public function createAuthorizationCode($user_id)
    {
        $model = new AuthorizationCodes;

        $model->code = md5(uniqid());

        $model->expires_at = time() + (60 * 5);

        $model->user_id = $user_id;

        if (isset($_SERVER['HTTP_X_HAIKUJAM_APPLICATION_ID']))
            $app_id = $_SERVER['HTTP_X_HAIKUJAM_APPLICATION_ID'];
        else
            $app_id = null;

        $model->app_id = $app_id;

        $model->created_at = time();

        $model->updated_at = time();

        $model->save(false);

        return ($model);

    }

    public function createAccesstoken($authorization_code)
    {

        $auth_code = AuthorizationCodes::findOne(['code' => $authorization_code]);

        $model = new AccessTokens();

        $model->token = md5(uniqid());

        $model->auth_code = $auth_code->code;

        $model->expires_at = time() + (60 * 60 * 24 * 60); // 60 days

        // $model->expires_at=time()+(60 * 2);// 2 minutes

        $model->user_id = $auth_code->user_id;

        $model->created_at = time();

        $model->updated_at = time();

        $model->save(false);

        return ($model);

    }

    public function refreshAccesstoken($token)
    {
        $access_token = AccessTokens::findOne(['token' => $token]);
        if ($access_token) {

            $access_token->delete();
            $new_access_token = $this->createAccesstoken($access_token->auth_code);
            return ($new_access_token);
        } else {

            Yii::$app->api->sendFailedResponse("Invalid Access token2");
        }
    }

}

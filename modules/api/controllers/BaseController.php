<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;

class BaseController extends Controller
{
    public $user = null;
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;

        $browser_lang = Yii::$app->request->headers->get('accept-language', "ru-RU");
        Yii::$app->language = $this->calculatei18nCode($browser_lang);

        $token = Yii::$app->request->headers->get('Authorization');
        if($token) {
            $token_array = explode(' ', $token);
            if(count($token_array) == 2) {
                $token = $token_array[1];
            }
        }

        $this->user = User::findIdentityByAccessToken($token);

        $device = Yii::$app->request->headers->get('App-Device');
        $build = Yii::$app->request->headers->get('App-Build');

        return parent::beforeAction($action);
    }

    public static function calculatei18nCode($browser_lang) {

        $code = substr($browser_lang, 0, 2);

        return $code;
    }

    public static function getError($model) {
        $errors = $model->getErrors();
        $array = array_shift($errors);
        return count($array) == 0 ? "Ошибка сервера" : $array[0];
    }
}
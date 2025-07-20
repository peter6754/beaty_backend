<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use app\models\Coupon;
use app\models\CouponForm;
use app\models\OrderCoupon;
use app\models\User;
use yii\web\HttpException;

class CouponController extends Controller
{
    public function beforeAction($action)
    {
        if ($action->id === 'register') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionOrder($id)
    {
        if (Yii::$app->user->isGuest) {
            throw new HttpException(401, 'Необходима авторизация');
        }

        $coupon = Coupon::findOne($id);
        if (! $coupon) {
            throw new NotFoundHttpException('Купон не найден');
        }

        $order = new OrderCoupon([
            "user_id" => Yii::$app->user->identity->id,
            "coupon_id" => $coupon->id,
            "price" => $coupon->price,
            "date" => time()
        ]);
        if (! $order->save()) {
            throw new HttpException(500, 'Ошибка при создании заказа');
        }

        // Создание ссылки на оплату через Robokassa
        $mrh_login = Yii::$app->params['robokassa_login'];
        $mrh_pass1 = Yii::$app->params['robokassa_pass1'];

        $order->order_id = (string) $order->id;
        if (!$order->save()) {
            throw new HttpException(500, 'Ошибка при обновлении заказа');
        }

        $signatureString = "$mrh_login:$coupon->price:$order->order_id:$mrh_pass1";
        $crc = md5($signatureString);
        
        // Отладочная информация
        Yii::info("Robokassa signature debug: string='$signatureString', hash='$crc', env=" . YII_ENV, 'payment');

        $resultUrl = urlencode('https://www.beautyms.ru/api/payment/result');
        $successUrl = urlencode('https://www.beautyms.ru/success');
        $failUrl = urlencode('https://www.beautyms.ru/fail');
        $description = urlencode($coupon->name);

        $url = "https://auth.robokassa.ru/Merchant/Index.aspx?MerchantLogin=$mrh_login&OutSum=$coupon->price&InvId=$order->order_id&Description=$description&SignatureValue=$crc&IsTest=" . Yii::$app->params['robokassa_test'] . "&ResultURL=$resultUrl&SuccessURL=$successUrl&FailURL=$failUrl";

        return $this->redirect($url);
    }

    public function actionRegister()
    {
        if (Yii::$app->request->isAjax) {
            // Additional security check since CSRF is disabled
            if (! Yii::$app->request->headers->get('X-Requested-With')) {
                throw new HttpException(400, 'Invalid request');
            }

            $model = new CouponForm();
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $user = Yii::$app->user->isGuest ? User::findOne(["phone" => preg_replace('/[^0-9]/', '', $model->phone)]) : Yii::$app->user->identity;
                if (empty($user)) {

                    $user = new User([
                        "name" => $model->name,
                        "phone" => $model->phone,
                        "password" => Yii::$app->getSecurity()->generatePasswordHash("00000000"),
                        "token" => Yii::$app->getSecurity()->generateRandomString()
                    ]);
                    $user->save();

                    Yii::$app->user->login($user, 3600 * 24 * 30);
                }

                $coupon = Coupon::findOne($model->coupon_id);
                if (! $coupon) {
                    throw new NotFoundHttpException('Купон не найден');
                }

                $order = new OrderCoupon([
                    "user_id" => $user->id,
                    "coupon_id" => $coupon->id,
                    "price" => $coupon->price,
                    "date" => time()
                ]);
                if (! $order->save()) {
                    throw new HttpException(500, 'Ошибка при создании заказа');
                }

                // Создание ссылки на оплату через Robokassa
                $mrh_login = Yii::$app->params['robokassa_login'];
                $mrh_pass1 = Yii::$app->params['robokassa_pass1'];

                $order->order_id = (string) $order->id;
                if (!$order->save()) {
                    throw new HttpException(500, 'Ошибка при обновлении заказа');
                }

                $signatureString = "$mrh_login:$coupon->price:$order->order_id:$mrh_pass1";
                $crc = md5($signatureString);
                
                // Отладочная информация
                Yii::info("Robokassa signature debug: string='$signatureString', hash='$crc', env=" . YII_ENV, 'payment');

                $resultUrl = urlencode('https://www.beautyms.ru/api/payment/result');
                $successUrl = urlencode('https://www.beautyms.ru/success');
                $failUrl = urlencode('https://www.beautyms.ru/fail');
                $description = urlencode($coupon->name);

                $url = "https://auth.robokassa.ru/Merchant/Index.aspx?MerchantLogin=$mrh_login&OutSum=$coupon->price&InvId=$order->order_id&Description=$description&SignatureValue=$crc&IsTest=" . Yii::$app->params['robokassa_test'] . "&ResultURL=$resultUrl&SuccessURL=$successUrl&FailURL=$failUrl";

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'status' => 'success',
                    'redirect' => $url
                ];
            } else {
                Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
        } else {
            throw new HttpException(404, 'Страница не найдена');
        }
    }
}
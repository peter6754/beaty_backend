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
use app\models\RegisterForm;
use app\models\OrderApplication;
use app\models\OrderCoupon;
use app\models\User;
use app\models\Orders;
use yii\web\HttpException;

use YooKassa\Client;

class OrderController extends Controller
{
    public function actionCreate()
    {
        $model = new OrderApplication();
        if ($model->load($this->request->post()) && $model->validate()) {
            $user = Yii::$app->user->isGuest ? User::findOne(["phone" => preg_replace('/[^0-9]/', '', $model->phone)]) : Yii::$app->user->identity;
            if (empty($user)) {

                $user = new User([
                    "name" => $model->name,
                    "phone" => $model->phone,
                    "password" => Yii::$app->getSecurity()->generatePasswordHash("00000000"),
                    "token" => Yii::$app->getSecurity()->generateRandomString()
                ]);
                if (! $user->save()) {
                    throw new HttpException(500, 'Ошибка при создании пользователя');
                }

                Yii::$app->user->login($user, 3600 * 24 * 30);
            }
            $model->user_id = $user->id;

            Yii::$app->session->setFlash('success', "Заявка отправлена в обработку");

            if ($model->order_coupon_id == 0) {
                $model->order_coupon_id = null;
            }

            if ($model->order_coupon_id != null) {
                $coupon = Coupon::findOne($model->order_coupon_id);
                if (! $coupon) {
                    throw new NotFoundHttpException('Купон не найден');
                }

                // Дополнительная проверка купона
                if (empty($coupon->price) || $coupon->price <= 0) {
                    throw new HttpException(500, 'Некорректная цена купона');
                }

                $order = new OrderCoupon([
                    "user_id" => $user->id,
                    "coupon_id" => $coupon->id,
                    "price" => $coupon->price,
                    "date" => time()
                ]);
                if (! $order->save()) {
                    $errors = $order->getErrors();
                    Yii::error('Ошибки при создании заказа купона: '.json_encode($errors));
                    throw new HttpException(500, 'Ошибка при создании заказа купона: '.json_encode($errors));
                }

                $orderId = new Orders(["type" => 1, "user_id" => $user->id, "date" => time(), "price" => $coupon->price, "info" => $order->id]);
                if (! $orderId->save()) {
                    $errors = $orderId->getErrors();
                    Yii::error('Ошибки при создании основного заказа: '.json_encode($errors));
                    throw new HttpException(500, 'Ошибка при создании основного заказа: '.json_encode($errors));
                }

                $mrh_login = "beautyms.ru";
                $mrh_pass1 = "dqqe66GzqsF91TPdLZh7";

                $order->order_id = (string) $orderId->id;
                if (! $order->save()) {
                    $errors = $order->getErrors();
                    Yii::error('Ошибки при обновлении заказа купона: '.json_encode($errors));
                    throw new HttpException(500, 'Ошибка при обновлении заказа купона: '.json_encode($errors));
                }

                $crc = md5("$mrh_login:$order->price:$order->order_id:$mrh_pass1");

                $url = "https://auth.robokassa.ru/Merchant/Index.aspx?MerchantLogin=$mrh_login&OutSum=$coupon->price&InvId=$order->order_id&Description=$coupon->name&SignatureValue=$crc&IsTest=1";

                //                $client = new Client();
//                $client->setAuth(Yii::$app->params['yoomoney_shopid'], Yii::$app->params['yoomoney_secret']);
//                $response = $client->createPayment(
//                    array(
//                        'amount' => array(
//                            'value' => $coupon->price,
//                            'currency' => 'RUB',
//                        ),
//                        'confirmation' => array(
//                            'type' => 'redirect',
//                            'locale' => 'ru_RU',
//                            'return_url' => 'https://beautyms.ru/',
//                        ),
//                        'capture' => true,
//                        'description' => $coupon->name
//                    ),
//                    "C" . $order->id
//                );
//
//                $order->order_id = $response->getId();
//                $order->save();

                $model->price = $order->price;
                $model->order_coupon_id = $order->id;
                if (! $model->save()) {
                    throw new HttpException(500, 'Ошибка при сохранении заявки');
                }
                return $this->redirect($url);
            }

            if (! $model->save()) {
                throw new HttpException(500, 'Ошибка при сохранении заявки');
            }

            return $this->goBack();
        } else {
            Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }

    public function actionValidation()
    {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;

        if (Yii::$app->request->isAjax) {
            $model = new OrderApplication();
            if ($model->load($this->request->post()) && ! $model->validate()) {
            }

            return \yii\widgets\ActiveForm::validate($model);
        } else {
            throw new HttpException(404, 'Страница не найдена');
        }
    }
}
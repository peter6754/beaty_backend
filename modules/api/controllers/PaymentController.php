<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Controller;
use app\models\OrderCoupon;
use app\models\OrderApplication;
use app\models\Master;

class PaymentController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * Обработка Result URL от Robokassa (POST)
     * https://www.beautyms.ru/api/payment/result
     */
    public function actionResult()
    {
        $outSum = Yii::$app->request->post('OutSum');
        $invId = Yii::$app->request->post('InvId');
        $signatureValue = Yii::$app->request->post('SignatureValue');

        // Проверяем подпись
        $mrh_pass2 = Yii::$app->params['robokassa_pass2'];
        
        $crc = md5("$outSum:$invId:$mrh_pass2");
        
        if (strtoupper($signatureValue) === strtoupper($crc)) {
            // Подпись верна, обновляем статус заказа
            $order = OrderCoupon::findOne(['order_id' => $invId]);
            if ($order && $order->status == 0) {
                $order->status = 1; // Оплачено
                $order->save();

                // Обновляем связанную заявку
                $orderApplication = OrderApplication::findOne(['order_coupon_id' => $order->id]);
                if ($orderApplication) {
                    $orderApplication->status = 1; // Оплачено
                    if ($order->coupon && $order->coupon->amount) {
                        $orderApplication->price -= $order->coupon->amount;
                    }
                    $orderApplication->save();
                }
            }

            // Проверяем если это мастер
            $master = Master::findOne(['order_id' => $invId]);
            if ($master && $master->status == 0) {
                $master->status = 1; // Оплачено
                $master->save();
            }

            return "OK$invId";
        }

        return 'bad sign';
    }
}
<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\OrderCoupon;
use app\models\OrderApplication;
use app\models\Master;
use YooKassa\Client;

class OrderController extends Controller
{
    public function actionCoupon()
    {
        $client = new Client();
        $client->setAuth(Yii::$app->params['yoomoney_shopid'], Yii::$app->params['yoomoney_secret']);

        $orders = OrderCoupon::find()->where(["status" => 0])->all();

        foreach ($orders as $order) {
            $response = $client->getPaymentInfo($order->order_id);
            $status = $response->getStatus();
            if($status == "succeeded") {

                $orderApplication = OrderApplication::findOne(["order_coupon_id" => $order->id]);
                if(empty($orderApplication)) {
                    $order->status = 1;
                } else {
                    $order->status = 2;

                    $orderApplication->price -= $order->coupon->amount;
                    $orderApplication->save();
                }
                $order->save();
            } else if($status == "canceled") {
                $orderApplication = OrderApplication::findOne(["order_coupon_id" => $order->id]);
                if(isset($orderApplication)) {
                    $orderApplication->order_coupon_id = 0;
                    $orderApplication->save();
                }

                $order->delete();
            }
        }
        return ExitCode::OK;
    }

    public function actionMaster()
    {
        $client = new Client();
        $client->setAuth(Yii::$app->params['yoomoney_shopid'], Yii::$app->params['yoomoney_secret']);

        $masters = Master::find()->where(["status" => 0])->all();

        foreach ($masters as $master) {
            if($master->order_id) {
                $response = $client->getPaymentInfo($master->order_id);
                $status = $response->getStatus();
                if($status == "succeeded") {
                    $master->status = 1;
                    $master->save();
                } else if($status == "canceled") {
                    $master->status = 4;
                    $master->save();
                }
            }
        }
        return ExitCode::OK;
    }
}

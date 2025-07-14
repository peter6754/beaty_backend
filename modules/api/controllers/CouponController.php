<?php

namespace app\modules\api\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;
use app\models\OrderCoupon;
use app\models\Coupon;

class CouponController extends BaseController
{

    /**
     * @SWG\Get(
     *    path = "/coupon/list",
     *    tags = {"Coupon"},
     *    summary = "Список купонов",
     *    security={{"access_token":{}}},
     *	  @SWG\Response(
     *      response = 200,
     *      description = "Список купонов",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref="#/definitions/Coupon")
     *      ),
     *    ),
     *    @SWG\Response(
     *      response = 400,
     *      description = "Ошибка запроса",
     *      @SWG\Schema(ref = "#/definitions/Result")
     *    ),
     *    @SWG\Response(
     *      response = 401,
     *      description = "Ошибка авторизации",
     *      @SWG\Schema(ref = "#/definitions/Result")
     *    ),
     *)
     * @throws HttpException
     */
    public function actionList()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! $this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }

        $data = [];
        $coupon_query = Coupon::find()->joinWith(["category"])->all();

        foreach ($coupon_query as $coupon) {
            $data[] = [
                "id" => $coupon->id,
                "name" => $coupon->name,
                "category" => isset($coupon->category) ? [
                    "id" => $coupon->category->id,
                    "name" => $coupon->category->name,
                    "color" => $coupon->category->color,
                    "image" => $coupon->category->getImageUrl("x200"),
                ] : null,
                "amount" => (float) $coupon->amount,
                "price" => (float) $coupon->price,
                "description" => $coupon->description,
                "image" => $coupon->getImageUrl("x200"),
            ];
        }

        return $data;
    }

    /**
     * @SWG\Post(
     *    path = "/coupon/order",
     *    tags = {"Coupon"},
     *    summary = "Купить купон",
     *    security={{"access_token":{}}},
     *    @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="coupon_id",
     *                 type="integer",
     *                 description="ID купона",
     *             ),
     *         )
     *     ),
     *	  @SWG\Response(
     *      response = 200,
     *      description = "Ссылка на оплату купорна",
     *      @SWG\Schema(ref = "#/definitions/Result")
     *    ),
     *    @SWG\Response(
     *      response = 400,
     *      description = "Ошибка запроса",
     *      @SWG\Schema(ref = "#/definitions/Result")
     *    ),
     *)
     * @throws HttpException
     */
    public function actionOrder()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! $this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }

        $coupon = Coupon::findOne(Yii::$app->request->post("coupon_id"));
        if (! $coupon) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Купон не найден"];
        }

        $order = new OrderCoupon([
            "user_id" => $this->user->id,
            "coupon_id" => $coupon->id,
            "price" => $coupon->price,
            "date" => time()
        ]);
        $order->save();

        $mrh_login = "beautyms.ru";
        $mrh_pass1 = "ILU1L1gbcaRmVvffO320";

        $crc = md5("$mrh_login:$order->price:$order->id:$mrh_pass1");

        $url = "https://auth.robokassa.ru/Merchant/Index.aspx?MerchantLogin=$mrh_login&OutSum=$coupon->price&InvId=$order->order_id&Description=$coupon->name&SignatureValue=$crc&IsTest=1";

        return ["url" => $url];
    }

    /**
     * @SWG\Get(
     *    path = "/coupon/order-list",
     *    tags = {"Coupon"},
     *    summary = "Список куплных купонов",
     *    security={{"access_token":{}}},
     *	  @SWG\Response(
     *      response = 200,
     *      description = "Список купонов",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref="#/definitions/OrderCoupon")
     *      ),
     *    ),
     *    @SWG\Response(
     *      response = 400,
     *      description = "Ошибка запроса",
     *      @SWG\Schema(ref = "#/definitions/Result")
     *    ),
     *    @SWG\Response(
     *      response = 401,
     *      description = "Ошибка авторизации",
     *      @SWG\Schema(ref = "#/definitions/Result")
     *    ),
     *)
     * @throws HttpException
     */
    public function actionOrderList()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! $this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }

        $data = [];
        $order_query = OrderCoupon::find()->joinWith(["coupon.category"])->where(["user_id" => $this->user->id])->all();

        foreach ($order_query as $order) {
            $data[] = [
                "id" => $order->id,
                "coupon" => isset($order->coupon) ? [
                    "name" => $order->coupon->name,
                    "category" => isset($order->coupon->category) ? [
                        "id" => $order->coupon->category->id,
                        "name" => $order->coupon->category->name,
                        "color" => $order->coupon->category->color,
                        "image" => $order->coupon->category->getImageUrl("x200"),
                    ] : null,
                    "amount" => (float) $order->coupon->amount,
                    "price" => (float) $order->coupon->price,
                    "description" => $order->coupon->description,
                    "image" => $order->coupon->getImageUrl("x200"),
                ] : null,
                "date" => date("Y-m-d\TH:i:sP", $order->date),
                "status" => $order->status
            ];
        }

        return $data;
    }
}

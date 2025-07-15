<?php

namespace app\modules\api\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;
use app\models\OrderCoupon;
use app\models\Coupon;

use OpenApi\Attributes as OA;

class CouponController extends BaseController
{

    #[OA\Get(
        path: "/api/coupon/list",
        summary: "Получение списка купонов",
        tags: ["Coupon"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список купонов",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer"),
                            new OA\Property(property: "name", type: "string"),
                            new OA\Property(property: "category", type: "object", properties: [
                                new OA\Property(property: "id", type: "integer"),
                                new OA\Property(property: "name", type: "string"),
                                new OA\Property(property: "color", type: "string"),
                                new OA\Property(property: "image", type: "string", format: "uri")
                            ]),
                            new OA\Property(property: "amount", type: "number"),
                            new OA\Property(property: "price", type: "number"),
                            new OA\Property(property: "description", type: "string"),
                            new OA\Property(property: "image", type: "string", format: "uri")
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: "Ошибка аутентификации")
        ]
    )]
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

    #[OA\Post(
        path: "/api/coupon/order",
        summary: "Заказ купона",
        tags: ["Coupon"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "coupon_id", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "URL для оплаты",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "url", type: "string", format: "uri")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Ошибка запроса"),
            new OA\Response(response: 401, description: "Ошибка аутентификации")
        ]
    )]
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

    #[OA\Get(
        path: "/api/coupon/order-list",
        summary: "Получение списка заказов купонов",
        tags: ["Coupon"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список заказов купонов",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer"),
                            new OA\Property(property: "coupon", type: "object", properties: [
                                new OA\Property(property: "name", type: "string"),
                                new OA\Property(property: "category", type: "object", properties: [
                                    new OA\Property(property: "id", type: "integer"),
                                    new OA\Property(property: "name", type: "string"),
                                    new OA\Property(property: "color", type: "string"),
                                    new OA\Property(property: "image", type: "string", format: "uri")
                                ]),
                                new OA\Property(property: "amount", type: "number"),
                                new OA\Property(property: "price", type: "number"),
                                new OA\Property(property: "description", type: "string"),
                                new OA\Property(property: "image", type: "string", format: "uri")
                            ]),
                            new OA\Property(property: "date", type: "string", format: "date-time"),
                            new OA\Property(property: "status", type: "integer")
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: "Ошибка аутентификации")
        ]
    )]
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

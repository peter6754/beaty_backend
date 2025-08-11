<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use app\models\Product;
use app\models\Coupon;
use app\models\OrderApplication;
use app\models\OrderCoupon;
use app\models\Orders;

use OpenApi\Attributes as OA;

class OrderController extends BaseController
{
    #[OA\Get(
        path: "/api/order/list/{id}",
        summary: "Получение списка продуктов для заказа",
        tags: ["Order"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список продуктов",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "results", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "integer"),
                                new OA\Property(property: "html", type: "string"),
                                new OA\Property(property: "text", type: "string")
                            ]
                        ))
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Ошибка аутентификации")
        ]
    )]
    public function actionList($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! $this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }

        $products = [];
        $product_query = Product::find()->where(["category_id" => $id])->all();
        foreach ($product_query as $product_item) {
            $products[] = [
                "id" => $product_item->id,
                "html" => '<div style="display: flex"><div style="width: calc(100% - 100px);">'.$product_item->name.'</div><div>'.$product_item->price.' руб.</div></div>',
                "text" => '<div style="display: flex"><div style="width: calc(100% - 100px); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">'.$product_item->name.'</div><div>'.$product_item->price.' руб.</div></div>'
            ];
        }

        return ["results" => $products];
    }

    #[OA\Get(
        path: "/api/order/time",
        summary: "Получение доступного времени для заказа",
        tags: ["Order"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "date", in: "query", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Доступное время",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "results", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "integer"),
                                new OA\Property(property: "text", type: "string")
                            ]
                        ))
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Ошибка аутентификации")
        ]
    )]
    public function actionTime($date = 0)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! $this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }

        $hour = (int) date("H", $date == 0 ? (time() + 7200) : strtotime("Y-m-d", $date));

        $times = [];
        for ($i = $hour; $i <= 23; $i++) {
            $times[] = [
                "id" => $i,
                "text" => $i.':00'
            ];
        }

        return ["results" => $times];
    }

    #[OA\Get(
        path: "/api/order/coupon/{id}",
        summary: "Получение информации о купоне для продукта",
        tags: ["Order"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "HTML с информацией о купоне"
            ),
            new OA\Response(response: 401, description: "Ошибка аутентификации")
        ]
    )]
    public function actionCoupon($id)
    {
        if (! $this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }

        $this->layout = false;

        $product = Product::findOne($id);
        if (!$product) {
            Yii::$app->response->statusCode = 404;
            return ["success" => false, "message" => "Продукт не найден"];
        }
        
        $coupon = Coupon::findOne(["category_id" => $product->category_id]);
        if (!$coupon) {
            Yii::$app->response->statusCode = 404;
            return ["success" => false, "message" => "Купон для данной категории не найден"];
        }

        return $this->render("coupon", [
            "product" => $product,
            "coupon" => $coupon
        ]);
    }

    #[OA\Post(
        path: "/api/order/create",
        summary: "Создание заказа",
        tags: ["Order"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "product_id", type: "integer", example: 3),
                    new OA\Property(property: "order_coupon_id", type: "integer", example: 0),
                    new OA\Property(property: "name", type: "string", example: "Иван Иванов"),
                    new OA\Property(property: "phone", type: "string", example: "+7(999) 999-99-99"),
                    new OA\Property(property: "city", type: "string", example: "Москва"),
                    new OA\Property(property: "street", type: "string", example: "Ленина"),
                    new OA\Property(property: "house", type: "string", example: "1"),
                    new OA\Property(property: "apartment", type: "string", example: "1"),
                    new OA\Property(property: "entrance", type: "string", example: "1"),
                    new OA\Property(property: "floor", type: "string", example: "1"),
                    new OA\Property(property: "intercom", type: "string", example: "123"),
                    new OA\Property(property: "date", type: "string", format: "date", example: "11.08.2025"),
                    new OA\Property(property: "time", type: "integer", example: 14)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Созданный заказ",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "name", type: "string"),
                        new OA\Property(property: "phone", type: "string"),
                        new OA\Property(property: "city", type: "string"),
                        new OA\Property(property: "street", type: "string"),
                        new OA\Property(property: "house", type: "string"),
                        new OA\Property(property: "apartment", type: "string"),
                        new OA\Property(property: "entrance", type: "string"),
                        new OA\Property(property: "floor", type: "string"),
                        new OA\Property(property: "intercom", type: "string"),
                        new OA\Property(property: "date", type: "string", format: "date"),
                        new OA\Property(property: "time", type: "string"),
                        new OA\Property(property: "status", type: "integer"),
                        new OA\Property(property: "url", type: "string", format: "uri")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Ошибка запроса"),
            new OA\Response(response: 401, description: "Ошибка аутентификации")
        ]
    )]
    public function actionCreate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! $this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }
        
        $model = new OrderApplication();
        $data = Yii::$app->request->getBodyParams();
        
        // If getBodyParams() is empty, try to parse raw JSON body
        if (empty($data)) {
            $rawBody = Yii::$app->request->getRawBody();
            if (!empty($rawBody)) {
                $data = json_decode($rawBody, true);
            }
        }
        
        // Log received data for debugging
        Yii::info('Received data: ' . print_r($data, true), 'application');
        
        $model->setAttributes($data);
        $model->user_id = $this->user->id;
        
        // Set default values for required fields that may not be provided
        if (empty($model->comment)) {
            $model->comment = '';
        }

        if ($model->order_coupon_id == 0) {
            $model->order_coupon_id = null;
        }

        if (! $model->validate()) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => $this->getError($model)];
        }
        
        if (! $model->save()) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => $this->getError($model)];
        }

        $data = [
            "id" => $model->id,
            "name" => $model->name,
            "phone" => $model->phone,
            "city" => $model->city,
            "street" => $model->street,
            "house" => $model->house,
            "apartment" => $model->apartment,
            "entrance" => $model->entrance,
            "floor" => $model->floor,
            "intercom" => $model->intercom,
            "date" => $model->date,
            "time" => $model->time,
            "status" => $model->status,
            "url" => null
        ];

        if ($model->order_coupon_id != null) {
            $coupon = Coupon::findOne($model->order_coupon_id);

            $order = new OrderCoupon([
                "user_id" => $this->user->id,
                "coupon_id" => $coupon->id,
                "price" => $coupon->price,
                "date" => time()
            ]);
            $order->save();

            $orderId = new Orders(["type" => 1, "user_id" => $this->user->id, "date" => time(), "price" => $coupon->price, "info" => $order->id]);
            $orderId->save();

            $mrh_login = Yii::$app->params['robokassa_login'];
            $mrh_pass1 = Yii::$app->params['robokassa_pass1'];

            $order->order_id = $orderId->id;
            $order->save();

            $crc = md5("$mrh_login:$order->price:$order->order_id:$mrh_pass1");

            $resultUrl = urlencode('https://www.beautyms.ru/api/payment/result');
            $successUrl = urlencode('https://www.beautyms.ru/success');
            $failUrl = urlencode('https://www.beautyms.ru/fail');
            
            $url = "https://auth.robokassa.ru/Merchant/Index.aspx?MerchantLogin=$mrh_login&OutSum=$coupon->price&InvId=$order->order_id&Description=$coupon->name&SignatureValue=$crc&IsTest=" . Yii::$app->params['robokassa_test'] . "&ResultURL=$resultUrl&SuccessURL=$successUrl&FailURL=$failUrl";

            $model->price = $order->price;
            $model->order_coupon_id = $order->id;
            $model->save();

            $data["url"] = $url;
        }

        return $data;
    }

    #[OA\Post(
        path: "/api/order/item",
        summary: "Получение информации о заказе",
        tags: ["Order"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "order_id", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Информация о заказе",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "name", type: "string"),
                        new OA\Property(property: "phone", type: "string"),
                        new OA\Property(property: "city", type: "string"),
                        new OA\Property(property: "street", type: "string"),
                        new OA\Property(property: "house", type: "string"),
                        new OA\Property(property: "apartment", type: "string"),
                        new OA\Property(property: "entrance", type: "string"),
                        new OA\Property(property: "floor", type: "string"),
                        new OA\Property(property: "intercom", type: "string"),
                        new OA\Property(property: "date", type: "string", format: "date"),
                        new OA\Property(property: "time", type: "string"),
                        new OA\Property(property: "status", type: "integer")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Ошибка запроса"),
            new OA\Response(response: 401, description: "Ошибка аутентификации")
        ]
    )]
    public function actionItem()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! $this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }

        $model = OrderApplication::findOne(Yii::$app->request->post("order_id"));
        if (! $model) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Заказ не найден"];
        }

        return [
            "id" => $model->id,
            "name" => $model->name,
            "phone" => $model->phone,
            "city" => $model->city,
            "street" => $model->street,
            "house" => $model->house,
            "apartment" => $model->apartment,
            "entrance" => $model->entrance,
            "floor" => $model->floor,
            "intercom" => $model->intercom,
            "date" => $model->date,
            "time" => $model->time,
            "status" => $model->status,
        ];
    }

    #[OA\Get(
        path: "/api/order/order-list",
        summary: "Получение списка заказов пользователя",
        tags: ["Order"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список заказов",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer"),
                            new OA\Property(property: "name", type: "string"),
                            new OA\Property(property: "phone", type: "string"),
                            new OA\Property(property: "city", type: "string"),
                            new OA\Property(property: "street", type: "string"),
                            new OA\Property(property: "house", type: "string"),
                            new OA\Property(property: "apartment", type: "string"),
                            new OA\Property(property: "entrance", type: "string"),
                            new OA\Property(property: "floor", type: "string"),
                            new OA\Property(property: "intercom", type: "string"),
                            new OA\Property(property: "date", type: "string", format: "date"),
                            new OA\Property(property: "time", type: "string"),
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
        $order_query = OrderApplication::find()->where(["user_id" => $this->user->id])->all();

        foreach ($order_query as $order) {
            $data[] = [
                "id" => $order->id,
                "name" => $order->name,
                "phone" => $order->phone,
                "city" => $order->city,
                "street" => $order->street,
                "house" => $order->house,
                "apartment" => $order->apartment,
                "entrance" => $order->entrance,
                "floor" => $order->floor,
                "intercom" => $order->intercom,
                "date" => $order->date,
                "time" => $order->time,
                "status" => $order->status
            ];
        }

        return $data;
    }

    public function actionValidation()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (Yii::$app->request->isAjax) {
            $model = new OrderApplication();
            if ($model->load($this->request->post()) && ! $model->validate()) {
                return $this->getError($model);
            }
        }

        return [];
    }
}

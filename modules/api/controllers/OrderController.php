<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Controller;
use app\models\Product;
use app\models\Coupon;
use app\models\OrderApplication;

class OrderController extends Controller
{
    public function actionList($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $products = [];
        $product_query = Product::find()->where(["category_id" => $id])->all();
        foreach ($product_query as $product_item) {
            $products[] = [
                "id" => $product_item->id,
                "html" => '<div style="display: flex"><div style="width: calc(100% - 100px);">' . $product_item->name . '</div><div>' . $product_item->price . ' руб.</div></div>',
                "text" => '<div style="display: flex"><div style="width: calc(100% - 100px); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">' . $product_item->name . '</div><div>' . $product_item->price . ' руб.</div></div>'
            ];
        }

        return ["results" => $products];
    }

    public function actionTime($date = 0)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $hour = (int)date("H", $date == 0 ? (time() + 7200) : strtotime("Y-m-d", $date));

        $times = [];
        for($i = $hour ; $i <= 23; $i++) {
            $times[] = [
                "id" => $i,
                "text" => $i . ':00'
            ];
        }

        return ["results" => $times];
    }

    public function actionCoupon($id)
    {
        $this->layout = false;

        $product = Product::findOne($id);
        $coupon = Coupon::findOne(["category_id" => $product->category_id]);

        return $this->render("coupon", [
            "product" => $product,
            "coupon" => $coupon
        ]);
    }

    /**
     * @SWG\Post(
     *    path = "/order/create",
     *    tags = {"Order"},
     *    summary = "Создать заказ",
     *    @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref = "#/definitions/OrderApplication")
     *     ),
     *	  @SWG\Response(
     *      response = 200,
     *      description = "Информация о заказа",
     *      @SWG\Schema(ref = "#/definitions/OrderApplication")
     *    ),
     *    @SWG\Response(
     *      response = 400,
     *      description = "Ошибка запроса",
     *      @SWG\Schema(ref = "#/definitions/Result")
     *    ),
     *)
     * @throws HttpException
     */
    public function actionCreate() {

        if(!$this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }

        $model = new OrderApplication();
        $model->setAttributes($this->request->post());
        $model->user_id = $this->user->id;

        if($model->order_coupon_id == 0) {
            $model->order_coupon_id = null;
        }

        if(!$model->save()) {
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

        if($model->order_coupon_id != null) {
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

            $mrh_login = "beautyms.ru";
            $mrh_pass1 = "dqqe66GzqsF91TPdLZh7";

            $order->order_id = $orderId->id;
            $order->save();

            $crc = md5("$mrh_login:$order->price:$order->order_id:$mrh_pass1");

            $url = "https://auth.robokassa.ru/Merchant/Index.aspx?MerchantLogin=$mrh_login&OutSum=$coupon->price&InvId=$order->order_id&Description=$coupon->name&SignatureValue=$crc&IsTest=1";

            $model->price = $order->price;
            $model->order_coupon_id = $order->id;
            $model->save();

            $data["url"] = $url;
        }

        return $data;
    }


    /**
     * @SWG\Get(
     *    path = "/order/item",
     *    tags = {"Order"},
     *    summary = "Информация о заказе",
     *    security={{"access_token":{}}},
     *	  @SWG\Response(
     *      response = 200,
     *      description = "Информация о заказе",
     *      @SWG\Schema(ref = "#/definitions/OrderApplication")
     *    ),
     *    @SWG\Response(
     *      response = 400,
     *      description = "Ошибка запроса",
     *      @SWG\Schema(ref = "#/definitions/Result")
     *    ),
     *    @SWG\Response(
     *      response = 403,
     *      description = "Ошибка авторизации",
     *      @SWG\Schema(ref = "#/definitions/Result")
     *    ),
     *)
     * @throws HttpException
     */
    public function actionItem()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if(!$this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }

        $model = OrderApplication::findOne(Yii::$app->request->post("order_id"));
        if(!$model) {
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

    /**
     * @SWG\Get(
     *    path = "/order/list",
     *    tags = {"Coupon"},
     *    summary = "Список заказов",
     *    security={{"access_token":{}}},
     *	  @SWG\Response(
     *      response = 200,
     *      description = "Список заказов",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref="#/definitions/OrderApplication")
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

        if(!$this->user) {
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
}

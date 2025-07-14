<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use app\models\MasterForm;
use app\models\Master;
use yii\helpers\Url;
use YooKassa\Client;

class MasterController extends BaseController
{

    /**
     * @SWG\Post(
     *    path = "/master/register",
     *    tags = {"Master"},
     *    summary = "Регистрация",
     *    @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref = "#/definitions/Master")
     *     ),
     *	  @SWG\Response(
     *      response = 200,
     *      description = "Ссылка на оплату",
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
    public function actionRegister()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! $this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }

        $masterForm = new MasterForm();
        $masterForm->setAttributes($this->request->post());
        if (! $masterForm->validate()) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => $this->getError($masterForm)];
        }

        $master = new Master(["user_id" => $this->user->id, "date" => time()]);
        $master->setAttributes($masterForm->attributes);

        if (! $master->save()) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => $this->getError($master)];
        }

        $client = new Client();
        $client->setAuth(Yii::$app->params['yoomoney_shopid'], Yii::$app->params['yoomoney_secret']);
        $response = $client->createPayment(
            array(
                'amount' => array(
                    'value' => 199,
                    'currency' => 'RUB',
                ),
                'confirmation' => array(
                    'type' => 'redirect',
                    'locale' => 'ru_RU',
                    'return_url' => YII_ENV_DEV ? 'http://localhost:8080/site/register-master' : 'https://beautyms.ru/site/register-master',
                ),
                'capture' => true,
                'description' => "Пошлина регистрации мастером"
            ),
            "M".$master->id
        );

        $master->birthday = date("d.m.Y", $master->birthday);
        $master->order_id = $response->getId();
        $master->save();

        return ["success" => true, "url" => $this->redirect($response->getConfirmation()->getConfirmationUrl())];

    }

    /**
     * @SWG\Get(
     *    path = "/master/profile",
     *    tags = {"Master"},
     *    summary = "Информация о мастер",
     *    security={{"access_token":{}}},
     *	  @SWG\Response(
     *      response = 200,
     *      description = "Информация о мастере",
     *      @SWG\Schema(ref = "#/definitions/Master")
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
    public function actionProfile()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! $this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }

        $master = Master::findOne(['user_id' => $this->user->id]);
        if (! $master) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Профиль мастера не найден"];
        }

        return [
            "id" => $master->id,
            "middlename" => $master->middlename,
            "firstname" => $master->firstname,
            "birthday" => $master->birthday,
            "date" => $master->date,
            "balance" => $master->balance,
            "gender" => $master->gender,
            "client_gender" => $master->client_gender,
            "status" => $master->status,
            "search_radius" => $master->search_radius,
            "work_city" => $master->work_city,
            "work_street" => $master->work_street,
            "work_house" => $master->work_house,
            "work_lat" => $master->work_lat,
            "work_lon" => $master->work_lon,
            "live_city" => $master->live_city,
            "live_street" => $master->live_street,
            "live_house" => $master->live_house,
            "live_lat" => $master->live_lat,
            "live_lon" => $master->live_lon,
        ];
    }

    /**
     * @SWG\Post(
     *    path = "/master/update",
     *    tags = {"Master"},
     *    summary = "Изменить мастера",
     *    security={{"access_token":{}}},
     *    @SWG\Parameter(
     *           name="body",
     *           in="body",
     *           required=true,
     *           @SWG\Schema(ref = "#/definitions/Master")
     *       ),
     *	  @SWG\Response(
     *      response = 200,
     *      description = "Сохранен успешно",
     *      @SWG\Schema(ref = "#/definitions/Master")
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
    public function actionUpdate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! $this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }

        return $this->getProfile($this->user);
    }

    /**
     * @SWG\Post(
     *    path = "/master/balance",
     *    tags = {"Master"},
     *    summary = "Пополнить баланса",
     *    @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="price",
     *                 type="number",
     *                 description="Сумма пополения",
     *             ),
     *         )
     *     ),
     *	  @SWG\Response(
     *      response = 200,
     *      description = "Ссылка на оплату",
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
    public function actionBalance()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! $this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }

        $price = (float) Yii::$app->request->post("price", 0);
        if ($price <= 0) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Введите сумму пополнения"];
        }

        $order_id = time();

        $mrh_login = "beautyms.ru";
        $mrh_pass1 = "dqqe66GzqsF91TPdLZh7";

        $crc = md5("$mrh_login:$price:$order_id:$mrh_pass1");

        $url = "https://auth.robokassa.ru/Merchant/Index.aspx?MerchantLogin=$mrh_login&OutSum=$price&InvId=$order_id&SignatureValue=$crc&IsTest=1";

        return ["success" => true, "url" => $url];
    }

    protected function getProfile($user)
    {

        return [
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
        ];
    }
}

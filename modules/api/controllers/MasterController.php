<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use app\models\MasterForm;
use app\models\Master;
use app\models\User;
use yii\helpers\Url;

use OpenApi\Attributes as OA;

class MasterController extends BaseController
{

    #[OA\Post(
        path: "/api/master/register",
        summary: "Полная регистрация мастера с созданием пользователя",
        tags: ["Master"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "phone", type: "string", example: "+7(999) 123-45-67"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "master@example.com"),
                    new OA\Property(property: "password", type: "string", example: "password123"),
                    new OA\Property(property: "password_repeat", type: "string", example: "password123"),
                    new OA\Property(property: "lastname", type: "string", example: "Иванов"),
                    new OA\Property(property: "firstname", type: "string", example: "Иван"),
                    new OA\Property(property: "middlename", type: "string", example: "Иванович"),
                    new OA\Property(property: "gender", type: "integer", example: 1),
                    new OA\Property(property: "birthday", type: "string", format: "date", example: "01.01.1990")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Мастер успешно зарегистрирован",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Мастер успешно зарегистрирован"),
                        new OA\Property(property: "payment_url", type: "string", format: "uri"),
                        new OA\Property(property: "access_token", type: "string"),
                        new OA\Property(property: "user_id", type: "integer"),
                        new OA\Property(property: "master_id", type: "integer")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Ошибка валидации данных")
        ]
    )]
    public function actionRegister()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $masterForm = new MasterForm();
            $masterForm->setAttributes(Yii::$app->request->post());
            
            if (!$masterForm->validate()) {
                Yii::$app->response->statusCode = 400;
                return ["success" => false, "message" => $this->getError($masterForm)];
            }

            // Создаем или находим пользователя
            $user = User::findOne(["phone" => preg_replace('/[^0-9]/', '', $masterForm->phone)]);
            if (empty($user)) {
                $user = new User([
                    "name" => $masterForm->middlename,
                    "email" => $masterForm->email,
                    "phone" => $masterForm->phone,
                    "password" => Yii::$app->getSecurity()->generatePasswordHash($masterForm->password),
                    "token" => Yii::$app->getSecurity()->generateRandomString()
                ]);
                if (!$user->save()) {
                    Yii::$app->response->statusCode = 400;
                    return ["success" => false, "message" => $this->getError($user)];
                }

                // Назначаем роль мастера
                $auth = Yii::$app->authManager;
                $role = $auth->getRole("master");
                if ($role) {
                    $auth->assign($role, $user->id);
                }
            }

            // Создаем профиль мастера
            $master = new Master(["user_id" => $user->id, "date" => time()]);
            $master->setAttributes($masterForm->attributes);

            if (!$master->save()) {
                Yii::$app->response->statusCode = 400;
                return ["success" => false, "message" => $this->getError($master)];
            }

            // Создание ссылки на оплату через Robokassa для регистрации мастера
            $mrh_login = Yii::$app->params['robokassa_login'];
            $mrh_pass1 = Yii::$app->params['robokassa_pass1'];
            $amount = 199; // 199 рублей при регистрации мастером

            $master->order_id = (string) $master->id;
            if (!$master->save(false)) { // Отключаем валидацию для обновления только order_id
                Yii::$app->response->statusCode = 400;
                return ["success" => false, "message" => "Ошибка при обновлении order_id мастера"];
            }

            $crc = md5("$mrh_login:$amount:$master->order_id:$mrh_pass1");

            $resultUrl = urlencode('https://www.beautyms.ru/api/payment/result');
            $successUrl = urlencode('https://www.beautyms.ru/success');
            $failUrl = urlencode('https://www.beautyms.ru/fail');

            $url = "https://auth.robokassa.ru/Merchant/Index.aspx?MerchantLogin=$mrh_login&OutSum=$amount&InvId=$master->order_id&Description=Пошлина регистрации мастером&SignatureValue=$crc&IsTest=" . Yii::$app->params['robokassa_test'] . "&ResultURL=$resultUrl&SuccessURL=$successUrl&FailURL=$failUrl";

            return [
                "success" => true,
                "message" => "Мастер успешно зарегистрирован",
                "payment_url" => $url,
                "access_token" => $user->token,
                "user_id" => $user->id,
                "master_id" => $master->id
            ];

        } catch (\Exception $e) {
            Yii::error('Ошибка API регистрации мастера: ' . $e->getMessage());
            Yii::$app->response->statusCode = 500;
            return ["success" => false, "message" => "Произошла ошибка при регистрации: " . $e->getMessage()];
        }
    }

    #[OA\Get(
        path: "/api/master/profile",
        summary: "Получение профиля мастера",
        tags: ["Master"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Профиль мастера",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "middlename", type: "string"),
                        new OA\Property(property: "firstname", type: "string"),
                        new OA\Property(property: "birthday", type: "string", format: "date"),
                        new OA\Property(property: "date", type: "integer"),
                        new OA\Property(property: "balance", type: "number"),
                        new OA\Property(property: "gender", type: "integer"),
                        new OA\Property(property: "client_gender", type: "integer"),
                        new OA\Property(property: "status", type: "integer"),
                        new OA\Property(property: "search_radius", type: "integer"),
                        new OA\Property(property: "work_city", type: "string"),
                        new OA\Property(property: "work_street", type: "string"),
                        new OA\Property(property: "work_house", type: "string"),
                        new OA\Property(property: "work_lat", type: "number"),
                        new OA\Property(property: "work_lon", type: "number"),
                        new OA\Property(property: "live_city", type: "string"),
                        new OA\Property(property: "live_street", type: "string"),
                        new OA\Property(property: "live_house", type: "string"),
                        new OA\Property(property: "live_lat", type: "number"),
                        new OA\Property(property: "live_lon", type: "number"),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Ошибка аутентификации")
        ]
    )]
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

    #[OA\Post(
        path: "/api/master/update",
        summary: "Обновление профиля мастера",
        tags: ["Master"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Обновленный профиль пользователя",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "name", type: "string"),
                        new OA\Property(property: "email", type: "string", format: "email")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Ошибка аутентификации")
        ]
    )]
    public function actionUpdate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! $this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }

        return $this->getProfile($this->user);
    }

    #[OA\Post(
        path: "/api/master/balance",
        summary: "Пополнение баланса",
        tags: ["Master"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "price", type: "number")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "URL для оплаты",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean"),
                        new OA\Property(property: "url", type: "string", format: "uri")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Ошибка запроса"),
            new OA\Response(response: 401, description: "Ошибка аутентификации")
        ]
    )]
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

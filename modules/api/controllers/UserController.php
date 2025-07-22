<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use app\models\LoginForm;
use yii\web\UploadedFile;
use app\models\User;
use app\models\UserWords;
use app\models\Chart;
use app\models\Affiliate;
use app\models\AuthCode;
use app\models\AffiliateHistory;
use app\models\FavoriteMaster;
use app\models\Master;
use yii\helpers\Url;
use OpenApi\Attributes as OA;

#[OA\Info(title: "BeautyMS API", version: "1.0")]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class UserController extends BaseController
{

    #[OA\PathItem(path: "/api/user/register")]
    #[OA\Post(
        path: "/api/user/register",
        summary: "Регистрация нового пользователя",
        tags: ["User"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "phone", type: "string", example: "79991234567"),
                    new OA\Property(property: "name", type: "string", example: "Иван Иванов"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Код подтверждения отправлен",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Код подтверждения отправлен"),
                        new OA\Property(property: "debugCode", type: "integer", example: 1234)
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Ошибка запроса")
        ]
    )]
    public function actionRegister()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $phone = Yii::$app->request->post("phone");
        $name = trim(Yii::$app->request->post("name", ""));
        $email = trim(Yii::$app->request->post("email", ""));

        if (! $phone) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Номер телефона не указан"];
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // Форматируем телефон для валидации модели
        $formattedPhone = User::maskPhone($cleanPhone);

        // Проверяем, существует ли уже пользователь с таким телефоном
        $existingUser = User::findOne(["phone" => $cleanPhone]);
        if ($existingUser) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Пользователь с таким номером телефона уже существует"];
        }

        // Создаем нового пользователя
        $user = new User([
            "phone" => $formattedPhone,
            "name" => $name,
            "email" => $email
        ]);

        if (! $user->save()) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => $this->getError($user)];
        }

        // Генерируем и отправляем код подтверждения, пока нет отправки SMS
        $code = rand(1000, 9999);
        $authCode = new AuthCode(["user_id" => $user->id, "code" => $code, "date" => time()]);
        $authCode->save();

        return ["success" => true, "message" => "Код подтверждения отправлен", "debugCode" => $authCode->code];
    }

    #[OA\PathItem(path: "/api/user/auth")]
    #[OA\Post(
        path: "/api/user/auth",
        summary: "Аутентификация по номеру телефона",
        tags: ["User"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "phone", type: "string", example: "79991234567")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Код успешно отправлен",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Код успешно отправлен"),
                        new OA\Property(property: "debugCode", type: "integer", example: 1234)
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Ошибка запроса")
        ]
    )]
    public function actionAuth()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! Yii::$app->request->post("phone")) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Номер телефона не указан"];
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', Yii::$app->request->post("phone"));
        $user = User::findOne(["phone" => $cleanPhone]);
        if (! $user) {
            $formattedPhone = User::maskPhone($cleanPhone);
            $user = new User(["phone" => $formattedPhone]);
            if (! $user->save()) {
                Yii::$app->response->statusCode = 400;
                return ["success" => false, "message" => $this->getError($user)];
            }
        }

        $code = rand(1000, 9999);

        $authCode = new AuthCode(["user_id" => $user->id, "code" => $code, "date" => time()]);
        $authCode->save();

        Yii::$app->sms->send($cleanPhone, 'Your auth code: ' . $code);

        return ["success" => true, "message" => "Код успешно отправлен", "debugCode" => $authCode->code];
    }

    #[OA\PathItem(path: "/api/user/code")]
    #[OA\Post(
        path: "/api/user/code",
        summary: "Подтверждение кода и получение токена",
        tags: ["User"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "phone", type: "string", example: "79991234567"),
                    new OA\Property(property: "code", type: "string", example: "1234")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешная аутентификация",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "access_token", type: "string", example: "someRandomString")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Ошибка запроса")
        ]
    )]
    public function actionCode()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! Yii::$app->request->post("phone")) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Номер телефона не указан"];
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', Yii::$app->request->post("phone"));
        $user = User::findOne(["phone" => $cleanPhone]);
        if (! $user) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Пользователь не найден"];
        }

        $code = AuthCode::findOne(["user_id" => $user->id, "code" => Yii::$app->request->post("code")]);
        if (! $code) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Код введен неправильно"];
        }

        $user->token = Yii::$app->security->generateRandomString();
        $user->save();

        $code->delete();

        return ["access_token" => $user->token];
    }

    #[OA\PathItem(path: "/api/user/profile")]
    #[OA\Get(
        path: "/api/user/profile",
        summary: "Получение профиля пользователя",
        tags: ["User"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Профиль пользователя",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "name", type: "string"),
                        new OA\Property(property: "fcm_token", type: "string"),
                        new OA\Property(property: "email", type: "string", format: "email")
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

        return $this->getProfile($this->user);
    }

    #[OA\PathItem(path: "/api/user/update")]
    #[OA\Post(
        path: "/api/user/update",
        summary: "Обновление профиля пользователя",
        tags: ["User"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "email", type: "string", format: "email"),
                    new OA\Property(property: "fcm_token", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Обновленный профиль пользователя",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "name", type: "string"),
                        new OA\Property(property: "fcm_token", type: "string"),
                        new OA\Property(property: "email", type: "string", format: "email")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Ошибка запроса"),
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

        $this->user->name = trim(Yii::$app->request->post("name", $this->user->name));
        $this->user->email = trim(Yii::$app->request->post("email", $this->user->email));
        $this->user->fcm_token = Yii::$app->request->post("fcm_token", $this->user->fcm_token);

        if (! $this->user->save()) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Error save"];
        }

        return $this->getProfile($this->user);
    }

    #[OA\PathItem(path: "/api/user/reset-password-request")]
    #[OA\Post(
        path: "/api/user/reset-password-request",
        summary: "Запрос сброса пароля по номеру телефона",
        tags: ["User"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "phone", type: "string", example: "79991234567")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Код сброса пароля отправлен",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Код сброса пароля отправлен"),
                        new OA\Property(property: "debugCode", type: "integer", example: 1234)
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Ошибка запроса")
        ]
    )]
    public function actionResetPasswordRequest()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $phone = Yii::$app->request->post("phone");

        if (! $phone) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Номер телефона не указан"];
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $user = User::findOne(["phone" => $cleanPhone]);
        
        if (! $user) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Пользователь с таким номером телефона не найден"];
        }

        // Удаляем предыдущие коды сброса пароля для этого пользователя
        AuthCode::deleteAll(['user_id' => $user->id]);

        // Генерируем новый код для сброса пароля
        $code = rand(1000, 9999);
        $authCode = new AuthCode([
            "user_id" => $user->id, 
            "code" => $code, 
            "date" => time()
        ]);
        
        if (! $authCode->save()) {
            Yii::$app->response->statusCode = 500;
            return ["success" => false, "message" => "Ошибка создания кода сброса"];
        }

        // Отправляем SMS с кодом сброса пароля
        Yii::$app->sms->send($cleanPhone, 'Ваш код для сброса пароля: ' . $code);

        return [
            "success" => true, 
            "message" => "Код сброса пароля отправлен", 
            "debugCode" => $authCode->code
        ];
    }

    #[OA\PathItem(path: "/api/user/reset-password")]
    #[OA\Post(
        path: "/api/user/reset-password",
        summary: "Сброс пароля с подтверждением кода",
        tags: ["User"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "phone", type: "string", example: "79991234567"),
                    new OA\Property(property: "code", type: "string", example: "1234"),
                    new OA\Property(property: "password", type: "string", example: "newPassword123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Пароль успешно изменен",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Пароль успешно изменен"),
                        new OA\Property(property: "access_token", type: "string", example: "someRandomString")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Ошибка запроса")
        ]
    )]
    public function actionResetPassword()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $phone = Yii::$app->request->post("phone");
        $code = Yii::$app->request->post("code");
        $password = Yii::$app->request->post("password");

        if (! $phone || ! $code || ! $password) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Все поля обязательны для заполнения"];
        }

        if (strlen($password) < 6) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Пароль должен содержать минимум 6 символов"];
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $user = User::findOne(["phone" => $cleanPhone]);
        
        if (! $user) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Пользователь не найден"];
        }

        // Проверяем код подтверждения
        $authCode = AuthCode::findOne([
            "user_id" => $user->id, 
            "code" => $code
        ]);
        
        if (! $authCode) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Неверный код подтверждения"];
        }

        // Проверяем, не истек ли код (15 минут)
        if (time() - $authCode->date > 900) {
            $authCode->delete();
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Код подтверждения истек"];
        }

        // Обновляем пароль пользователя
        $user->password = Yii::$app->security->generatePasswordHash($password);
        $user->token = Yii::$app->security->generateRandomString();
        
        if (! $user->save()) {
            Yii::$app->response->statusCode = 500;
            return ["success" => false, "message" => "Ошибка обновления пароля"];
        }

        // Удаляем использованный код
        $authCode->delete();

        return [
            "success" => true,
            "message" => "Пароль успешно изменен",
            "access_token" => $user->token
        ];
    }

    #[OA\PathItem(path: "/api/user/favorite-master")]
    #[OA\Post(
        path: "/api/user/favorite-master",
        summary: "Добавление мастера в избранное",
        tags: ["User"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "master_id", type: "integer", example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Мастер добавлен в избранное",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Мастер добавлен в избранное")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Ошибка запроса"),
            new OA\Response(response: 401, description: "Ошибка аутентификации")
        ]
    )]
    public function actionFavoriteMaster()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! $this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }

        $masterId = (int) Yii::$app->request->post("master_id");

        if (! $masterId) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "ID мастера не указан"];
        }

        // Проверяем существование мастера
        $master = Master::findOne($masterId);
        if (! $master) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Мастер не найден"];
        }

        // Проверяем, не добавлен ли уже мастер в избранное
        $existingFavorite = FavoriteMaster::findOne([
            'user_id' => $this->user->id,
            'master_id' => $masterId
        ]);

        if ($existingFavorite) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Мастер уже в избранном"];
        }

        // Добавляем в избранное
        $favorite = new FavoriteMaster([
            'user_id' => $this->user->id,
            'master_id' => $masterId
        ]);

        if (! $favorite->save()) {
            Yii::$app->response->statusCode = 500;
            return ["success" => false, "message" => "Ошибка добавления в избранное"];
        }

        return [
            "success" => true,
            "message" => "Мастер добавлен в избранное"
        ];
    }

    #[OA\PathItem(path: "/api/user/unfavorite-master")]
    #[OA\Post(
        path: "/api/user/unfavorite-master",
        summary: "Удаление мастера из избранного",
        tags: ["User"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "master_id", type: "integer", example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Мастер удален из избранного",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Мастер удален из избранного")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Ошибка запроса"),
            new OA\Response(response: 401, description: "Ошибка аутентификации")
        ]
    )]
    public function actionUnfavoriteMaster()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! $this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }

        $masterId = (int) Yii::$app->request->post("master_id");

        if (! $masterId) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "ID мастера не указан"];
        }

        // Ищем запись в избранном
        $favorite = FavoriteMaster::findOne([
            'user_id' => $this->user->id,
            'master_id' => $masterId
        ]);

        if (! $favorite) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Мастер не найден в избранном"];
        }

        if (! $favorite->delete()) {
            Yii::$app->response->statusCode = 500;
            return ["success" => false, "message" => "Ошибка удаления из избранного"];
        }

        return [
            "success" => true,
            "message" => "Мастер удален из избранного"
        ];
    }

    #[OA\PathItem(path: "/api/user/favorite-masters")]
    #[OA\Get(
        path: "/api/user/favorite-masters",
        summary: "Получение списка избранных мастеров",
        tags: ["User"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "page",
                description: "Номер страницы",
                in: "query",
                schema: new OA\Schema(type: "integer", default: 1)
            ),
            new OA\Parameter(
                name: "limit",
                description: "Количество элементов на странице",
                in: "query",
                schema: new OA\Schema(type: "integer", default: 20, maximum: 100)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список избранных мастеров",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "masters",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer"),
                                    new OA\Property(property: "firstname", type: "string"),
                                    new OA\Property(property: "middlename", type: "string"),
                                    new OA\Property(property: "lastname", type: "string"),
                                    new OA\Property(property: "gender", type: "integer"),
                                    new OA\Property(property: "work_city", type: "string"),
                                    new OA\Property(property: "work_street", type: "string"),
                                    new OA\Property(property: "work_house", type: "string"),
                                    new OA\Property(property: "status", type: "integer"),
                                    new OA\Property(property: "added_at", type: "integer")
                                ]
                            )
                        ),
                        new OA\Property(property: "pagination", type: "object",
                            properties: [
                                new OA\Property(property: "page", type: "integer"),
                                new OA\Property(property: "limit", type: "integer"),
                                new OA\Property(property: "total", type: "integer"),
                                new OA\Property(property: "pages", type: "integer")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Ошибка аутентификации")
        ]
    )]
    public function actionFavoriteMasters()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! $this->user) {
            Yii::$app->response->statusCode = 401;
            return ["success" => false, "message" => "Token не найден"];
        }

        $page = (int) Yii::$app->request->get('page', 1);
        $limit = min((int) Yii::$app->request->get('limit', 20), 100);

        $query = FavoriteMaster::find()
            ->with('master')
            ->where(['user_id' => $this->user->id])
            ->orderBy(['created_at' => SORT_DESC]);

        // Получаем общее количество
        $total = $query->count();

        // Применяем пагинацию
        $offset = ($page - 1) * $limit;
        $favorites = $query->offset($offset)->limit($limit)->all();

        $result = [];
        foreach ($favorites as $favorite) {
            if ($favorite->master) {
                $result[] = [
                    'id' => $favorite->master->id,
                    'firstname' => $favorite->master->firstname,
                    'middlename' => $favorite->master->middlename,
                    'lastname' => $favorite->master->lastname,
                    'gender' => $favorite->master->gender,
                    'work_city' => $favorite->master->work_city,
                    'work_street' => $favorite->master->work_street,
                    'work_house' => $favorite->master->work_house,
                    'status' => $favorite->master->status,
                    'added_at' => $favorite->created_at
                ];
            }
        }

        return [
            'masters' => $result,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ];
    }

    protected function getProfile($user)
    {

        return [
            "id" => $user->id,
            "name" => $user->name,
            "fcm_token" => $user->fcm_token,
            "email" => $user->email,
        ];
    }
}

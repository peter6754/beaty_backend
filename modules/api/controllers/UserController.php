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
use yii\helpers\Url;
/**
 * Default controller for the `api` module
 */
class UserController extends BaseController
{

    /**
     * @SWG\Post(
     *    path = "/user/auth",
     *    tags = {"User"},
     *    summary = "Авторизация",
     *    @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="phone",
     *                 type="string",
     *                 description="Телефон",
     *             ),
     *         )
     *     ),
     *	  @SWG\Response(
     *      response = 200,
     *      description = "Код авторизации пользователя отправлен",
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
    public function actionAuth()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! Yii::$app->request->post("phone")) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Номер телефона не указан"];
        }

        $user = User::findOne(["phone" => preg_replace('/[^0-9]/', '', Yii::$app->request->post("phone"))]);
        if (! $user) {
            $user = new User(["phone" => Yii::$app->request->post("phone")]);
            if (! $user->save()) {
                Yii::$app->response->statusCode = 400;
                return ["success" => false, "message" => $this->getError($user)];
            }
        }

        $code = rand(1000, 9999);

        $authCode = new AuthCode(["user_id" => $user->id, "code" => $code, "date" => time()]);
        $authCode->save();

        return ["success" => true, "message" => "Код успешно отправлен", "debugCode" => $authCode->code];
    }

    /**
     * @SWG\Post(
     *    path = "/user/code",
     *    tags = {"User"},
     *    summary = "Подтверждение пользователя",
     *    @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(
     *              type="object",
     *               @SWG\Property(
     *                   property="phone",
     *                   type="string",
     *                   description="Телефон",
     *               ),
     *               @SWG\Property(
     *                   property="code",
     *                   type="string",
     *                   description="Код подверждения",
     *               ),
     *          )
     *      ),
     *	  @SWG\Response(
     *      response = 200,
     *      description = "Токен авторизации пользователя",
     *      @SWG\Schema(ref = "#/definitions/Token")
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
    public function actionCode()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! Yii::$app->request->post("phone")) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Номер телефона не указан"];
        }

        $user = User::findOne(["phone" => preg_replace('/[^0-9]/', '', Yii::$app->request->post("phone"))]);
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
        ;
        $user->save();

        $code->delete();

        return ["access_token" => $user->token];
    }

    /**
     * @SWG\Get(
     *    path = "/user/profile",
     *    tags = {"User"},
     *    summary = "Информация о пользователе",
     *    security={{"access_token":{}}},
     *	  @SWG\Response(
     *      response = 200,
     *      description = "Информация о пользователе",
     *      @SWG\Schema(ref = "#/definitions/User")
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

        return $this->getProfile($this->user);
    }

    /**
     * @SWG\Post(
     *    path = "/user/update",
     *    tags = {"User"},
     *    summary = "Изменить пользователя",
     *    security={{"access_token":{}}},
     *    @SWG\Parameter(
     *           name="body",
     *           in="body",
     *           required=true,
     *           @SWG\Schema(
     *               type="object",
     *                 @SWG\Property(
     *                     property="name",
     *                     type="string",
     *                     description="ФИО",
     *                 ),
     *                 @SWG\Property(
     *                     property="email",
     *                     type="string",
     *                     description="Email",
     *                 ),
     *                 @SWG\Property(
     *                     property="fcm_token",
     *                     type="string",
     *                     description="Токен Firebase",
     *                 ),
     *           )
     *       ),
     *	  @SWG\Response(
     *      response = 200,
     *      description = "Сохранен успешно",
     *      @SWG\Schema(ref = "#/definitions/User")
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

        $this->user->name = trim(Yii::$app->request->post("name", $this->user->name));
        $this->user->email = trim(Yii::$app->request->post("email", $this->user->email));
        $this->user->fcm_token = Yii::$app->request->post("fcm_token", $this->user->fcm_token);

        if (! $this->user->save()) {
            Yii::$app->response->statusCode = 400;
            return ["success" => false, "message" => "Error save"];
        }

        return $this->getProfile($this->user);
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

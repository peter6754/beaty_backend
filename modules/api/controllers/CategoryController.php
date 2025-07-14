<?php

namespace app\modules\api\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\User;
use app\models\Category;

class CategoryController extends BaseController
{
    /**
     * @SWG\Get(
     *    path = "/category/list",
     *    tags = {"Category"},
     *    summary = "Список категорий",
     *    security={{"access_token":{}}},
     *	  @SWG\Response(
     *      response = 200,
     *      description = "Список категорий",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref="#/definitions/Category")
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
        $category_query = Category::find()->where(["active" => 1])->all();

        foreach ($category_query as $category) {
            $data[] = [
                "id" => $category->id,
                "name" => $category->name,
                "color" => $category->color,
                "image" => $category->getImageUrl("x200"),
            ];
        }

        return $data;
    }

}

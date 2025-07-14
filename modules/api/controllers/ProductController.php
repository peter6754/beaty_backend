<?php

namespace app\modules\api\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;
use app\models\Product;

class ProductController extends BaseController
{

    /**
     * @SWG\Get(
     *    path = "/product/list",
     *    tags = {"Product"},
     *    summary = "Список услуг",
     *    security={{"access_token":{}}},
     *	  @SWG\Response(
     *      response = 200,
     *      description = "Список услуг",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref="#/definitions/Product")
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
        $product_query = Product::find()->joinWith(["category"])->all();

        foreach ($product_query as $product) {
            $data[] = [
                "id" => $product->id,
                "name" => $product->name,
                "category" => isset($product->category) ? [
                    "id" => $product->category->id,
                    "name" => $product->category->name,
                    "color" => $product->category->color,
                    "image" => $product->category->getImageUrl("x200"),
                ] : null,
                "price" => (float) $product->price,
            ];
        }

        return $data;
    }

}

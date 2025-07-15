<?php

namespace app\modules\api\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;
use app\models\Product;

use OpenApi\Attributes as OA;

class ProductController extends BaseController
{

    #[OA\Get(
        path: "/api/product/list",
        summary: "Получение списка продуктов",
        tags: ["Product"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список продуктов",
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
                            new OA\Property(property: "price", type: "number")
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

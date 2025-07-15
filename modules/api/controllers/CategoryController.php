<?php

namespace app\modules\api\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;
use app\models\User;
use app\models\Category;

use OpenApi\Attributes as OA;

class CategoryController extends BaseController
{
    #[OA\Get(
        path: "/api/category/list",
        summary: "Получение списка категорий",
        tags: ["Category"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список категорий",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer"),
                            new OA\Property(property: "name", type: "string"),
                            new OA\Property(property: "color", type: "string"),
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

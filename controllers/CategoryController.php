<?php

namespace app\controllers;

use yii\web\NotFoundHttpException;
use app\models\Category;
use yii\web\Controller;
use app\models\Coupon;

class CategoryController extends Controller
{
    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $categories = Category::find()->where(["active" => 1])->all();

        return $this->render('index', [
            'categories' => $categories,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        if (! is_numeric($id) || $id <= 0) {
            throw new NotFoundHttpException('Неверный идентификатор категории');
        }

        $category = Category::findOne($id);

        if (! $category) {
            throw new NotFoundHttpException('Категория не найдена');
        }

        $coupons = Coupon::find()->where([
            "category_id" => $id
        ])->all();

        return $this->render('view', [
            'category' => $category,
            'coupons' => $coupons,
        ]);
    }
}
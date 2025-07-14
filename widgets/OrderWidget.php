<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use app\models\Category;
use app\models\OrderApplication;

class OrderWidget extends Widget
{

    public function run()
    {
        $category_data = array();
        $category = Category::find()->where(["active" => 1])->all();

        // Если нет активных категорий, добавляем пустую опцию
        if (empty($category)) {
            $category_data[0] = '<div style="display: flex"><span class="select-category-title">Категории не найдены</span></div>';
        } else {
            foreach ($category as $category_item) {
                $category_data[$category_item->id] =
                    '<div style="display: flex"><div style="background-color: '.$category_item->color.'" class="select-category-image"><img src="'.$category_item->getImageUrl("66x55").'"/></div><span class="select-category-title">'.$category_item->name.'</span></div>';
            }
        }

        $model = new OrderApplication(["order_coupon_id" => 0, "date" => date('d.m.Y')]);

        return $this->render('orderWidget', [
            'category' => $category,
            'model' => $model,
            'category_data' => $category_data,
        ]);
    }

}
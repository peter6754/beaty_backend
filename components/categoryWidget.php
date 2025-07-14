<?php

namespace app\components;

use Yii;
use yii\base\Widget;
use app\models\Category;
use yii\helpers\Url;

class categoryWidget extends Widget {

    public function init()
    {
        parent::init();
    }

    public function run()
    {
		$menu = '';
		$menu_query = Category::find()->where(["active" => 1])->all();
		foreach ($menu_query as $item) {
            $menu .= '<li><a class="dropdown-item" href="' . Url::to(['category/view', 'id' => $item->id]) . '">' . $item->name . '</a></li>';
        }

        return $menu;
    }
}

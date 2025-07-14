<?php
// Тестовый скрипт для проверки изображений купонов

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__.'/vendor/autoload.php');
require(__DIR__.'/vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__.'/config/web.php');

(new yii\web\Application($config))->init();

echo "Тестирование изображений купонов...\n\n";

// Получаем несколько купонов
$coupons = \app\models\Coupon::find()->limit(3)->all();

foreach ($coupons as $coupon) {
    echo "Купон #{$coupon->id}: {$coupon->name}\n";

    // Новый способ - прямая ссылка (используется в приложении)
    $newUrl = $coupon->getImageUrl("250x250");
    echo "  URL изображения: {$newUrl}\n";

    // Проверяем, существует ли файл по новому пути
    if ($coupon->image_path) {
        $webPath = Yii::getAlias('@webroot').'/'.$coupon->image_path;
        $exists = file_exists($webPath) ? "ДА" : "НЕТ";
        echo "  Файл существует: {$exists} ({$webPath})\n";
    } else {
        echo "  Путь к изображению не задан в БД\n";
    }

    echo "\n";
}

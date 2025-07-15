<?php

namespace app\models;

use Yii;

class Image extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['filePath', 'modelName', 'urlAlias'], 'required'],
            [['itemId', 'isMain', 'sorted'], 'integer'],
            [['filePath', 'urlAlias'], 'string', 'max' => 400],
            [['modelName'], 'string', 'max' => 150],
            [['name'], 'string', 'max' => 80],
        ];
    }

    /**
     * Создать или найти существующее изображение
     */
    public static function createFromUpload($uploadedFile)
    {
        if (! $uploadedFile || $uploadedFile->error !== UPLOAD_ERR_OK) {
            return null;
        }

        // Генерируем безопасное имя файла
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $uploadedFile->baseName);
        $safeName = trim($safeName, '_');
        if (empty($safeName)) {
            $safeName = 'image_'.time();
        }

        // Проверяем уникальность имени файла
        $fileName = $safeName.'.'.$uploadedFile->extension;
        $uploadDir = '/images/storage/';
        $fullUploadDir = Yii::getAlias('@webroot').$uploadDir;

        if (! is_dir($fullUploadDir)) {
            mkdir($fullUploadDir, 0755, true);
        }

        $counter = 1;
        while (file_exists($fullUploadDir.$fileName)) {
            $fileName = $safeName.'_'.$counter.'.'.$uploadedFile->extension;
            $counter++;
        }

        // Сохраняем файл
        $uploadPath = $fullUploadDir.$fileName;
        if ($uploadedFile->saveAs($uploadPath)) {
            // Создаем запись в БД
            $image = new self();
            $image->filePath = $uploadDir.$fileName;
            $image->name = $fileName;
            $image->modelName = 'Category'; // Можно адаптировать под разные модели
            $image->urlAlias = $fileName;
            $image->isMain = 1;
            $image->sorted = 0;

            if ($image->save()) {
                return $image;
            }
        }

        return null;
    }

    /**
     * Получить URL изображения
     */
    public function getUrl()
    {
        // Для консольного приложения формируем простой URL
        if (! (Yii::$app instanceof \yii\web\Application)) {
            return $this->filePath;
        }

        $baseUrl = Yii::$app->request->baseUrl;

        // Убеждаемся, что filePath начинается с /
        $filePath = $this->filePath;
        if (! empty($filePath) && $filePath[0] !== '/') {
            $filePath = '/'.$filePath;
        }

        // Если baseUrl пустой или ".", убираем его
        if (empty($baseUrl) || $baseUrl === '.') {
            return $filePath;
        }

        return $baseUrl.$filePath;
    }

    /**
     * Получить полный путь к файлу
     */
    public function getFilePath()
    {
        return Yii::getAlias('@webroot').$this->filePath;
    }

    /**
     * Проверить, используется ли изображение
     */
    public function isUsed()
    {
        // Проверяем использование в категориях
        $categoryCount = Category::find()->where(['image_id' => $this->id])->count();

        // Проверяем использование в купонах
        $couponCount = Coupon::find()->where(['image_id' => $this->id])->count();

        return ($categoryCount + $couponCount) > 0;
    }

    /**
     * Удалить неиспользуемые изображения
     */
    public static function cleanupUnused()
    {
        $unusedImages = self::find()->all();
        $deletedCount = 0;

        foreach ($unusedImages as $image) {
            if (! $image->isUsed()) {
                $filePath = $image->getFilePath();
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                $image->delete();
                $deletedCount++;
            }
        }

        return $deletedCount;
    }
}

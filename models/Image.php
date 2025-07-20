<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

class Image extends \yii\db\ActiveRecord
{
    const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const UPLOAD_DIR = '/images/storage/';

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
            [['filePath', 'modelName'], 'required'],
            [['itemId', 'isMain', 'sorted'], 'integer'],
            [['filePath'], 'string', 'max' => 400],
            [['modelName'], 'string', 'max' => 150],
            [['name', 'urlAlias'], 'string', 'max' => 255],
        ];
    }

    /**
     * Создать изображение из загруженного файла
     */
    public static function createFromUpload(UploadedFile $uploadedFile, $modelName, $itemId = null)
    {
        if (!$uploadedFile || $uploadedFile->error !== UPLOAD_ERR_OK) {
            return null;
        }

        // Проверяем размер файла
        if ($uploadedFile->size > self::MAX_FILE_SIZE) {
            throw new \Exception('Файл слишком большой. Максимальный размер: ' . (self::MAX_FILE_SIZE / 1024 / 1024) . 'MB');
        }

        // Проверяем расширение файла
        $extension = strtolower($uploadedFile->extension);
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            throw new \Exception('Недопустимый тип файла. Разрешены: ' . implode(', ', self::ALLOWED_EXTENSIONS));
        }

        // Генерируем безопасное имя файла
        $safeName = self::generateSafeName($uploadedFile->baseName);
        $fileName = $safeName . '.' . $extension;
        
        // Создаем директорию если не существует
        $fullUploadDir = Yii::getAlias('@webroot') . self::UPLOAD_DIR;
        if (!is_dir($fullUploadDir)) {
            mkdir($fullUploadDir, 0755, true);
        }

        // Обеспечиваем уникальность имени файла
        $fileName = self::ensureUniqueFileName($fullUploadDir, $fileName, $safeName, $extension);

        // Сохраняем файл
        $uploadPath = $fullUploadDir . $fileName;
        if ($uploadedFile->saveAs($uploadPath)) {
            // Создаем запись в БД
            $image = new self();
            $image->filePath = self::UPLOAD_DIR . $fileName;
            $image->name = $fileName;
            $image->modelName = $modelName;
            $image->itemId = $itemId;
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
     * Генерирует безопасное имя файла
     */
    private static function generateSafeName($originalName)
    {
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
        $safeName = trim($safeName, '_');
        
        if (empty($safeName)) {
            $safeName = 'image_' . time() . '_' . rand(1000, 9999);
        }
        
        return $safeName;
    }

    /**
     * Обеспечивает уникальность имени файла
     */
    private static function ensureUniqueFileName($uploadDir, $fileName, $safeName, $extension)
    {
        $counter = 1;
        while (file_exists($uploadDir . $fileName)) {
            $fileName = $safeName . '_' . $counter . '.' . $extension;
            $counter++;
        }
        return $fileName;
    }

    /**
     * Получить URL изображения
     */
    public function getUrl()
    {
        // Проверяем существование файла
        if (!$this->fileExists()) {
            return $this->getPlaceholderUrl();
        }

        // Для консольного приложения формируем простой URL
        if (!(Yii::$app instanceof \yii\web\Application)) {
            return $this->filePath;
        }

        $baseUrl = Yii::$app->request->baseUrl;
        $filePath = $this->filePath;

        // Убеждаемся, что filePath начинается с /
        if (!empty($filePath) && $filePath[0] !== '/') {
            $filePath = '/' . $filePath;
        }

        // Если baseUrl пустой или ".", убираем его
        if (empty($baseUrl) || $baseUrl === '.') {
            return $filePath;
        }

        return $baseUrl . $filePath;
    }

    /**
     * Получить полный путь к файлу
     */
    public function getFilePath()
    {
        return Yii::getAlias('@webroot') . $this->filePath;
    }

    /**
     * Проверить существование файла
     */
    public function fileExists()
    {
        return file_exists($this->getFilePath());
    }

    /**
     * Получить URL плейсхолдера
     */
    public function getPlaceholderUrl()
    {
        $baseUrl = (Yii::$app instanceof \yii\web\Application && Yii::$app->request)
            ? Yii::$app->request->baseUrl
            : '';
        return $baseUrl . '/images/placeholder.svg';
    }

    /**
     * Удалить изображение
     */
    public function deleteImage()
    {
        $filePath = $this->getFilePath();
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
        return $this->delete();
    }

    /**
     * Обновить связанную модель при изменении изображения
     */
    public function updateRelatedModel($oldImageId = null)
    {
        // Удаляем старое изображение если оно есть и отличается
        if ($oldImageId && $oldImageId != $this->id) {
            $oldImage = self::findOne($oldImageId);
            if ($oldImage) {
                $oldImage->deleteImage();
            }
        }
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
            if (!$image->isUsed()) {
                $image->deleteImage();
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    /**
     * Получить изображения по модели
     */
    public static function findByModel($modelName, $itemId = null)
    {
        $query = self::find()->where(['modelName' => $modelName]);
        
        if ($itemId !== null) {
            $query->andWhere(['itemId' => $itemId]);
        }
        
        return $query->orderBy(['isMain' => SORT_DESC, 'sorted' => SORT_ASC]);
    }
}

<?php

namespace app\models;

use Yii;

/**
 * @SWG\Definition(required={"id", "name", "color"})
 *
 * @SWG\Property(property="id", type="integer")
 * @SWG\Property(property="active", type="boolean")
 * @SWG\Property(property="name", type="string")
 * @SWG\Property(property="color", type="string")
 * @SWG\Property(property="image", type="file")
 */
class Category extends \yii\db\ActiveRecord
{
    public $image;

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            'id',
            'active',
            'name',
            'color',
            'image_path', // Оставляем для обратной совместимости
            'image_id'    // Новое поле для ссылки на Image
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active', 'image_id'], 'integer'],
            [['image'], 'file', 'extensions' => 'png, jpg, jpeg'],
            [['name', 'color'], 'required'],
            [['name', 'color', 'image_path'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'active' => 'Активность',
            'image' => 'Подложка',
            'image_path' => 'Путь к изображению',
            'name' => 'Название',
            'color' => 'Цвет'
        ];
    }
    public function upload()
    {
        if ($this->image && $this->image->error === UPLOAD_ERR_OK) {
            // Используем существующую таблицу image
            $image = Image::createFromUpload($this->image);

            if ($image) {
                // Связываем изображение с категорией
                $image->itemId = $this->id; // ID текущей категории
                $image->modelName = 'Category';
                $image->save();

                // Удаляем связь со старым изображением
                $oldImageId = $this->image_id;

                // Устанавливаем новое изображение
                $this->image_id = $image->id;
                $this->image_path = $image->filePath; // Дублируем для совместимости

                // Если было старое изображение, удаляем его
                if ($oldImageId && $oldImageId != $image->id) {
                    $oldImage = Image::findOne($oldImageId);
                    if ($oldImage) {
                        $filePath = $oldImage->getFilePath();
                        if (file_exists($filePath)) {
                            @unlink($filePath);
                        }
                        $oldImage->delete();
                    }
                }

                return true;
            }
        }
        return false;
    }

    /**
     * Связь с изображением
     */
    public function getImage()
    {
        return $this->hasOne(\app\models\Image::class, ['id' => 'image_id']);
    }

    /**
     * Получить прямую ссылку на изображение категории
     * @param string $size Размер изображения (не используется для прямых ссылок)
     * @return string URL изображения или плейсхолдер
     */
    public function getImageUrl($size = null)
    {
        // Сначала пробуем получить изображение через новую систему
        if ($this->image_id) {
            $image = $this->image; // Используем связь
            if ($image) {
                return $image->getUrl();
            }

            // Если связь не работает, пробуем найти изображение напрямую
            $image = Image::findOne($this->image_id);
            if ($image) {
                return $image->getUrl();
            }
        }

        // Fallback на старую систему для совместимости
        $imagePath = $this->getImagePath();
        if ($imagePath) {
            $fullImagePath = Yii::getAlias('@webroot/').$imagePath;
            if (file_exists($fullImagePath)) {
                $baseUrl = (Yii::$app instanceof \yii\web\Application && Yii::$app->request)
                    ? Yii::$app->request->baseUrl
                    : '';
                return $baseUrl.'/'.$imagePath;
            }
        }

        // Возвращаем плейсхолдер если изображение не найдено
        $baseUrl = (Yii::$app instanceof \yii\web\Application && Yii::$app->request)
            ? Yii::$app->request->baseUrl
            : '';
        return $baseUrl.'/images/placeholder.svg';
    }

    /**
     * Getter для image_path
     */
    public function getImagePath()
    {
        return $this->getAttribute('image_path');
    }

    /**
     * Setter для image_path  
     */
    public function setImagePath($value)
    {
        $this->setAttribute('image_path', $value);
    }
}

<?php

namespace app\models;

use Yii;

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
            'image_path',
            'image_id'
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
            try {
                $image = Image::createFromUpload($this->image, 'Category', null);

                if ($image) {
                    $oldImageId = $this->image_id;

                    // Устанавливаем новое изображение
                    $this->image_id = $image->id;
                    $this->image_path = $image->filePath;

                    // Удаляем старое изображение
                    $image->updateRelatedModel($oldImageId);

                    return true;
                }
            } catch (\Exception $e) {
                Yii::error('Ошибка загрузки изображения категории: '.$e->getMessage());
                return false;
            }
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Обновляем itemId в связанном изображении
        if ($this->image_id) {
            $image = Image::findOne($this->image_id);
            if ($image && $image->itemId != $this->id) {
                $image->itemId = $this->id;
                $image->save(false);
            }
        }
    }

    /**
     * Связь с изображением
     */
    public function getImage()
    {
        return $this->hasOne(Image::class, ['id' => 'image_id']);
    }

    /**
     * Получить URL изображения категории
     * @param string $size Размер изображения (не используется)
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
     * Getter для image_path (для совместимости)
     */
    public function getImagePath()
    {
        return $this->getAttribute('image_path');
    }

    /**
     * Setter для image_path (для совместимости)
     */
    public function setImagePath($value)
    {
        $this->setAttribute('image_path', $value);
    }
}

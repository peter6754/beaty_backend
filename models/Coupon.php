<?php

namespace app\models;

use Yii;

/**
 * @SWG\Definition(required={"id", "name"})
 *
 * @SWG\Property(property="id", type="integer")
 * @SWG\Property(property="name", type="string")
 * @SWG\Property(property="description", type="string")
 * @SWG\Property(property="amount", type="number")
 * @SWG\Property(property="price", type="number")
 * @SWG\Property(property="caregory", type="object", ref="#/definitions/Category")
 * @SWG\Property(property="image", type="string")
 */
class Coupon extends \yii\db\ActiveRecord
{
    public $image;

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            'id',
            'category_id',
            'name',
            'description',
            'amount',
            'price',
            'image_path'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'coupon';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'name', 'description', 'amount', 'price'], 'required'],
            [['category_id'], 'integer'],
            [['amount', 'price'], 'number'],
            [['image'], 'safe'], // Изменено с 'file' на 'safe' чтобы избежать автоматической валидации
            [['description'], 'string'],
            [['name', 'image_path'], 'string', 'max' => 255],
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Категория',
            'name' => 'Название',
            'description' => 'Описания',
            'amount' => 'Сумма купона',
            'price' => 'Цена купона',
            'image_path' => 'Путь к изображению',
        ];
    }

    public function upload()
    {
        if ($this->image && $this->image->error === UPLOAD_ERR_OK) {
            // Создаем папку если её нет
            $uploadDir = Yii::getAlias('@webroot/images/coupons/');
            if (! is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Безопасная очистка имени файла
            $originalName = $this->image->baseName;
            $extension = $this->image->extension;

            // Удаляем опасные символы и приводим к безопасному виду
            $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
            $safeName = trim($safeName, '_');

            // Если имя пустое после очистки, используем timestamp
            if (empty($safeName)) {
                $safeName = 'image_'.time();
            }

            // Проверяем уникальность и добавляем суффикс если нужно
            $fileName = $safeName.'.'.$extension;
            $uploadPath = $uploadDir.$fileName;
            $counter = 1;

            while (file_exists($uploadPath)) {
                $fileName = $safeName.'_'.$counter.'.'.$extension;
                $uploadPath = $uploadDir.$fileName;
                $counter++;
            }

            // Сохраняем файл
            if ($this->image->saveAs($uploadPath)) {
                // Удаляем старое изображение если есть
                $oldImagePath = $this->getImagePath();
                if ($oldImagePath) {
                    $oldPath = Yii::getAlias('@webroot/').$oldImagePath;
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                $this->setImagePath('images/coupons/'.$fileName);
                return true;
            }
        }
        return false;
    }

    /**
     * Получить прямую ссылку на изображение купона
     * @param string $size Размер изображения (не используется для прямых ссылок)
     * @return string URL изображения или плейсхолдер
     */
    public function getImageUrl($size = null)
    {
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

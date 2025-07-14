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
            'image_path'
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
            [['active'], 'integer'],
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
        if ($this->image) {
            $fileName = uniqid().'.'.$this->image->extension;
            $uploadPath = Yii::getAlias('@webroot/images/categories/').$fileName;

            if ($this->image->saveAs($uploadPath)) {
                // Удаляем старое изображение если есть
                $oldImagePath = $this->getImagePath();
                if ($oldImagePath) {
                    $oldPath = Yii::getAlias('@webroot/').$oldImagePath;
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                $this->setImagePath('images/categories/'.$fileName);
                return true;
            }
        }
        return false;
    }

    /**
     * Получить прямую ссылку на изображение категории
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

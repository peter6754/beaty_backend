<?php

namespace app\models;

use Yii;

class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'name', 'price'], 'required'],
            [['category_id', 'price'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'price' => 'Цена',
        ];
    }
}

<?php

namespace app\models;

use Yii;

/**
 * @SWG\Definition(required={"phone", "name"})
 *
 * @SWG\Property(property="id", type="integer")
 * @SWG\Property(property="name", type="string")
 * @SWG\Property(property="email", type="string")
 * @SWG\Property(property="phone", type="string")
 * @SWG\Property(property="image", type="file")
 * @SWG\Property(property="token", type="string")
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{

    public $image;

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            'id',
            'phone',
            'name',
            'email',
            'fcm_token',
            'token',
            'password'
        ];
    }

    public function behaviors()
    {
        return [
            // ImageBehave removed - no longer using yii2images module
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['image'], 'file', 'extensions' => 'png, jpg, jpeg'],
            ['phone', 'match', 'pattern' => '/^\+7\([0-9]{3}\)\s[0-9]{3}\-[0-9]{2}\-[0-9]{2}$/', 'message' => 'Пример: +7(999) 999-99-99'],
            ['email', 'email'],
            ['email', 'unique'],
            [['email', 'token', 'fcm_token', 'password'], 'string', 'max' => 255],
        ];
    }

    static function maskPhone($number)
    {
        return sprintf("+%s(%s) %s-%s-%s",
            substr($number, 0, 1),
            substr($number, 1, 3),
            substr($number, 4, 3),
            substr($number, 7, 2),
            substr($number, 9)
        );
    }

    public function afterFind()
    {
        $this->phone = $this->maskPhone($this->phone);
    }

    public function beforeSave($insert)
    {
        $this->phone = preg_replace('/[^0-9]/', '', $this->phone);

        if (parent::beforeSave($insert)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Телефон',
            'email' => 'Электронная почта',
            'image' => 'Аватар',
            'name' => 'Имя',
            'token' => 'Token',
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->token;
    }

    public function validateAuthKey($authKey)
    {
        return $this->token === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    public function upload()
    {
        if ($this->image) {
            $path = 'uploads/'.$this->image->baseName.'.'.$this->image->extension;
            $this->image->saveAs($path);
            // TODO: Implement new image handling logic if needed
            // attachImage method was removed with yii2images module
        }
    }
}

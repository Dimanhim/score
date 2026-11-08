<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property int $id
 * @property string $username
 * @property string $password password hash
 * @property string $role
 */
class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public static function findIdentity($id): ?static
    {
        return static::findOne((int) $id);
    }

    public static function findIdentityByAccessToken($token, $type = null): ?static
    {
        return null;
    }

    public static function findByUsername(string $username): ?static
    {
        return static::findOne(['username' => $username]);
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * The table has no auth_key column, so the key is derived from the password hash:
     * changing the password invalidates every "remember me" cookie.
     */
    public function getAuthKey(): string
    {
        return hash_hmac('sha256', $this->id . '|' . $this->password, Yii::$app->request->cookieValidationKey);
    }

    public function validateAuthKey($authKey): bool
    {
        return is_string($authKey) && hash_equals($this->getAuthKey(), $authKey);
    }

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
}

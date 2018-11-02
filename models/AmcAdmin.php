<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_admin".
 *
 * @property int 	$id
 * @property string $username 账号
 * @property string $password 密码
 * @property string $name 姓名
 * @property string $createtime 注册时间
 * @property string $remark 备注
 * @property int	$status 状态 1 使用 2 停用
 * @property string $permission 权限
 */
class AmcAdmin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amc_admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','username','password','createtime'], 'required'],
            [['id', 'status'], 'integer'],
            [['username', 'password', 'name'], 'string', 'max' => 20],
            [['createtime'], 'string', 'max' => 12],
            [['remark', 'permission'], 'string', 'max' => 200],
            [['id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'name' => 'Name',
            'status' => 'Status',
            'createtime' => 'Createtime',
            'remark' => 'Remark',      
            'permission' => 'Permission',
        ];
    }
}

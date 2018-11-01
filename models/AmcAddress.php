<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_address".
 *
 * @property int 	$id
 * @property string $address 地址
 * @property string $phone 电话
 * @property int 	$uid 用户表id
 * @property int 	$isdefault 默认地址 1不是 2是
 * @property string $createtime 注册时间
 * @property int 	$zipcode 邮编
 */
class AmcAddress extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amc_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','address','phone','createtime','uid','zipcode'], 'required'],
            [['id', 'uid', 'isdefault', 'zipcode'], 'integer'],
            [['phone', 'createtime'], 'string', 'max' => 12],
            [['address'], 'string', 'max' => 200],
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
            'address' => 'Address',
            'phone' => 'Phone',
            'uid' => 'Uid',
            'isdefault' => 'Isdefault',
            'createtime' => 'Createtime',
            'zipcode' => 'Zipcode',          
        ];
    }
}

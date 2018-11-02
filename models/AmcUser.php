<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_user".
 *
 * @property int 	$id
 * @property string $phone 账号手机号
 * @property string $password 密码
 * @property string $name 姓名
 * @property int 	$status 状态 1 使用 2 暂停使用
 * @property string $createtime 注册时间
 * @property string $remark 备注
 * @property int 	$money 现金券
 * @property int 	$hdlock 待用hd
 * @property int 	$hdcirculate 可用hd
 * @property int 	$amclock 待用amc
 * @property int 	$amccirculate 可用amc
 * @property int 	$eth 
 * @property int 	$btc 
 * @property int 	$referrallevel 层级
 * @property string $groupnum 组号(用户id)
 * @property int 	$reccount 发展人数
 * @property string $referralnum 邀请码(会员phone)
 * @property int 	$uploadpermission 上传应用商城商品权限 1 不可以 2 可以
 * @property string $cardnum 身份证
 * @property int	$isreal 是否实名 1 不是 2 是
 * @property string $permission 权限
 */
class AmcUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amc_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','phone','password','createtime','groupnum'], 'required'],
            [['id', 'status', 'referrallevel', 'reccount', 'uploadpermission', 'isreal'], 'integer'],
            [['money', 'hdlock', 'hdcirculate', 'amclock', 'amccirculate', 'eth', 'btc'], 'number'],
            [['phone', 'name', 'createtime', 'groupnum', 'referralnum'], 'string', 'max' => 12],
            [['cardnum'], 'string', 'max' => 18],
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
            'phone' => 'Phone',
            'password' => 'Password',
            'name' => 'Name',
            'status' => 'Status',
            'createtime' => 'Createtime',
            'remark' => 'Remark',
            'money' => 'Money',
            'hdlock' => 'Hdlock',
            'hdcirculate' => 'Hdcirculate',
            'amclock' => 'Amclock',
            'amccirculate' => 'Amccirculate',
            'eth' => 'Eth',
            'btc' => 'Btc',
            'referrallevel' => 'Referrallevel',
            'groupnum' => 'Groupnum',
            'reccount' => 'Reccount',
            'referralnum' => 'Referralnum',
            'uploadpermission' => 'Uploadpermission',
            'cardnum ' => 'Cardnum ',
            'isreal' => 'Isreal',
            'permission' => 'Permission',
        ];
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_user".
 *
 * @property int $id
 * @property string $phone 手机号/账号
 * @property string $password 密码
 * @property string $name 姓名
 * @property int $status 状态 1 使用 2 暂停使用
 * @property string $createtime 注册时间
 * @property string $remark 备注
 * @property string $money 现金券
 * @property string $hdlock 待用hd
 * @property string $hdcirculate 可用hd
 * @property string $amclock 待用amc
 * @property string $amccirculate 可用amc
 * @property string $eth
 * @property string $btc
 * @property int $referrallevel 层级
 * @property string $groupnum 组号(用户id)
 * @property int $reccount 发展人数
 * @property string $referralnum 邀请码(会员phone)
 * @property int $uploadpermission 上传应用商城商品权限 1 不可以 2 可以
 * @property string $cardnum 身份证
 * @property int $isreal 是否实名 1 不是 2 是
 * @property string $permission 权限
 * @property string $avatar 头像
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
	
	public $userlv;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone', 'password'], 'required'],
            [['status', 'referrallevel', 'reccount', 'uploadpermission', 'isreal'], 'integer'],
            [['createtime'], 'safe'],
            [['money', 'hdlock', 'hdcirculate', 'amclock', 'amccirculate', 'eth', 'btc'], 'number'],
            [['phone', 'name', 'groupnum', 'referralnum'], 'string', 'max' => 12],
            [[ 'cardnum'], 'string', 'max' => 18],
            [['password'], 'string', 'max' => 50],
            [['remark', 'permission'], 'string', 'max' => 200],
            [['avatar'], 'string', 'max' => 100],
            [['phone','password','token','referralnum','avatar'],'safe']
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
            'cardnum' => 'Cardnum',
            'isreal' => 'Isreal',
            'permission' => 'Permission',
            'avatar' => 'Avatar',
        ];
    }

    public static function findByPhone($phone){
        return AmcUser::find()->where(['phone'=>$phone, 'status'=>1])->one();
    }


    public static function findById($id){
        return AmcUser::find()->where(['id'=>$id])->one();
    }
}

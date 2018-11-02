<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_recharge_log".
 *
 * @property int $id
 * @property string $tpayertel 转账方手机号
 * @property int $treceiverid 接收方用户ID
 * @property string $treceivertel 接收方手机号
 * @property string $tpremoney 税前金额
 * @property int $tpayerid 转账方用户ID
 * @property string $taccountaddress 钱包地址
 * @property int $taccounttype 钱包类型；1=现金券；2=HD;3=AMC;4=BTC;5=ETH
 * @property int $ttype 类型：1=新增；2=减少
 * @property string $tmemo 说明
 * @property string $ttitle 标题
 * @property int $tstate 状态1=生效；0=待生效；9=作废记录
 * @property string $tmoney 金额
 */
class AmcRechargeLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amc_recharge_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'treceiverid', 'tpayerid', 'taccounttype', 'ttype', 'tstate'], 'integer'],
            [['tpremoney', 'tmoney'], 'number'],
            [['tpayertel', 'treceivertel'], 'string', 'max' => 11],
            [['taccountaddress', 'ttitle'], 'string', 'max' => 255],
            [['tmemo'], 'string', 'max' => 2000],
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
            'tpayertel' => 'Tpayertel',
            'treceiverid' => 'Treceiverid',
            'treceivertel' => 'Treceivertel',
            'tpremoney' => 'Tpremoney',
            'tpayerid' => 'Tpayerid',
            'taccountaddress' => 'Taccountaddress',
            'taccounttype' => 'Taccounttype',
            'ttype' => 'Ttype',
            'tmemo' => 'Tmemo',
            'ttitle' => 'Ttitle',
            'tstate' => 'Tstate',
            'tmoney' => 'Tmoney',
        ];
    }
}

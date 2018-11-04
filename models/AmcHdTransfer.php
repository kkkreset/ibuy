<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_hd_transfer".
 *
 * @property int $id
 * @property int $code
 * @property string $payertel 转账方手机号
 * @property int $receiverid 接收方用户ID
 * @property string $receivertel 接收方手机号
 * @property string $premoney 税前金额
 * @property int $payerid 转账方用户ID
 * @property string $accountaddress 钱包地址
 * @property int $accounttype 钱包类型；1=现金券；2=HD;3=AMC;4=BTC;5=ETH
 * @property string $memo 说明
 * @property string $title 标题
 * @property string $addtime 记录产生时间
 * @property int $state 状态1=生效；0=待生效；9=作废记录
 * @property string $money 金额
 */
class AmcHdTransfer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amc_hd_transfer';
    }


    public static function tableNameExp($exp)
    {
        return 'amc_hd_transfer '.$exp;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'code', 'receiverid', 'payerid', 'accounttype', 'state'], 'integer'],
            [['premoney', 'money'], 'number'],
            [['addtime'], 'safe'],
            [['payertel', 'receivertel'], 'string', 'max' => 11],
            [['accountaddress', 'title'], 'string', 'max' => 255],
            [['memo'], 'string', 'max' => 2000],
            [['id'], 'unique'],
        ];
    }



    public static function findAll($condition=[], $page=1, $pagesize=10) {
        $query = AmcHdTransfer::find();
        $query->from(self::tableNameExp('t'));
        $query->select('t.*');
        $query->where('payerid = '. $condition['payerid']);
        $count = $query->count();
        $query->orderBy('t.id desc');
        $query->offset(0);
        if($page > 1) {
            $query->offset(($page - 1) * $pagesize);
        }
        $query->limit($pagesize);
        $data = $query->all();
        return compact('count', 'data');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'payertel' => 'Payertel',
            'receiverid' => 'Receiverid',
            'receivertel' => 'Receivertel',
            'premoney' => 'Premoney',
            'payerid' => 'Payerid',
            'accountaddress' => 'Accountaddress',
            'accounttype' => 'Accounttype',
            'memo' => 'Memo',
            'title' => 'Title',
            'addtime' => 'Addtime',
            'state' => 'State',
            'money' => 'Money',
        ];
    }


    public static function findByHd($id) {
        $query = AmcHdTransfer::find();
        $query->from(self::tableNameExp('t'));
        $query->select('t.*');
        $query->andWhere('id = '.$id);
        return $query->one();
    }
}

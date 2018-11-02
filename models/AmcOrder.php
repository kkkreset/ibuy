<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_order".
 *
 * @property int $id
 * @property string $ocode 订单编号
 * @property int $uid 用户ID
 * @property int $pid 产品ID
 * @property string $ptitle 产品标题
 * @property string $pimg 产品图片
 * @property int $shop_id 店铺ID
 * @property string $mprice 邮费
 * @property string $pay_hd 实付HD
 * @property string $pay_price 实付金额
 * @property string $price 应付金额
 * @property int $address_id 地址ID
 * @property string $pname 收件人姓名
 * @property string $phone 收件人联系方式
 * @property string $province 省
 * @property string $city 市
 * @property string $county 县城
 * @property string $address 详细地址
 * @property string $pay_time 支付时间
 * @property string $addtime 下单时间
 */
class AmcOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amc_order';
    }

    public static function tableNameExp($exp)
    {
        return 'amc_order '.$exp;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ocode', 'uid', 'pid'], 'required'],
            [['uid', 'pid', 'shop_id', 'address_id'], 'integer'],
            [['mprice', 'pay_hd', 'pay_price', 'price'], 'number'],
            [['pay_time', 'addtime'], 'safe'],
            [['ocode', 'pname', 'phone', 'province', 'city', 'county'], 'string', 'max' => 50],
            [['ptitle', 'pimg'], 'string', 'max' => 100],
            [['address'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ocode' => 'Ocode',
            'uid' => 'Uid',
            'pid' => 'Pid',
            'ptitle' => 'Ptitle',
            'pimg' => 'Pimg',
            'shop_id' => 'Shop ID',
            'mprice' => 'Mprice',
            'pay_hd' => 'Pay Hd',
            'pay_price' => 'Pay Price',
            'price' => 'Price',
            'address_id' => 'Address ID',
            'pname' => 'Pname',
            'phone' => 'Phone',
            'province' => 'Province',
            'city' => 'City',
            'county' => 'County',
            'address' => 'Address',
            'pay_time' => 'Pay Time',
            'addtime' => 'Addtime',
        ];
    }

    public static function findAll($condition=[], $page=1, $pagesize=10) {
        $query = AmcOrder::find();
        $query->from(self::tableNameExp('t'));
        $query->select('t.*');
        foreach ($condition as $k => $v) {
            if($v) {
                switch ($k) {
                    case 'ocode':
                        $query->andWhere(['like','ocode',$v]);
                        break;
                    case 'status':
                        $query->andWhere('status = '.$v);
                        break;
                }
            }
        }
        $count = $query->count();
        $query->orderBy('t.id desc');
        if($page <= 1) {
            $limit = ' LIMIT 0,'.$pagesize;
        }else{
            $limit = ' LIMIT '.(($page - 1) * $pagesize).','.$pagesize;
        }
        $data = $query->all();
        return compact('count', 'data');
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_product_shop".
 *
 * @property int $id
 * @property int $uid 用户ID
 * @property int $pid 商品ID
 * @property int $number 数量
 * @property string $shop_name 商店名
 * @property string $pname 商品名
 * @property string $img 商品图片
 * @property string $ctime 创建时间
 */
class AmcProductShop extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amc_product_shop';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'pid', 'number'], 'integer'],
            [['ctime'], 'safe'],
            [['shop_name', 'pname', 'img'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'pid' => 'Pid',
            'number' => 'Number',
            'shop_name' => 'Shop Name',
            'pname' => 'Pname',
            'img' => 'Img',
            'ctime' => 'Ctime',
        ];
    }

    public static function findByAll($condition=[], $page=1, $pagesize=10) {
        $query = AmcProductShop::find();
        $query->from('amc_product_shop t');
        $query->select('t.*');
        foreach ($condition as $k => $v) {
            if($v) {
                switch ($k) {
                    case 'uid':
                        $query->andWhere('t.uid = '.$v);
                        break;
                    default:
                        break;
                }
            }
        }
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

    }
}

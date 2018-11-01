<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_product".
 *
 * @property int $id
 * @property string $title 产品名
 * @property string $title_c 产品副标题
 * @property int $level 产品商城属性
 * @property string $level_title
 * @property int $category_id 分类ID
 * @property int $f_shop_id 总店ID
 * @property int $shop_id 店铺ID
 * @property string $price 价格
 * @property string $r_price 原价
 * @property int $storage 库存
 * @property string $hd_price HD价格
 * @property string $m_price 运费
 * @property string $img 图片
 * @property string $desc 图文详情
 * @property string $addtime 录入时间
 * @property int $is_view 是否可见0不可1可见
 * @property int $is_del 是否删除0不删1删除
 * @property int $is_hot 是否热门0不热1热门
 */
class AmcProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amc_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'level', 'category_id', 'img'], 'required'],
            [['level', 'category_id', 'f_shop_id', 'shop_id', 'storage'], 'integer'],
            [['price', 'r_price', 'hd_price', 'm_price'], 'number'],
            [['desc'], 'string'],
            [['addtime'], 'safe'],
            [['title', 'title_c', 'img'], 'string', 'max' => 100],
            [['level_title'], 'string', 'max' => 50],
            [['is_view', 'is_del', 'is_hot'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '产品名',
            'title_c' => '产品副标题',
            'level' => '产品商城属性',
            'level_title' => 'Level Title',
            'category_id' => '分类ID',
            'f_shop_id' => '总店ID',
            'shop_id' => '店铺ID',
            'price' => '价格',
            'r_price' => '原价',
            'storage' => '库存',
            'hd_price' => 'HD价格',
            'm_price' => '运费',
            'img' => '图片',
            'desc' => '图文详情',
            'addtime' => '录入时间',
            'is_view' => '是否可见0不可1可见',
            'is_del' => '是否删除0不删1删除',
            'is_hot' => '是否热门0不热1热门',
        ];
    }

    /**
     * @inheritdoc
     * @return AmcProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AmcProductQuery(get_called_class());
    }
}

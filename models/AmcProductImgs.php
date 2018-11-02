<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_product_imgs".
 *
 * @property int $id
 * @property int $p_id 产品ID
 * @property string $img 图片地址
 * @property int $is_del 是否删除
 */
class AmcProductImgs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amc_product_imgs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'img'], 'required'],
            [['pid'], 'integer'],
            [['img'], 'string', 'max' => 100],
            [['is_del'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'p_id' => 'P ID',
            'img' => 'Img',
            'is_del' => 'Is Del',
        ];
    }

    public static function findAllByPid($pid) {
        return AmcProductImgs::find()->where(['pid'=>$pid,'is_del'=>0])->orderBy('id asc')->all();
    } 
}

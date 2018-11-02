<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_product_category".
 *
 * @property int $id
 * @property string $title 分类名
 * @property int $r_id 父类分类ID
 * @property int $is_del 是否删除
 */
class AmcProductCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amc_product_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['r_id'], 'integer'],
            [['title'], 'string', 'max' => 50],
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
            'title' => 'Title',
            'r_id' => 'R ID',
            'is_del' => 'Is Del',
        ];
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_notice".
 *
 * @property int $id
 * @property string $title 标题
 * @property string $desc 描述
 * @property string $img 图片
 * @property int $is_hot 是否热门
 * @property int $is_del 是否删除
 * @property string $addtime 录入时间
 */
class AmcNotice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amc_notice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'img'], 'required'],
            [['addtime'], 'safe'],
            [['title', 'img'], 'string', 'max' => 100],
            [['desc'], 'string', 'max' => 250],
            [['is_hot', 'is_del'], 'string', 'max' => 1],
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
            'desc' => 'Desc',
            'img' => 'Img',
            'is_hot' => 'Is Hot',
            'is_del' => 'Is Del',
            'addtime' => 'Addtime',
        ];
    }
}

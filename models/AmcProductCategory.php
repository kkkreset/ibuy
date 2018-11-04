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

    public function findByAll($condition=[],$page=1,$pagesize=10) {
        $query = AmcProductCategory::find();
        $query->from('amc_product_category t');
        if($condition->type != 'all') {
            $query->where('t.r_id = '.$condition->type);
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

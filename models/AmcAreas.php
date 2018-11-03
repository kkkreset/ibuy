<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_areas".
 *
 * @property int 	$id
 * @property string $areaid 区域编码
 * @property string $area 区县名称
 * @property int 	$cityid 所属城市编码
 */
class AmcAreas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amc_areas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'areaid' => 'Areaid',
            'area' => 'Area',
            'cityid' => 'Cityid',       
        ];
    }
}

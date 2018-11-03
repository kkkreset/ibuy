<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_cities".
 *
 * @property int 	$id
 * @property string $cityid 城市编码
 * @property string $city 城市名称
 * @property int 	$provinceid 所属省份编码
 */
class AmcCities extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amc_cities';
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
            'cityid' => 'Cityid',
            'city' => 'City',
            'provinceid' => 'Provinceid',        
        ];
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_provinces".
 *
 * @property int 	$id
 * @property string $provinceid 省id
 * @property string $province 省
 */
class AmcProvinces extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amc_provinces';
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
            'provinceid' => 'Provinceid',
            'province' => 'Province',        
        ];
    }
}

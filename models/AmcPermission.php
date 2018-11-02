<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_permission".
 *
 * @property int 	$id
 * @property string $permissionname 权限名称 
 * @property int 	$type 权限所属 1用户表 2 管理员表
 */
class AmcPermission extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amc_permission';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','permissionname','type'], 'required'],
            [['id', 'type'], 'integer'],
            [['permissionname'], 'string', 'max' => 20],
            [['id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'permissionname' => 'Permissionname',
            'type' => 'Type',          
        ];
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_hd_transfer_log".
 *
 * @property int $id
 * @property int $rechcode 编码
 * @property string $rmobile 手机号
 * @property int $rtype 类型1=增加；2=减少
 * @property string $rmoney 金额
 * @property string $rpremoney 税前金额
 * @property string $rpasstime 处理时间
 * @property string $raddtime 添加时间
 * @property int $userid 用户ID
 * @property string $raccount 交易账号
 * @property string $rtitle 标题
 * @property int $rstate 状态1=有效；9=失效;0=待处理
 */
class AmcHdTransferLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amc_hd_transfer_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'rtype'], 'required'],
            [['id', 'rechcode', 'rtype', 'userid', 'rstate'], 'integer'],
            [['rmoney', 'rpremoney'], 'number'],
            [['rpasstime', 'raddtime'], 'safe'],
            [['rmobile', 'raccount'], 'string', 'max' => 11],
            [['rtitle'], 'string', 'max' => 255],
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
            'rechcode' => 'Rechcode',
            'rmobile' => 'Rmobile',
            'rtype' => 'Rtype',
            'rmoney' => 'Rmoney',
            'rpremoney' => 'Rpremoney',
            'rpasstime' => 'Rpasstime',
            'raddtime' => 'Raddtime',
            'userid' => 'Userid',
            'raccount' => 'Raccount',
            'rtitle' => 'Rtitle',
            'rstate' => 'Rstate',
        ];
    }
}

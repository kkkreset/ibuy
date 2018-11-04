<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amc_recharge".
 *
 * @property int $rid
 * @property int $rechcode 编码
 * @property string $rmobile 手机号
 * @property int $rtype 类型1=现金充值
 * @property string $rbank 开户行
 * @property string $rbankname 开户人
 * @property string $rbankadderss 开户行
 * @property string $rmoney 金额
 * @property string $rpremoney 税前金额
 * @property string $rpasstime 审核时间
 * @property string $raddtime 添加时间
 * @property string $raccount 银行账号
 * @property int $userid 用户ID
 * @property string $rtitle 标题
 * @property int $rstate 状态0=未处理；1=审核通过；2=审核未通过
 */
class AmcRecharge extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amc_recharge';
    }


    public static function tableNameExp($exp)
    {
        return 'amc_recharge '.$exp;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rid', 'rechcode', 'rtype', 'userid', 'rstate'], 'integer'],
            [['rmoney', 'rpremoney'], 'number'],
            [['rpasstime', 'raddtime'], 'safe'],
            [['rmobile'], 'string', 'max' => 11],
            [['rbank', 'rbankname', 'rbankadderss', 'raccount', 'rtitle'], 'string', 'max' => 255],
            [['rid'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rid' => 'Rid',
            'rechcode' => 'Rechcode',
            'rmobile' => 'Rmobile',
            'rtype' => 'Rtype',
            'rbank' => 'Rbank',
            'rbankname' => 'Rbankname',
            'rbankadderss' => 'Rbankadderss',
            'rmoney' => 'Rmoney',
            'rpremoney' => 'Rpremoney',
            'rpasstime' => 'Rpasstime',
            'raddtime' => 'Raddtime',
            'raccount' => 'Raccount',
            'userid' => 'Userid',
            'rtitle' => 'Rtitle',
            'rstate' => 'Rstate',
        ];
    }


    public static function findAll($condition=[], $page=1, $pagesize=10) {
        $query = AmcRecharge::find();
        $query->from(self::tableNameExp('t'));
        $query->select('t.*');
        $query->where('userid = '. $condition['userid']);
        $count = $query->count();
        $query->orderBy('t.rid desc');
        $query->offset(0);
        if($page > 1) {
            $query->offset(($page - 1) * $pagesize);
        }
        $query->limit($pagesize);
        $data = $query->all();
        return compact('count', 'data');
    }

    public static function findByCharge($id) {
        $query = AmcRecharge::find();
        $query->from(self::tableNameExp('t'));
        $query->select('t.*');
        $query->andWhere('rid = '.$id);
        return $query->one();
    }
}

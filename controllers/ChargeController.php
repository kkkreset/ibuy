<?php

namespace app\controllers;

use app\commands\BaseController;
use app\commands\F;
use app\commands\Consts;
use app\models\AmcRecharge;
use app\models\AmcRechargeLog;
use app\models\AmcUser;

class ChargeController extends BaseController
{
    /**
     * 现金券列表
     */
    public function actionList()
    {
        $dataObj  = isset($this->jData) ? $this->jData : '';
        $page     = isset($dataObj->page) ? $dataObj->page : 1;
        $pageSize = isset($dataObj->pagesize) ? $dataObj->pagesize : 10;

        $list    = AmcRecharge::findAll(['userid' => $this->user->id], $page, $pageSize);
        $dataArr = F::newModelToArr($list['data']);

        $dataArr['nextPageNo'] = F::pages($list['count'], $page, $pageSize);
        $dataArr['totalPages'] = $list['count'];

        return F::buildJsonData(0, Consts::msgInfo(), $dataArr);
    }


    /**
     * 现金转账申请
     */
    public function actionApply()
    {
        $dataObj = isset($this->jData) ? $this->jData : '';
        $money   = isset($dataObj->money) ? $dataObj->money : 0;
        $account = isset($dataObj->account) ? $dataObj->account : 0;

        if (!$money || !$account) {
            return F::buildJsonData(10010, Consts::msgInfo(10010));
        }

        /* 查询接收方账户，检查输入的账户是否正确 */
        $userAccount = AmcUser::find()->where(['phone' => $account])->one();
        if (!$userAccount) {
            return F::buildJsonData(10021, Consts::msgInfo(10021));
        }

        /* 检查当前用户现金券余额是否充足 */
        $uid     = $this->user->id;
        $userObj = AmcUser::find()->where(['id' => $uid])->one();
        if ($userObj['money'] < $money) {
            return F::buildJsonData(40001, Consts::msgInfo(40001));
        }

        $chargeObj            = new AmcRecharge();
        $chargeObj->rechcode  = F::randStr(20, 'NUMBER');
        $chargeObj->rmobile   = $account;
        $chargeObj->rmoney    = $money;
        $chargeObj->rpremoney = $money;
        $chargeObj->raddtime  = date('Y-m-d H:i:s', time());
        $chargeObj->userid    = $userAccount['id'];
        $chargeObj->rstate    = 0;
        $chargeObj->rtitle    = "现金转账";

        $chargeLogObj               = new AmcRechargeLog();
        $chargeLogObj->tpayertel    = $userObj['phone'];
        $chargeLogObj->treceiverid  = $userAccount['id'];
        $chargeLogObj->treceivertel = $userAccount['phone'];
        $chargeLogObj->tpremoney    = $money;
        $chargeLogObj->tpayerid     = $userObj['id'];
        $chargeLogObj->taccounttype = 1;
        $chargeLogObj->ttype        = 1;
        $chargeLogObj->tstate       = 0;
        $chargeLogObj->tmoney       = $money;
        $chargeLogObj->ttitle       = "现金转账";


        if (!$chargeObj->validate()) {
            return F::buildJsonData(3, Consts::msgInfo(3));
        }
        if (!$chargeObj->save()) {
            return F::buildJsonData(3, Consts::msgInfo(3));
        }

        if (!$chargeLogObj->validate()) {
            return F::buildJsonData(3, Consts::msgInfo(3));
        }
        if (!$chargeLogObj->save()) {
            return F::buildJsonData(3, Consts::msgInfo(3));
        }

        return F::buildJsonData(0, Consts::msgInfo());

    }

    /**
     * 现金券详情
     */
    public function actionInfo()
    {
        $dataObj = isset($this->jData) ? $this->jData : '';
        $rid     = isset($dataObj->rid) ? $dataObj->rid : 0;

        if (!$rid) {
            return F::buildJsonData(4, Consts::msgInfo(4));
        }

        $detail = AmcRecharge::findByCharge($rid);
        if ($detail->attributes) {
            $data = F::objectModelToArr($detail->attributes);

            return F::buildJsonData(0, Consts::msgInfo(), $data);
        }

        return F::buildJsonData(0, Consts::msgInfo());
    }


}
















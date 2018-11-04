<?php

namespace app\controllers;

use app\commands\BaseController;
use app\commands\F;
use app\commands\Consts;
use app\models\AmcUser;
use app\models\AmcHdTransfer;
use app\models\AmcHdTransferLog;

class HdController extends BaseController
{
    /**
     * 记录列表
     */
    public function actionList()
    {
        $dataObj  = isset($this->jData) ? $this->jData : '';
        $page     = isset($dataObj->page) ? $dataObj->page : 1;
        $pageSize = isset($dataObj->pagesize) ? $dataObj->pagesize : 10;

        $list    = AmcHdTransfer::findAll(['payerid' => $this->user->id], $page, $pageSize);
        $dataArr = F::newModelToArr($list['data']);

        $dataArr['nextPageNo'] = F::pages($list['count'], $page, $pageSize);
        $dataArr['totalPages'] = $list['count'];

        return F::buildJsonData(0, Consts::msgInfo(), $dataArr);

    }


    /**
     * Hd流通转账
     */
    public function actionTransfer()
    {
        $dataObj = isset($this->jData) ? $this->jData : '';
        $num     = isset($dataObj->num) ? $dataObj->num : 0;
        $account = isset($dataObj->account) ? $dataObj->account : 0;

        if (!$num || !$account) {
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
        if ($userObj['hdcirculate'] < $num) {
            return F::buildJsonData(40002, Consts::msgInfo(40002));
        }

        $hdObj              = new AmcHdTransfer();
        $hdObj->code        = F::randStr(20, 'NUMBER');
        $hdObj->payertel    = $userObj['phone'];
        $hdObj->receiverid  = $userAccount['id'];
        $hdObj->receivertel = $userAccount['phone'];
        $hdObj->premoney    = $num;
        $hdObj->payerid     = $userObj['id'];
        $hdObj->accounttype = 2;
        $hdObj->state       = 1;
        $hdObj->money       = $num;
        $hdObj->title       = "转账";

        $hdLogObj            = new AmcHdTransferLog();
        $hdLogObj->rechcode  = F::randStr(20, 'NUMBER');
        $hdLogObj->rmobile   = $userAccount['phone'];
        $hdLogObj->rtype     = 1;
        $hdLogObj->rmoney    = $num;
        $hdLogObj->rpremoney = $num;
        $hdLogObj->raddtime  = date('Y-m-d H:i:s', time());
        $hdLogObj->userid    = $userAccount['id'];
        $hdLogObj->rstate    = 1;
        $hdLogObj->rtitle    = "转账";


        if (!$hdObj->validate()) {
            return F::buildJsonData(31, Consts::msgInfo(3));
        }
        if (!$hdObj->save()) {
            return F::buildJsonData(32, Consts::msgInfo(3));
        }

        if (!$hdLogObj->validate()) {
            return F::buildJsonData(33, Consts::msgInfo(3));
        }
        if (!$hdLogObj->save()) {
            return F::buildJsonData(34, Consts::msgInfo(3));
        }

        //减少/增加相应的流通HD值
        $userObject             = new AmcUser();
        $userModel              = $userObject->findById($uid);
        $userModel->hdcirculate -= $num;
        $userModel->save();

        $fuserModel              = $userObject->findByPhone($account);
        $fuserModel->hdcirculate += $num;
        $fuserModel->save();

        return F::buildJsonData(0, Consts::msgInfo());

    }


    /**
     * 详情
     */
    public function actionInfo()
    {
        $dataObj = isset($this->jData) ? $this->jData : '';
        $id      = isset($dataObj->id) ? $dataObj->id : 0;

        if (!$id) {
            return F::buildJsonData(4, Consts::msgInfo(4));
        }

        $detail = AmcHdTransfer::findByHd($id);
        if ($detail->attributes) {
            $data = F::objectModelToArr($detail->attributes);

            return F::buildJsonData(0, Consts::msgInfo(), $data);
        }

        return F::buildJsonData(0, Consts::msgInfo());
    }


}
















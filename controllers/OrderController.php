<?php

namespace app\controllers;

use app\commands\BaseController;
use app\commands\F;
use app\commands\Consts;
use app\models\AmcOrder;
use app\models\AmcUser;

class OrderController extends BaseController{


	public function actionList() {
		$dataObj = isset($this->jData)?$this->jData:'';
		$page = isset($dataObj->page)?$dataObj->page: 1;
		$pageSize = isset($dataObj->pagesize)?$dataObj->pagesize: 10;
		$status = isset($dataObj->status)?$dataObj->status: 0;

		$list = AmcOrder::findAll(['uid'=>$this->user->id,'status'=>$status], $page, $pagesize);
		$dataArr = F::newModelToArr($list['data']);

		$dataArr['nextPageNo'] = F::pages($list['count'], $page, $pageSize);
		$dataArr['totalPages'] = $list['count'];

		return F::buildJsonData(0, Consts::msgInfo(), $dataArr);
	}

	public function actionOrder() {
		$phone = $this->token->getClaim('phone');
        if(!$phone || $phone == ''){
            return F::buildJsonData(10022, Consts::msgInfo(10022));exit;
        }

	}
}
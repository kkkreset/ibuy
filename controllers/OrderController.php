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
		$condition = empty($dataObj->condition)?[]:$dataObj->condition;

		$list = AmcOrder::findAll(['uid'=>$this->user->id,'condition'=>$condition], $page, $pagesize);
		$dataArr = F::newModelToArr($list['data']);

		$dataArr['nextPageNo'] = F::pages($list['count'], $page, $pageSize);
		$dataArr['totalPages'] = $list['count'];

		return F::buildJsonData(0, Consts::msgInfo(), $dataArr);
	}

	public function actionInfo() {
		$dataObj = isset($this->jData)?$this->jData:'';
		$id = isset($dataObj->id)?$dataObj->id:0;
		$ocode = isset($dataObj->ocode)?$dataObj->ocode:'';
		
		if(!$id && !$ocode) 
			return F::buildJsonData(4, Consts::msgInfo(4));

		$order = AmcOrder::findByOrder($id, $ocode);
		if($order->attributes) {
			return F::buildJsonData(0, Consts::msgInfo(), $order->attributes);
		}
		return F::buildJsonData(0, Consts::msgInfo());
	}
}
<?php

namespace app\controllers;

use app\commands\BaseController;
use app\commands\F;
use app\commands\Consts;
use app\models\AmcUser;;
use app\models\AmcProduct;
use app\models\AmcProductShop;

class ShopController extends BaseController{

	public function actionList() {
		$dataObj = isset($this->jData)?$this->jData:'';
		if(empty($dataObj)) {
			return F::buildJsonData(10011, Consts::msgInfo(10011));
		}
		$page = isset($dataObj->page)?$dataObj->page: 1;
		$pageSize = isset($dataObj->pagesize)?$dataObj->pagesize: 10;
		// $condition = empty($dataObj->condition)?[]:$dataObj->condition;

		$list = AmcProductShop::findByAll(['uid'=>$this->user->id], $page, $pagesize);
		$dataArr = F::newModelToArr($list['data']);

		$dataArr['nextPageNo'] = F::pages($list['count'], $page, $pageSize);
		$dataArr['totalPages'] = $list['count'];

		return F::buildJsonData(0, Consts::msgInfo(), $dataArr);
	}

	public function actionRemove() {
		$dataObj = isset($this->jData)?$this->jData:'';
		if(empty($dataObj)) {
			return F::buildJsonData(10011, Consts::msgInfo(10011));
		}
		$id = isset($dataObj->id)?$dataObj->id: 1;

		$shop = AmcProductShop::findOne($id);
		if($shop->uid == $this->user->id) {
			if($shop->delete() > 0) {
				return F::buildJsonData(0, Consts::msgInfo());
			}
		}
		return F::buildJsonData(3, Consts::msgInfo(3));
	}
 

	public function actionAdd() {
		$dataObj = isset($this->jData)?$this->jData:'';
		if(empty($dataObj)) {
			return F::buildJsonData(10011, Consts::msgInfo(10011));
		}
		$pid = isset($dataObj->pid)?$dataObj->pid: 0;
		$number = isset($dataObj->number)?$dataObj->number: 0;

		if(!$pid && !$number) {
			return F::buildJsonData(4, Consts::msgInfo(4));
		}

		if(!$this->user->id) {
			return F::buildJsonData(10022, Consts::msgInfo(10022));
		}

		$productObj = AmcProduct::findById($pid);
		if(!$productObj) {
			return F::buildJsonData(21001, Consts::msgInfo(21001));
		}

		$shop = new AmcProductShop();
		$shop->uid = $this->user->id;
		$shop->pid = $productObj->id;
		$shop->number = $number;
		$shop->pname = $productObj->title;
		$shop->img = $productObj->img;
		if($shop->validate()) {
			if($shop->save()) {
				return F::buildJsonData(0, Consts::msgInfo());
			}
		}
		return F::buildJsonData(3, Consts::msgInfo(3));
	}
}

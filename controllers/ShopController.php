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
		// $dataObj = isset($this->jData)?$this->jData:'';
		// $pid = isset($dataObj->pid)?$dataObj->pid: 0;
		// $number = isset($dataObj->number)?$dataObj->number: 0;

		// AmcProductShop::findByAll();
		return F::buildJsonData(3, Consts::msgInfo(3));
	}
 

	public function actionAdd() {
		$dataObj = isset($this->jData)?$this->jData:'';
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

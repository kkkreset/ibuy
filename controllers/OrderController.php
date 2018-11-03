<?php

namespace app\controllers;

use app\commands\BaseController;
use app\commands\F;
use app\commands\Consts;
use app\models\AmcOrder;
use app\models\AmcUser;
use app\models\AmcProduct;
use app\models\AmcAddress;

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
			$data = F::objectModelToArr($order->attributes);
			return F::buildJsonData(0, Consts::msgInfo(), $data);
		}
		return F::buildJsonData(0, Consts::msgInfo());
	}

	public function actionNew() {
		$dataObj = isset($this->jData)?$this->jData:'';
		$pid = isset($dataObj->pid)?$dataObj->pid:0;
		$addres_id = isset($dataObj->address_id)?$dataObj->address_id:0;
		$payWay = isset($dataObj->pay_way)?$dataObj->pay_way:0;
		if(!$pid || !$addres_id || !$payWay) {
			return F::buildJsonData(10010,Consts::msgInfo(10010));
		}
		$userObj = AmcUser::findByPhone(F::parsingTokenParams($this->token, 'phone'));
		if(!$userObj) {
			return F::buildJsonData(10022, Consts::msgInfo(10022));
		}
		$productObj = AmcProduct::findById($pid);
		if(!$productObj) {
			return F::buildJsonData(21001, Consts::msgInfo(21001));
		}
		$addressObj = AmcAddress::findOne($addres_id);
		if(!$addressObj) {
			return F::buildJsonData(22002, Consts::msgInfo(22002));
		}
		$orderObj = new AmcOrder();
		$orderObj->ocode = 'HD'.$userObj->id.date('YmdHis'); //订单号
		$orderObj->status = 10;// 待付款
		//商品ID
		$orderObj->pid = $pid;
		//用户ID
		$orderObj->uid = $userObj->id;
		if($payWay == 1) {
			$orderObj->pay_hd = $productObj->hd_price;
		}elseif($payWay == 2) {
			$orderObj->pay_price = $productObj->price;
		}
		//address
		$orderObj->address_id = $addressObj->id;
		$orderObj->pname = $addressObj->name; 
		$orderObj->zipcode = $addressObj->zipcode;
		$orderObj->phone = $addressObj->phone;
		$orderObj->province = $addressObj->provinces;
		$orderObj->city = $addressObj->cities;
		$orderObj->county = $addressObj->areas;
		$orderObj->address = $addressObj->address;

		if($orderObj->validate()) {
			if($orderObj->save()) {
				return F::buildJsonData(0, Consts::msgInfo(), ['code'=>$orderObj->ocode]);
			}
		}
		return F::buildJsonData(3, Consts::msgInfo(3));
	}
}
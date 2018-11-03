<?php

namespace app\controllers;

use yii\web\Controller;
use app\commands\F;
use app\commands\Consts;
use app\models\AmcProduct;
use app\models\AmcProductImgs;


class ProductController extends Controller{

	/**
	 * 商品列表 & 搜索 
	 * 
	**/ 
	public function actionList() {
		$json = F::parsingPost();
		$dataObj = isset($json->jData)?$json->jData:'';
		$condition = empty($dataObj->condition)?[]:$dataObj->condition;
		$page = isset($dataObj->page)?$dataObj->page: 1;
		$pageSize = isset($dataObj->pagesize)?$dataObj->pagesize: 10;

		$list = AmcProduct::findAll($condition, $page, $pageSize);
		$dataArr = F::newModelToArr($list['data']);

		$dataArr['nextPageNo'] = F::pages($list['count'], $page, $pageSize);
		$dataArr['totalPages'] = $list['count'];
		return F::buildJsonData(0, Consts::msgInfo(), $dataArr);
	}

	public function actionInfo() {
		$json = F::parsingPost();
		$dataObj = isset($json->jData)?$json->jData:'';
		$id = isset($dataObj->id)?$dataObj->id:0;
		$sku = isset($dataObj->sku)?$dataObj->sku:'';

		if(!$id && !$sku) 
			return F::buildJsonData(4, Consts::msgInfo(4));

		$product = AmcProduct::findByProduct($id, $sku);
		if($product->attributes) {
			$imgsObjArr = AmcProductImgs::findAllByPid($product->id);
			$imgs = [];
			foreach ($imgsObjArr as $imgObj) {
				$imgs[] = $imgObj->img;
			}
			$imgArr['imgs'] = $imgs;
			return F::buildJsonData(0, Consts::msgInfo(), array_merge($product->attributes, $imgArr));
		}
		return F::buildJsonData(0, Consts::msgInfo(), $product->attributes);
	}
}
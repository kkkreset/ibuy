<?php

namespace app\controllers;

use Yii;
use yii\db\Expression;
use app\commands\BaseController;
use app\commands\F;
use app\commands\Consts;
use app\models\AmcProduct;

class GoodsController extends BaseController{

	/**
	 * 商品列表 & 搜索 
	 * 
	**/ 
	public function actionList() {
		$dataObj = isset($this->jData)?$this->jData:'';
		$condition = empty($dataObj->condition)?[]:$dataObj->condition;
		$page = isset($dataObj->page)?$dataObj->page: 1;
		$pageSize = isset($dataObj->pagesize)?$dataObj->pagesize: 10;

		$list = AmcProduct::findAll($condition, $page, $pageSize);
		$dataArr = [];
		foreach ($list['data'] as $i => $r) {
			foreach ($r as $k => $v) {
				$v = isset($v)?$v:'';
				$dataArr['allData'][$i][$k] = $v;
			}
		}

		$dataArr['nextPageNo'] = F::pages($list['count'], $page, $pageSize);
		$dataArr['totalPages'] = $list['count'];
		return F::buildJsonData(0, Consts::msgInfo(), $dataArr);
	}
}
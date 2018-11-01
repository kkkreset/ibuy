<?php

namespace app\controllers;

use Yii;
use yii\db\Expression;
use app\commands\BaseController;
use app\commands\F;
use app\models\AmcProduct;

class GoodsController extends BaseController{

	/**
	 * 商品列表 & 搜索 
	 * 
	**/ 
	public function actionList() {
		return F::buildJsonData(0, Consts::msgInfo(), $dataArr);
	}
}
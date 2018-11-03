<?php

namespace app\controllers;

use Yii;
use yii\db\Expression;
use app\commands\BaseController;
use app\commands\F;
use app\commands\Consts;
use app\models\AmcProduct;
use app\models\AmcProductImgs;


class ProductController extends BaseController
{

    /**
     * 商品列表 & 搜索
     *
     **/
    public function actionList()
    {
        $dataObj = isset($this->jData) ? $this->jData : '';
        $condition = empty($dataObj->condition) ? [] : $dataObj->condition;
        $page = isset($dataObj->page) ? $dataObj->page : 1;
        $pageSize = isset($dataObj->pagesize) ? $dataObj->pagesize : 10;

        $list = AmcProduct::findAll($condition, $page, $pageSize);
        $dataArr = F::newModelToArr($list['data']);

        $dataArr['nextPageNo'] = F::pages($list['count'], $page, $pageSize);
        $dataArr['totalPages'] = $list['count'];
        return F::buildJsonData(0, Consts::msgInfo(), $dataArr);
    }

    public function actionInfo()
    {
        $dataObj = isset($this->jData) ? $this->jData : '';
        $id = isset($dataObj->id) ? $dataObj->id : 0;
        $sku = isset($dataObj->sku) ? $dataObj->sku : '';

        if (!$id && !$sku)
            return F::buildJsonData(1, Consts::msgInfo(4));

        $product = AmcProduct::findByProduct($id, $sku);
        if ($product->attributes) {
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

    /**
     * 保存产品信息
     * @return array
     */
    public function actionSave()
    {
        $userInfo=$this->getUserInfo($this->access_token);

        $dataObj = isset($this->jData) ? $this->jData : '';
        $productId = empty($dataObj->product_id) ? 0 : $dataObj->product_id;
        $model = AmcProduct::find()->where(['id'=>$productId])->one();
        if($model){
            $model->id = $productId;
        }else{
            $model = new AmcProduct();
        }
        //$model = $model?:new AmcProduct();
        $model->title = $dataObj->title;
        $model->title_c = $dataObj->title_c;
        $model->sku = F::build_unique_no();
        $model->level = $dataObj->level;
        $model->level_title = Consts::$mallLeaveTitle[$dataObj->level];
        $model->category_id=0;
        $model->f_shop_id = $userInfo->groupnum;
        $model->shop_id = $userInfo->id;
        $model->price = $dataObj->price;
        $model->r_price = $dataObj->r_price;
        $model->storage = $dataObj->storage;
        $model->hd_price = $dataObj->hd_price;
        $model->hd_handsel = $dataObj->hd_handsel;
        $model->m_price = $dataObj->m_price;
        $model->addtime = time();
        $model->img = $dataObj->img[0];
        $model->desc = $dataObj->desc;
        if(!$model->validate()){
            if($model->errors) {
                return F::buildJsonData(1, $model->errors);
            }
        }
        $model->save();
        return F::buildJsonData(0, Consts::msgInfo());
    }
}


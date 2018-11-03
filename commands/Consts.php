<?php
/**
 * 常用配置类封装
 */
namespace app\commands;

define('GOODS_STATUS_UP', '1');   // 商品上架
define('GOODS_STATUS_DOWN', '2'); // 商品下架

class Consts {

    public static $messageInfo = [
			0=>'SUCCESS',
			3=>'系统处理失败',
			4=>'非法的请求方式',
			5=>'版本已过期',
			41001=>'缺少access_token参数',
			41002=>'access_token超时',
			10010=>'参数格式错误',
			10011=>'参数缺失',
			10012=>'上传图片异常',
	
			10020=>'缺少手机号或密码错误',
			10021=>'用户遗失，请联系管理员',
			10022=>'不存在用户',

			21001=>'商品不存在',
			22002=>'地址信息不存在',

			30001=>'验证码已过期',
			30002=>'验证码匹配不正确',
			30003=>'请求验证码异常'
	];

	public static function msgInfo($name=0) {
        if(!isset($name)) {
            return self::$messageInfo[3];
        }
        return self::$messageInfo[$name];
	}

	public static $goodsStatus = [
		0=>'下架',
		1=>'上架',
		2=>'已租',
		3=>'已售'
	];

	public static $orderStatus = [
		0=>'交易关闭',
		1=>'交易完成',
		2=>'异常订单',
		10=>'待付款',
		11=>'待发货',
		12=>'待签收',
	];

}

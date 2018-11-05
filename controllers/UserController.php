<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use app\commands\BaseController;
use app\commands\F;
use app\commands\Consts;
use app\models\AmcUser;
use app\models\AmcAddress;
use app\models\AmcProvinces;
use app\models\AmcCities;
use app\models\AmcAreas;
use app\models\AmcRecharge;
use app\models\AmcHdTransfer;

class UserController extends BaseController{
	public function init(){
		date_default_timezone_set("PRC");
	}

    public $layout = false;
	
    public function actionError() {
        return $this->render('error');
    }

    public function actionIndex() {
        return $this->render('index');
    }    
    
    /*
     * 验证
     */
    public function baseValidate($json){
    	$token = isset($json->access_token)?$json->access_token:'';
    	if(!$token){
    		return ['code' => 41001, 'msg' =>''];
    	}
    	$userObj = AmcUser::find()->where(['token'=>$token])->one();
    	if(!$userObj){
    		return ['code' => 10022, 'msg' =>''];
    	}
    	return ['code' => 0, 'msg' =>$userObj];
    }
    
    /*
     * 获取用户基本信息
     * 
     */
    public function actionGetinfo(){
    	$postData = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
    	$json = json_decode($postData);
    	$return = self::baseValidate($json);
    	if($return['code'] == 0){
			$levelunm = Consts::$levelNum;
    		$userinfo = $return['msg'];
    		$userinfoArr = $userinfo->toArray();
    		if($userinfoArr['hdlock']<$levelunm[0]){
    			$userinfoArr['userlv'] = Consts::$userLevel[0];
    		}else if($userinfoArr['hdlock']<$levelunm[1]){
    			$userinfoArr['userlv'] = Consts::$userLevel[1];
    		}else{
    			$userinfoArr['userlv'] = Consts::$userLevel[2];
    		}
    		if($userinfoArr['register'] && $userinfoArr['register'] >= strtotime(date("Y-m-d 00:00:00"))){
    			//已签到
    			$userinfoArr['registertype'] = 2;
    		}else{
    			$userinfoArr['registertype'] = 1;
    		}
    		return F::buildJsonData(0, Consts::msgInfo(),$userinfoArr);
    	}else{
    		return F::buildJsonData(1, Consts::msgInfo($return['code']));
    	}
    }
    
    /*
     * 修改用户基本信息
     */
    public function actionReviseinfo(){
    	$postData = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
    	$json = json_decode($postData);
    	$return = self::baseValidate($json);
    	if($return['code'] == 0){
    		$userObj = $return['msg'];
    	}else{
    		return F::buildJsonData(1, Consts::msgInfo($return['code']));
    	}
    	$userObj->name = isset($json->name)?$json->name:'';
    	$userObj->cardnum = isset($json->cardnum)?$json->cardnum:'';
    	$userObj->avatar = isset($json->avatar)?$json->avatar:'';
    	if($userObj->validate()){
            if($userObj->errors) {
                return F::buildJsonData(1,$userObj->errors);
        }
            $userObj->save();
        }
    	return F::buildJsonData(0, Consts::msgInfo());
    }
    
    /*
     * 修改密码
     */
    public function actionRevisepsw(){
    	$postData = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
    	$json = json_decode($postData);
    	$return = self::baseValidate($json);
    	if($return['code'] == 0){
    		$userObj = $return['msg'];
    	}else{
    		return F::buildJsonData(1, Consts::msgInfo($return['code']));
    	}
    	$newpsw = $json->password;
    	if(!$newpsw){
    		return F::buildJsonData(1, Consts::msgInfo(10011));
    	}
    	$oldpsw1 = $userObj->password;
    	$oldpsw2 = $json->oldpsw;
    	if($oldpsw1 != md5(md5($oldpsw2))){
    		return F::buildJsonData(1, Consts::msgInfo(10023));
    	}
    	$userObj->password = md5(md5($newpsw));
    	if($userObj->validate()){
            if($userObj->errors) {
                return F::buildJsonData(1,$userObj->errors);
            }
            $userObj->save();
        }
    	return F::buildJsonData(0, Consts::msgInfo());
    }
    
    /*
     * 签到
     */
    public function actionRegister(){
    	$postData = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
    	$json = json_decode($postData);
    	$return = self::baseValidate($json);
    	if($return['code'] == 0){
    		$userObj = $return['msg'];
    	}else{
    		return F::buildJsonData(1, Consts::msgInfo($return['code']));
    	}
    	if($userObj->register && $userObj->register >= strtotime(date("Y-m-d 00:00:00"))){
    		return F::buildJsonData(1, Consts::msgInfo(10025));
    	}else{
    		$userObj->register = time();
    		$userObj->hdlock = $userObj->hdlock + 0.5;
    		$userObj->save();

    		$hdcharge = new AmcHdTransfer();
    		$hdcharge->code =  date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 12);
    		$hdcharge->receiverid = $userObj->id;
    		$hdcharge->receivertel = $userObj->phone;
    		$hdcharge->premoney = 0.5;
    		$hdcharge->money = 0.5;
    		$hdcharge->state = 1;
    		$hdcharge->title = "签到";
    		$hdcharge->accounttype = 6;
    		$hdcharge->addtime = date("Y-m-d H:i:s");
    		$hdcharge->save();
    		return F::buildJsonData(0, Consts::msgInfo());
    	}
    }
    
    /*
     * 充值凭证上传
     */
    public function actionRecharge(){
    	$postData = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
    	$json = json_decode($postData);
    	$return = self::baseValidate($json);
    	if($return['code'] == 0){
    		$userObj = $return['msg'];
    	}else{
    		return F::buildJsonData(1, Consts::msgInfo($return['code']));
    	}
    	$name = isset($json->name)?$json->name:'';
    	$money = isset($json->money)?$json->money:'';
    	$remark = isset($json->remark)?$json->remark:'';
    	$image = isset($json->image)?$json->image:'';
    	if(!$name || !$money || !$remark || !$image){
    		return F::buildJsonData(1, Consts::msgInfo(10011));
    	}
    	$recharge = new AmcRecharge();
    	$recharge->rmobile = $userObj->phone;
    	$recharge->userid = $userObj->id;
    	$recharge->rtype = 1;
    	$recharge->rtitle = $remark;
    	$recharge->rimage = $image;
    	$recharge->rstate = 0;
    	$recharge->rmoney = $money;
    	$recharge->rpremoney = $money;
    	$recharge->rbankname = $name;
    	$recharge->raddtime = date("Y-m-d H:i:s");
    	$recharge->rechcode = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 12);
    	$recharge->save();
        return F::buildJsonData(0, Consts::msgInfo());
    }
    
    /*
     *退出登录
     */
    public function actionLoginout(){
    	$postData = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
        $json = json_decode($postData);
        $return = self::baseValidate($json);
    	if($return['code'] == 0){
    		$userObj = $return['msg'];
    	}else{
    		return F::buildJsonData(1, Consts::msgInfo($return['code']));
    	}
    	$userObj->token = '';
    	$userObj->save();
    	return F::buildJsonData(0, Consts::msgInfo());
    }
    
    /*
     * 新增收货地址 
     */
    public function actionAddaddr(){
    	$postData = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
        $json = json_decode($postData);
        $return = self::baseValidate($json);
    	if($return['code'] == 0){
    		$userObj = $return['msg'];
    	}else{
    		return F::buildJsonData(1, Consts::msgInfo($return['code']));
    	}
        $phone = isset($json->phone)?$json->phone:'';
        $address = isset($json->address)?$json->address:'';
        $zipcode = isset($json->zipcode)?$json->zipcode:'000000';
        $name = isset($json->name)?$json->name:'';
        $provinces = isset($json->provinces)?$json->provinces:'';
        $cities = isset($json->cities)?$json->cities:'';
        $areas = isset($json->areas)?$json->areas:'';
		if(!$phone || !$address || !$name || !$provinces || !$cities)
            return F::buildJsonData(1, Consts::msgInfo(10011));
        $addr = new AmcAddress();
        $addr->phone = $phone;
        $addr->address = $address;
        $addr->zipcode = $zipcode;
        $addr->name = $name;
        $addr->provinces = $provinces;
        $addr->cities = $cities;
        $addr->areas = $areas;
//      $addr->createtime = (string)time();
        $addr->uid = $userObj->id;
        if($addr->validate()){
            if($addr->errors) {
                return F::buildJsonData(1,$addr->errors);
        	}
        	$addr->id = $addr->save();
        }
        return F::buildJsonData(0, Consts::msgInfo(),$addr->toArray());
    }
    
    /*
     * 设为默认地址
     */
    public function actionSetdeaddr(){
    	$postData = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
    	$json = json_decode($postData);
    	$return = self::baseValidate($json);
    	if($return['code'] == 0){
    		$userObj = $return['msg'];
    	}else{
    		return F::buildJsonData(1, Consts::msgInfo($return['code']));
    	}
    	$id = isset($json->id)?$json->id:'';
    	if(!$id)
            return F::buildJsonData(1, Consts::msgInfo(10011));
        $oldDefault = AmcAddress::find()->where(['isdefault'=>2,'uid'=>$userObj->id])->one();
        if($oldDefault){
        	$oldDefault->isdefault = 1;
        	if($oldDefault->validate()){
                if($oldDefault->errors) {
                    return F::buildJsonData(1,$oldDefault->errors);
                }
            	$oldDefault->save();
            }
        }
        $newDefault = AmcAddress::find()->where(['id'=>$id])->one();
        $newDefault->isdefault = 2;
		if($newDefault->validate()){
            if($newDefault->errors) {
                return F::buildJsonData(1,$newDefault->errors);
            }
            $newDefault->save();
    }   	
		return F::buildJsonData(0, Consts::msgInfo());    	
    }
    
    /*
     * 获取收货信息
     * type 1 单个  2 所有 3默认
     * 
     */
    public function actionGetaddr(){
    	$postData = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
    	$json = json_decode($postData);
    	$return = self::baseValidate($json);
    	if($return['code'] == 0){
    		$userObj = $return['msg'];
    	}else{
    		return F::buildJsonData(1, Consts::msgInfo($return['code']));
    	}
    	$type = isset($json->type)?$json->type:'1';
    	if($type == 1){
    		$id = isset($json->id)?$json->id:'';
    		if(!$id)
            	return F::buildJsonData(1, Consts::msgInfo(10011));           
            $addr = AmcAddress::find()->where(['id'=>$id])->one();
            if(!$addr){
            	return F::buildJsonData(0, Consts::msgInfo(),[]);	
            }else{ 
            	$pname = AmcProvinces::find()->where(['provinceid'=>$addr->provinces])->one();
            	$cname = AmcCities::find()->where(['cityid'=>$addr->cities])->one();
            	$aname = AmcAreas::find()->where(['areaid'=>$addr->areas])->one();
            	$addr = $addr->toArray();
            	$pname = $pname->toArray(); 
            	$cname = $cname->toArray();
            	$aname = $aname->toArray();
            	$addr['pname'] = $pname['province'];
            	$addr['cname'] = $cname['city'];
            	$addr['aname'] = $aname['area'];            	
            	return F::buildJsonData(0, Consts::msgInfo(),$addr);	
            }
    	}else if($type == 2){
    		$sql1 = "select b.province province from amc_address a LEFT JOIN  amc_provinces b on a.provinces = b.provinceid where uid = {$userObj->id} order by a.id desc";
    		$sql2 = "select b.city city from amc_address a LEFT JOIN  amc_cities b on a.cities = b.cityid where uid = {$userObj->id} order by a.id desc";
    		$sql3 = "select a.id id,a.address address,a.phone phone,a.uid uid,a.isdefault isdefault,a.zipcode zipcode,a.provinces provinces,a.cities cities,a.areas areas,a.name name,b.area aname from amc_address a LEFT JOIN  amc_areas b on a.areas = b.areaid where uid = {$userObj->id} order by a.id desc";
			$pname = Yii::$app->db->createCommand($sql1)->queryAll();
			$cname = Yii::$app->db->createCommand($sql2)->queryAll();
			$aname = Yii::$app->db->createCommand($sql3)->queryAll();
			foreach($aname as $key => $val){
				$aname[$key]['pname'] = $pname[$key]['province'];
				$aname[$key]['cname'] = $cname[$key]['city'];
			}    		   			
    		return F::buildJsonData(0, Consts::msgInfo(),$aname);
    	}else if($type == 3){
    		$addr = AmcAddress::find()->where(['isdefault'=>2,'uid'=>$userObj->id])->one();
    		if(!$addr){
    			return F::buildJsonData(0, Consts::msgInfo(),[]);
    		}else{
    			$pname = AmcProvinces::find()->where(['provinceid'=>$addr->provinces])->one();
            	$cname = AmcCities::find()->where(['cityid'=>$addr->cities])->one();
            	$aname = AmcAreas::find()->where(['areaid'=>$addr->areas])->one();
            	$addr = $addr->toArray();
            	$pname = $pname->toArray(); 
            	$cname = $cname->toArray();
            	$aname = $aname->toArray();
            	$addr['pname'] = $pname['province'];
            	$addr['cname'] = $cname['city'];
            	$addr['aname'] = $aname['area'];     
    			return F::buildJsonData(0, Consts::msgInfo(),$addr);
    		}
    	}else{
    		return F::buildJsonData(1, Consts::msgInfo(10010));
    	}
    }
    
    
    /*
     * 修改收货地址
     */
    public function actionReviseaddr(){
    	$postData = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
    	$json = json_decode($postData);
//  	$return = self::baseValidate($json);
//  	if($return['code'] == 0){
//  		$userObj = $return['msg'];
//  	}else{
//  		return F::buildJsonData(1, Consts::msgInfo($return['code']));
//  	}
    	$id = isset($json->id)?$json->id:'';
    	$phone = isset($json->phone)?$json->phone:'';
    	$address = isset($json->address)?$json->address:'';
    	$zipcode = isset($json->zipcode)?$json->zipcode:'000000';
    	$name = isset($json->name)?$json->name:'';
        $provinces = isset($json->provinces)?$json->provinces:'';
        $cities = isset($json->cities)?$json->cities:'';
        $areas = isset($json->areas)?$json->areas:'';
    	if(!$id || !$phone || !$address || !$name || !$provinces || !$cities)
            return F::buildJsonData(1, Consts::msgInfo(10011));            
        $addr = AmcAddress::find()->where(['id'=>$id])->one();      
		$addr->phone = $phone;
		$addr->address = $address;
		$addr->zipcode = $zipcode;
		$addr->name = $name;
        $addr->provinces = $provinces;
        $addr->cities = $cities;
        $addr->areas = $areas;
		if($addr->validate()){
            if($addr->errors) {
                return F::buildJsonData(1,$addr->errors);
            }
            $addr->save();
        }
		return F::buildJsonData(0, Consts::msgInfo()); 
    }
    
    /*
     * 删除收货地址
     */
    public function actionDeladdr(){
    	$postData = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
    	$json = json_decode($postData);
    	$return = self::baseValidate($json);
    	if($return['code'] == 0){
    		$userObj = $return['msg'];
    	}else{
    		return F::buildJsonData(1, Consts::msgInfo($return['code']));
    	}
    	$id = isset($json->id)?$json->id:'';
    	if(!$id)
            return F::buildJsonData(1, Consts::msgInfo(10011)); 
        $addr = AmcAddress::find()->where(['id'=>$id,'uid'=>$userObj->id])->one();
        if(!$addr)
        	return F::buildJsonData(1, Consts::msgInfo(10024)); 
        AmcAddress::find()->where(['id'=>$id,'uid'=>$userObj->id])->one()->delete();
		return F::buildJsonData(0, Consts::msgInfo());         
    }
    
    /*
     * 获取省市区
     * type 1 省  2 市 3 区
     */
    public function actionGetarea(){
    	$postData = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
    	$json = json_decode($postData);
    	$type = isset($json->type)?$json->type:'';
    	if(!$type)
    		return F::buildJsonData(1, Consts::msgInfo(10011)); 
    	if($type == 1){
    		$provinces = AmcProvinces::find()->all();
    		return F::buildJsonData(0, Consts::msgInfo(),ArrayHelper::toArray($provinces));
    	}else{
    		$id = isset($json->id)?$json->id:'';
    		if(!$id)
    			return F::buildJsonData(1, Consts::msgInfo(10011)); 
    		if($type == 2){
    			$cities = AmcCities::find()->where(['provinceid'=>$id])->all();
    			return F::buildJsonData(0, Consts::msgInfo(),ArrayHelper::toArray($cities));
    		}else if($type == 3){
    			$areas = AmcAreas::find()->where(['cityid'=>$id])->all();
    			return F::buildJsonData(0, Consts::msgInfo(),ArrayHelper::toArray($areas));
    		}
    	}
    	
    }

}

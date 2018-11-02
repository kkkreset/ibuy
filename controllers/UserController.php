<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\commands\F;
use app\commands\Consts;
use app\models\AmcUser;
use app\models\AmcAddress;

class UserController extends Controller{

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
    	$token = isset($json->data->access_token)?$json->data->access_token:'';
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
    		return F::buildJsonData(0, Consts::msgInfo(),$return['msg']->toArray());
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
    	$userObj->name = isset($json->data->name)?$json->data->name:'';
    	$userObj->cardnum = isset($json->data->cardnum)?$json->data->cardnum:'';
    	$userObj->avatar = isset($json->data->avatar)?$json->data->avatar:'';
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
    	$newpsw = $json->data->password;
    	if(!$newpsw){
    		return F::buildJsonData(1, Consts::msgInfo(10011));
    	}
    	$oldpsw1 = $userObj->password;
    	$oldpsw2 = $json->data->oldpsw;
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
        $phone = isset($json->data->phone)?$json->data->phone:'';
        $address = isset($json->data->address)?$json->data->address:'';
        $zipcode = isset($json->data->zipcode)?$json->data->zipcode:'';
		if(!$phone || !$address || !$zipcode)
            return F::buildJsonData(1, Consts::msgInfo(10011));
        $addr = new AmcAddress();
        $addr->phone = $phone;
        $addr->address = $address;
        $addr->zipcode = $zipcode;
        $addr->createtime = time();
        $addr->uid = $userObj->id;
        if($addr->validate()){
            if($addr->errors) {
                return F::buildJsonData(1,$addr->errors);
        	}
        	$addr->save();
        }
        return F::buildJsonData(0, Consts::msgInfo());
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
    	$id = isset($json->data->id)?$json->data->id:'';
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
     * type 1 单个  2 所有
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
    	$type = isset($json->data->type)?$json->data->type:'1';
    	if($type == 1){
    		$id = isset($json->data->id)?$json->data->id:'';
    		if(!$id)
            	return F::buildJsonData(1, Consts::msgInfo(10011));
           	$addr = AmcAddress::find()->where(['id'=>$id])->one();
           	F::buildJsonData(0, Consts::msgInfo(),$addr);
    	}else if($type == 2){
    		$addrs = AmcAddress::find()->where(['uid'=>$userObj->id])->select();
    		F::buildJsonData(0, Consts::msgInfo(),$addrs);
    	}else{
    		F::buildJsonData(1, Consts::msgInfo(10010));
    	}
    }
    
    
    /*
     * 修改收货地址
     */
    public function actionReviseaddr(){
    	$postData = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
    	$json = json_decode($postData);
    	$return = self::baseValidate($json);
    	if($return['code'] == 0){
    		$userObj = $return['msg'];
    	}else{
    		return F::buildJsonData(1, Consts::msgInfo($return['code']));
    	}
    	$id = isset($json->data->id)?$json->data->id:'';
    	$phone = isset($json->data->phone)?$json->data->phone:'';
    	$address = isset($json->data->address)?$json->data->address:'';
    	$zipcode = isset($json->data->zipcode)?$json->data->zipcode:'';
    	if(!$id || !$phone || !$address || !$zipcode )
            return F::buildJsonData(1, Consts::msgInfo(10011));            
        $addr = AmcAddress::find()->where(['id'=>$id])->one();        
		$addr->$phone = $phone;
		$addr->$address = $address;
		$addr->$zipcode = $zipcode;
		if($addr->validate()){
            if($addr->errors) {
                return F::buildJsonData(1,$addr->errors);
            }
            $addr->save();
        }
		return F::buildJsonData(0, Consts::msgInfo()); 
    }

}

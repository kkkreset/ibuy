<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\commands\F;
use app\commands\Consts;
use app\models\AmcUser;

class SiteController extends Controller{

    public $layout = false;

    public function actionError() {
        return $this->render('error');
    }

    public function actionIndex() {
        return $this->render('index');
    }

    /**
     * 前台用户手机号 & 验证码登录
     *
     **/
    public function actionLogin() {
        $postData = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
        $json = json_decode($postData);
        $phone = isset($json->data->phone)?$json->data->phone:'';
        $code = isset($json->data->code)?$json->data->code:'';
        $invite = isset($json->data->invite)?$json->data->invite:'';
        if(!$phone || !$code)
            return F::buildJsonData(1, Consts::msgInfo(10020));
        if($code == '1234') {
            $userObj = AmcUser::find()->where(['phone'=>$phone])->one();
            $token = F::setToken('phone', $phone, 3600 * 24 * 7);// Token时效设置
            if(!$userObj) {
                $userObj = new AmcUser();
                $userObj->phone = $phone;
                $userObj->password = md5(md5('123456'));
                $userObj->status = 1;
                $userObj->referralnum = $invite;
                // $userObj->avatar = 'user/default-'.rand(1,5).'.png'; //设置默认头像
                $userObj->avatar = '';
                $userObj->token = ''.$token;
                if($userObj->validate()){
                    if($userObj->errors) {
                        return F::buildJsonData(1,$userObj->errors);
                    }
                    $userObj->save();
                }
            }
            return F::buildJsonData(0, Consts::msgInfo(), ['access_token'=>''.$token]);
        }
        return F::buildJsonData(1, Consts::msgInfo(30001));
    }

    /**
     * Upyun图片上传
     *
     **/
    public function actionUpmoreimage() {
        // $path = isset($_POST['path'])?$_POST['path']:'goods';
        // $files =  isset($_FILES['files'])?$_FILES['files']:array();
        // if(!$path && empty($files)) {
        //     return F::buildJsonData(1, Consts::msgInfo(101));
        // }
        // $newImgArr = [];
        // for($i=0; $i < count($files['name']); $i++) { 
        //     $fileInfo = pathinfo($files['name'][$i]);
        //     $bucketConfig = new Config(XY_UPYUN_SERVICE_NAME, XY_UPYUN_USERNAME, XY_UPYUN_PASSWORD);
        //     $client = new Upyun($bucketConfig);
        //     $pathBase = '/uploads/';
        //     $res = $client->createDir($pathBase.$path);
        //     // 读文件
        //     $file = fopen($files['tmp_name'][$i], 'r');      
        //     // 上传文件
        //     $newImg = $path.'/'.date('Ymd').'/'.md5(time()).'.'.$fileInfo['extension'];
        //     $res = $client->write($pathBase.$newImg, $file);
        //     if($res['x-upyun-frames'] == 1) {
        //         $newImgArr[] = $newImg;
        //     }
        //     sleep(2);
        // }
        // if($newImgArr) {
        //     return F::buildJsonData(0, Consts::msgInfo(0),['images_url' => $newImgArr]);
        // }
        return F::buildJsonData(1, Consts::msgInfo(3));   
        
    }
}

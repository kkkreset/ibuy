<?php
/**
 * 常用函数类封装
 */
namespace app\commands;


use Yii;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signature;

use yii\web\Session;

class F {
    
    /**
     * 解析请求的Post数据流
     * @author Roble
     * @param string @format
     */
    public static function parsingPost() {
        $postData = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
        return json_decode($postData);// 解析为json对象
    }

    /**
     * 获取用户IP
     */
    public static function fetch_alt_ip() {
        $alt_ip = $_SERVER['REMOTE_ADDR'];
        if(isset($_SERVER['HTTP_CLIENT_IP'])) {
            $alt_ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) and preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            // make sure we dont pick up an internal IP defined by RFC1918
            foreach ($matches[0] as $ip) {
                if(!preg_match("#^(10|172\.16|192\.168)\.#", $ip)) {
                    $alt_ip = $ip;
                    break;
                }
            }
        } else if(isset($_SERVER['HTTP_FROM'])) {
            $alt_ip = $_SERVER['HTTP_FROM'];
        }
        return $alt_ip;
    }
    
    /**
     * 生成随机字符串
     * @param int $len
     * @param string @format
     */
    public static function randStr($len=6, $format='ALL') {
        switch($format) {
            case 'ALL':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~'; break;
            case 'CHAR':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~'; break;
            case 'NUMBER':
                $chars='0123456789'; break;
            case 'CHARNUM':
                $chars='abcdefghijklmnopqrstuvwxyz0123456789'; break;
            default :
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
                break;
        }
        mt_srand((double)microtime()*1000000*getmypid());
        $password = "";
        while(strlen($password) < $len) {
            $password .= substr($chars,(mt_rand()%strlen($chars)),1);
        }
        return $password;
    }
    
    /**
     * 
     * Phone 正确格式验证
     * @param String $phone
     */
    public static function is_phone($phone) {
        if(preg_match("/^1(3|4|5|7|8)\d{9}$/", $phone)) {
            return true;
        }
        return false;
    }
    
    /**
     * email 正确格式验证
     * @param String $email
     */
    public static function is_email($email) {
        if(preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$/", $email)) {
            return true;
        }
        return false;
    }

    public static function is_ucode($ucode) {
        if(preg_match("/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X)$/", $ucode)) {
            return true;
        }
        return false;
    }

    public static function is_adult($ucode) {
        if(!self::is_ucode($ucode)) {
            return false;
        }
        $year = substr($ucode, 6, 4);
        if((date('Y') - $year) >= 18) {
            return true;
        }
        return false;
    }
    
    /**
     * 为字符串添加''
     * @param int $int
     */
    public static function q($string) {
        if($string == "" and $string != 0) {
            return 'NULL';
        } else {
            return "'" . addslashes($string) . "'";
        }
    }
    
    /**
     * 对象转换为数组
     * @param Object $obj
     * @author Roble
     * @create 2014-8-7
     */
    public static function getObjdecArr($obj){
        $arr = array();
        if(!$obj) {
            return $arr;
        }
        foreach($obj as $row) {
            array_push($arr, $row->attributes);
        }
        return $arr;
    }

    /**
     * @param String $expression
     * @param String $true
     * @param String $false
     */
    public static function iif($expression, $true, $false = '') {
        if($expression == 0) {
            return $false;
        } else {
            return $true;
        }
    }

    /**
     * 返回指定字符首次出现位置
     * @param String $str
     * @param String $findStr
     */
    public static function findCharPosition($str, $findStr) {
        return strpos(strip_tags($str), $findStr, 0);
    }

    /**
     * 根据指定字符串截取
     * @param String $str
     * @param String $char
     * @param String $size
     */

    public static function substrToCharPosition($str, $char="。", $size=3) {
        return  substr(strip_tags($str), 0, F::findCharPosition(strip_tags($str), $char) + $size);
    }

    /**
     * 上传文件到本地
     * @param string  $fileTmp  上传的文件信息
     * @param string  $dirName  路径
     * @param string  $size 限制上传大小
     * @author Roble
     * @create 2016-3-8
     */
    public static function upImage($fileTmp, $path, $dirName='uploads/image', $size=2097152) {
        /*获得上传的后缀(提供的文件后缀可能与检测的不一致,但是要保持上传的后缀一致)*/
        $filesinfo = pathinfo($fileTmp['name']);
        $suffix = $filesinfo['extension']; //后缀
        $dirName = WEBPATH.'/'.$dirName;// 保存路径
        if(!file_exists($dirName)) {  //判断文件是否存在或者是否是文件目录
            @mkdir($dirName, 0777);    //不存在则创建目录
            if(!file_exists($dirName)){
                return ['error'=>self::strError('上传目录创建失败!')];
            }
        }
        //上传文件信息异常判断
        if($fileTmp['error'] == 1) {
            return ['err'=>$fileTmp['error']];
        }
        if($fileTmp['size'] > $size) {
            return ['err'=>'上传文件大于'.($size / 1024).'!'];
        }
        $fileSuf = self::filetype($suffix);
        if($fileSuf >= 1 && $fileSuf <= 9) { //判断文件类型
            //判断目录是否存在
            $pathfile = $path.'.'.$suffix;
            if(move_uploaded_file($fileTmp['tmp_name'], $dirName.'/'.$pathfile)) {
                return ['url'=> $pathfile];
            }else{
                return ['err'=> $fileTmp['tmp_name'].'|'.$dirName];
            }
        }
        return ['err'=>'文件格式不正确,当前接口仅支持图片格式.'];
    }

    public static function strError($str){
       return $str;
    }


    /**
     * 上传文件到本地
     * @param string  $fileTmp  上传的文件信息
     * @param string  $dirName  路径
     * @param string  $size 限制上传大小
     * @author Roble
     * @create 2016-3-8
     */
    public static function uploadFiles($fileTmp, $filename, $dirName='uploads/image', $isdir=false, $size=90112) {
        /*获得上传的后缀(提供的文件后缀可能与检测的不一致,但是要保持上传的后缀一致)*/
        $filesinfo = pathinfo($fileTmp->name);
        $suffix = $filesinfo['extension'];
        if($isdir) {// 多一层格式
            $folder = '/'.date('ymd');
            $dirName .= $folder; 
        }
        
        //判断文件是否存在或者是否是文件目录
        if(!file_exists($dirName)) {
            mkdir($dirName, 0777);//不存在则创建目录
            if(!file_exists($dirName)){
                return ['error'=>self::strError('上传目录创建失败!')];
            }
        }
        //上传文件信息异常判断
        if($fileTmp->error == 1) {
            return ['error'=>self::strError('上传文件大小超过服务器的设置,请联系管理员!')];
        }
        if($fileTmp != '') {
            $fileSuf = self::filetype($suffix);
            //判断文件类型
            if($fileSuf <= 3) {
                if($fileTmp->size > 5 * 1024 * 1024) {
                    return ['error'=>self::strError('图像是超过5MB.请上传一个较小的图像!')];
                }
                $dirUrl = $filename.'.'.$suffix;
            }else if($fileSuf >4  && $fileSuf <= 16) {
                if($fileTmp->size > 10 * 1024 * 1024) {
                    return ['error'=>self::strError('文件是超过10MB.请上传小于10MB的文件!')];
                }
                $dirUrl = $filename.'.'.$suffix;
            }else{
                return ['error'=>self::strError('无效的文件格式上传的尝试.')];
            }
            //上传完整路径
            $uploadDir = $dirName.'/'.$dirUrl;
            //判断目录是否存在
            if(move_uploaded_file($fileTmp->tempName, $uploadDir)) {
                return ['url'=>$folder.'/'.$dirUrl];
            }else{
                return ['error'=>self::strError($fileTmp->tempName.'|'.$uploadDir)];
            }
        }else{
            return ['error'=>self::strError('没有这样的文件或目录.')];
        }
    }

   /**
      * 上传文件规则
      *
      * @author Roble
      * @param int     $types
      * @return string
      */
     public static function filetype($types) {
        $file_types = [
            1 => 'gif', 2 => 'jpg', 3 => 'png', 4 =>'jpeg',
            10 => 'swf', 11 => 'dcr', 12 => 'rar', 13 => 'zip',
            20 => 'unity3d', 21 => 'spx', 22 =>'swc',
        ];
        return array_search($types, $file_types);
     }

    public static function isLogin($user) {
        if(!$user) {
             throw new \yii\web\HttpException(500, 'equest is not legal.');exit;
        }
        return true;
    }

    /**
     * 删除本地文件
     * @param $file
     * @author Roble
     * @create 2015-2-10
     */
    public static function delLocaFile($file='') {
        if(file_exists($file)) {
            if(unlink($file)) {
                return true;
            }
        }
        return false;
    }
    /**
     * 判断浏览器
     * @param $file
     * @author Roble
     * @create 2015-2-10
     */
    public static function getBrowse() {
        $agent=$_SERVER["HTTP_USER_AGENT"];
        if(strpos($agent,'MSIE')!==false || strpos($agent,'rv:11.0')) //ie11判断
        return "ie";
        else if(strpos($agent,'Firefox')!==false)
        return "firefox";
        else if(strpos($agent,'Chrome')!==false)
        return "chrome";
        else if(strpos($agent,'Opera')!==false)
        return 'opera';
        else if((strpos($agent,'Chrome')==false)&&strpos($agent,'Safari')!==false)
        return 'safari';
        else 
        return 'unknown';
    }

    public static function setToken($key, $val, $expiration=3600) {
        $token = (new Builder())->setIssuer(JWT_ISSUER) // 签发者
                        ->setAudience(JWT_AUDIENCE) // 接收jwt的一方
                        ->setIssuedAt(time()) // 生成时间
                        ->setExpiration(time() + $expiration) // 过期时间
                        ->set($key, $val)
                        ->getToken(); // 检索生成的令牌
        return $token; // 返回令牌
    }

    public static function parsingToken($tokenString) {
        $token = (new Parser())->parse((string)$tokenString);
        
        if(self::ValidationJWT($token)) {
            return $token;
        }
        return false;
    }

    public static function ValidationJWT($tokenObj) {
        $data = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
        $data->setIssuer(JWT_ISSUER);
        $data->setAudience(JWT_AUDIENCE);

        // $data->setCurrentTime(time() + 4000); //使Token过期
 
        return $tokenObj->validate($data);
    }

    public static function buildJsonData($code, $msg, $paramsdata=array()) {
        if(count($paramsdata) <= 0) {
            $paramsdata = '{}';
        }
        $data = ['code'=>$code,'msg'=>$msg, 'data'=>$paramsdata];
        return json_encode($data);
    }

    public static function pages($cnt, $page, $pagesize) {
        if($page * $pagesize < $cnt) {
            return $page + 1;
        }else {
            return 0;
        }
        // if((($page + 1) * $pageSize) - $list['count']->cnt > 0) {
        //     $dataArr['nextPageNo'] = $page + 1; 
        // }else{
        //     $dataArr['nextPageNo'] = 0;
        // }
    }

    public static function postCurl($url, $postData) {
        if(empty($postData)) {
            return false;
        }
        $ch = curl_init();// 创建实例
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_HEADER, 0 );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData );
        $return = curl_exec($ch);
        curl_close ($ch);
        return $return;
    }

    public static function getCurl($url) {
        if(isset($url) && $url != '')  {
            $ch = curl_init();// 创建实例
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $output = curl_exec($ch);
            curl_close($ch);
            return $output;
        }
    }

    public static function setCookie($key, $val, $exptime=300) {
        $cookies = Yii::$app->response->cookies;
         
        $cookies->add(new \yii\web\Cookie([
            'name' => $key,
            'value' => $val,
            'expire'=>time() + $exptime,
            // 'httpOnly' => true
        ]));
    }

    public static function getCookie($key) {
        $cookies = Yii::$app->request->cookies;//注意此处是request
        return $cookies->get($key);//设置默认值
    }

    public static function setSession($key, $name) {
        $session = Yii::$app->session;
        $session->set($key, $name);
    }

    public static function getSession($key) {
        $session = Yii::$app->session;
        return $session->get($key);
    }

    /*
    但是商品的编码应该是Z0013201706290001
    固定为17位数，Z代表重奢，0013代表品牌编码，20170629是入库时间，0001是今天第一个入库的顺利码
    */
    public static function newGoodsNumber($gearid, $brandid, $number) {
        $gear = self::gearListNumber($gearid);
        $brand = self::bulidZero($brandid, 3);
        $newNumber = self::bulidZero($number, 4, true);
        return $gear.$brand.date('Ymd').$newNumber;
    }

    public static function gearListNumber($gearid) {
        $arr = [
            1=>'C',
            2=>'B',
            3=>'A',
        ];
        return $arr[$gearid];
    }

    /**
     * 生成Number,前置位0
     *
     **/
    public static function bulidZero($number, $count=4, $is_increase=false) {
        $intNumber = (int)$number;
        $increase = 0;
        if($is_increase) {
            $increase = 1;
        }
        if($intNumber >= 9999) {
            return false;
        }
        $len = strlen($intNumber);
        $zeroStr = '';
        if(strlen(($intNumber + $increase)) < $count) {
            for($i=0; $i < ($count - (int)$len); $i++) { 
                $zeroStr .= '0';
            }
            $newStr = $zeroStr.($intNumber + $increase);
            if(strlen($newStr) > $count) {
                return substr($newStr, 1, strlen($newStr));
            }
            return $newStr;
        }
        
        return ($intNumber + $increase);    
    }

    public static function modelToArr($modelArr) {        
        $dataArr['allData'] = [];
        foreach($modelArr as $index=>$r) {
            foreach($r as $key=>$value) {
                $dataArr['allData'][$index][$key] = $value;
            }
        }
        return $dataArr;
    }

    public static function newModelToArr($modelArr) {        
        $dataArr['allData'] = [];
        foreach($modelArr as $index=>$r) {
            foreach($r as $key=>$val) {
                switch(gettype($val)) {
                    case 'string':
                        $newVal = isset($val)?$val:'';
                        break;
                    case 'integer':
                        $newVal = isset($val)?$val:0;
                        break;
                    case 'double':
                        $newVal = isset($val)?$val:0.00;
                        break;
                    default:
                        $newVal = '';
                        break;
                }
                $dataArr['allData'][$index][$key] = $newVal;
            }
        }
        return $dataArr;
    }

    public static function parsingTokenParams($token, $key) {
        if(!$token) 
            return false;
        if($token->getClaim($key)) {
            return $token->getClaim($key);
        }
        return '';
    }

    /**
     * 将传入的参数组织成key1=value1&key2=value2形式的字符串
     * @param $params
     * @return string
     */
    public static function buildQuery($params, $needEncode){
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === self::checkEmpty($v)) {
                if($needEncode){
                    $v = urlencode($v);
                }

                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }

    /**
     *  校验$value是否非空
     *  if not set ,return true;
     *  if is null , return true;
     * @param $value
     * @return bool
     */
    public static function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }

   

    public static function flowCode($id) {
        return date('YmdHis').F::randStr(4, 'NUMBER').$id;
    }


    public static function imageBinary($image) {
        if(!file_exists($image)) {
            return false;
        }
        $fp = fopen($image, 'rb');
        return fread($fp, filesize($image));
    }

    public static function assert($val, $msg) {
        if(!$val) 
            return F::buildJsonData(1, Consts::msgInfo($msg), $dataArr);
    }

    
}
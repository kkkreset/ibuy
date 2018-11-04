<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
namespace app\commands;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

use app\commands\F;
use app\commands\Consts;
use app\models\AmcUser;

class BaseController extends Controller {

    public  $json;
    public  $jData;
    private $access_token;
    public  $token;
    public  $user;
    public  $layout = false;

    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public function beforeAction($action) {
        $postData =  isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
        if(!$postData) {
            return F::buildJsonData(1, Consts::msgInfo(3));exit;
        }
        $this->json = json_decode($postData);
        if($action->id != 'signin') {
            $this->access_token = $this->json->access_token;
            $this->jData = isset($this->json->data)?$this->json->data:null;
            
            if(!$this->access_token) {
                return F::buildJsonData(41001, Consts::msgInfo(41001));exit;
            }
            $this->token = F::parsingToken($this->access_token);
            if(!$this->token) {
                return F::buildJsonData(41002, Consts::msgInfo(41002));exit;
            }
            $phone = $this->token->getClaim('phone');
            if(!$phone || $phone == ''){
                return F::buildJsonData(10022, Consts::msgInfo(10022));exit;
            }
            $this->user = AmcUser::findByPhone($phone);
            if(!$this->user) {
                return F::buildJsonData(10022, Consts::msgInfo(10022));exit;
            }
        }
        return true;
    }

}

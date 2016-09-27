<?php
namespace lspbupt\common\behaviors;
use Yii;
use yii\base\ActionEvent;
use yii\base\Behavior;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use lspbupt\common\helpers\InnerHelper;
use yii\helpers\ArrayHelper;
//内部接口行为
class InnerBehavior extends Behavior
{
    public $actions = [];
    public $controller;

    public function events()
    {
        return [Controller::EVENT_BEFORE_ACTION => 'beforeAction'];
    }
    
    public function beforeAction($event)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $action = $event->action->id;
        if(in_array($action, $this->actions)){
            // ip不正确
            /*if(!StringHelper::isInnerIP(Yii::$app->request->userIP)) {
                return SysMsg::getErrData("SSO_USER_IP_ERR", 1);
            }*/
            $method = "GET";
            $params = Yii::$app->request->get();
            $path = '/'.Yii::$app->request->getPathInfo();
            if(!Yii::$app->request->isGet) {
                $method = "POST";
                $params = Yii::$app->request->post();
            }
            if(empty($params['_sign']) || empty($params['_key']) || empty($params['_ts']) || empty($params['_nonce'])) {
                throw new BadRequestHttpException(Yii::t('yii', '参数错误'), 1); 
            }
            $secret = ArrayHelper::getValue(Yii::$app->params, "keylist.".$params['_key']);
            if(!$secret) {
                throw new BadRequestHttpException(Yii::t('yii', '该key不存在'), 1); 
            }
            $checkSign = $params['_sign'];
            $ts = $params['_ts'];
            //检查时间戳是否正确
            if(abs(time()-$ts) > 5*60) {
                throw new BadRequestHttpException(Yii::t('yii', '时间错误'), 1); 
            }
            $sign = InnerHelper::getSign($method, $path, $params, $secret);
            if($checkSign !=  $sign) {
                throw new BadRequestHttpException(Yii::t('yii', '签名错误'), 20); 
            }  
        }
    }    
}

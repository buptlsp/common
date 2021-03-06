<?php
namespace lspbupt\common\helpers;
use Yii;
use lspbupt\common\helpers\SysMsg;

class InnerHelper extends \lspbupt\curl\CurlHttp
{
    public $appsecret;
    public $appkey;

    public function __construct()
    {
        $this->afterRequest = function($response, $curlHttp){
            $code = curl_getinfo($curlHttp->getCurl(), CURLINFO_HTTP_CODE);
            $data = [
                'code' => 1,
                'message' => '网络错误',
                'data' => [],
            ];

            $logInfo = sprintf(" [%s] [%s]", $curlHttp->getUrl(), $response);
            $category = "curl." . $this->appkey;

            if($code == 200) {
                $ret = json_decode($response, true);
                if(empty($ret)) {
                    Yii::warning("error!" . $logInfo, $category);
                    return $data;
                }
                if(!empty($ret['code'])) {
                    Yii::warning("error!" . $logInfo, $category);
                    return $ret;
                }
                Yii::info("ok!" . $logInfo, $category);
                return $ret;
            }
            Yii::error("error" . $logInfo,  $category);
            return $data;
        };
    }
    public function httpExec($action, $params)
    {
        $method = $this->getMethodStr();
        $params["_key"] = $this->appkey;
        $params['_ts'] = time();
        $params['_nonce'] = uniqid();
        $sign = self::getSign($method, $action, $params, $this->appsecret);
        $params['_sign'] = $sign;
        $ret = parent::httpExec($action, $params);
        return $ret;
    }

    public function getMethodStr()
    {
        return $this->method == self::METHOD_GET ? "GET" : "POST";
    }


    public static function getSign($method, $action, $arr, $secret)
    {
        if(isset($arr['_sign'])) {
            unset($arr['_sign']);
        }
        ksort($arr);
        $temp = "";
        foreach($arr as $key => $val) {
            $temp .= self::percentEncode($key).'='.self::percentEncode($val)."&";
        }
        $str = $method.'&'.self::percentEncode($action).'&'.self::percentEncode(trim($temp, '&'));
        return hash_hmac('sha1', $str, $secret."&");
    }

    public function percentEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }
}
SysMsg::register('H_INNER_NETWORK_ERR', '网络错误');

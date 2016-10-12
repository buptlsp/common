<?php
namespace lspbupt\common\actions;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use yii\web\Response;
use Closure;
class SearchAction extends Action
{
    public $limit = 20;
    public $processQuery;

    public function init()
    {
        parent::init();
        if (empty($this->processQuery)) {
            throw new InvalidConfigException("请配置查询方法");
        }
        if(!($this->processQuery instanceof Closure)) {
            throw new InvalidConfigException("查询必须是closure");
        }
    }

    /**
     * Runs the action.
     */
    public function run()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = [];
        $q = Yii::$app->request->get("q", "");
        if (!is_null($q)) {
            $data = call_user_func($this->processQuery, $q);
        }
        $ret= [];
        $ret['results'] = $data;
        return $ret;
    }
}

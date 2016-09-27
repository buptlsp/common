<?php
namespace lspbupt\common\widgets;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;

class SelectInput
{
    public static function inputConfig($url, $placeholder = "", $name="")
    {
        $configArr = [
            'language' => 'zh-cn',
            'options' => ['placeholder' => '请使用中文名称进行搜索'],
            'pluginOptions' => [
                'allowClear' => false,
                'minimumInputLength' => 1,
                'ajax' => [
                    'url' => $url,
                    'dataType' => 'json',
                    'data' => self::getDataJS(),
                ],
                'templateResult' => self::getTemplateJS(),
                'templateSelection' => self::getTemplateJS(),
            ],
        ];
        !empty($placeholder) && $configArr['options']['placeholder'] = $placeholder;
        !empty($name) && $configArr['name'] = $name;
        return $configArr;
    }
    public static function multiInputConfig($url, $placeholder = "", $name="")
    {
        $configArr = self::inputConfig($url, $placeholder, $name);
        $configArr['options']['multiple'] = true;
        return $configArr;
    }

    public static function getDataJS()
    {
        return new jsexpression('function(params) { return {q:params.term}; }');
    }

    public static function getTemplateJS()
    {
        return new jsexpression('function(data) { return data.text; }');
    }
}

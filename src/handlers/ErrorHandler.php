<?php
namespace lspbupt\common\handlers;

use Yii;

class ErrorHandler extends \yii\web\ErrorHandler
{
    protected function convertExceptionToArray($exception)
    {
        $arr = parent::convertExceptionToArray($exception);
        $arr['data'] = "";
        if(empty($arr['code'])) {
            $arr['code'] = 1;
        }
        return $arr;
    }
}

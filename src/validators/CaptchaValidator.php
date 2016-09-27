<?php
namespace lspbupt\common\validators;

use Yii;

class CaptchaValidator extends \yii\captcha\CaptchaValidator
{
    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($object, $attribute, $view)
    {
        return "";
    }
}

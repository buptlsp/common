<?php
namespace lspbupt\common\widgets;
use kartik\editable\Editable;
class EditableWidget
{
    public static function config($name, $type=Editable::INPUT_TEXTAREA, $url="", $placeholder="", $value="", $displayValue="")
    {
        $config = [
            'name'=> $name, 
            'asPopover' => false,
            'displayValue' => $displayValue,
            'inputType' => $type,
            'value' => $value,
            'submitOnEnter' => false,
            'showButtonLabels' => true,
            'submitButton' => [
                'label' => '提交',
            ],
            'resetButton' => [
                'label' => '重置',
            ],
            'ajaxSettings' => ['url' => $url],
            'options' => [
                'class'=>'form-control',
                'rows'=>5,
                'style'=>'width:400px', 
                'placeholder'=> $placeholder,
            ]
        ];
        return $config; 
    } 

}

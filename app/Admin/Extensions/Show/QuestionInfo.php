<?php
/**
 * Created by PhpStorm.
 * User: Jx
 * Date: 2020/4/1
 * Time: 20:34
 */

namespace App\Admin\Extensions\Show;

use Encore\Admin\Show\AbstractField;

class QuestionInfo extends AbstractField
{
    public function render($arg = '', $is_judge=false)
    {
        if ($is_judge) {
            $res = str_replace('1', '√', $arg);
            $res = str_replace('2', '×', $res);
            return $res;
        } else {
            // 返回任意可被渲染的内容
            return $arg;
        }
    }
}
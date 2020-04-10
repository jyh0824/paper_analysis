<?php

namespace App\Admin\Actions\Feedback;

use Encore\Admin\Actions\RowAction;

class Feedback extends RowAction
{

    public function handle(\App\Feedback $feedback)
    {
        // 行操作修改反馈状态
        $feedback->status = (int)!$feedback->status;
        $feedback->updated_at = date("Y-m-d H:i:s", time() + 3600 * 8);
        $feedback->save();

        // 保存之后返回新的html到前端显示
        $html = $feedback->status ? "<i class=\"fa fa-check\" style='color: darkgreen;'></i>已处理" : "<i class=\"fa fa-times\" style='color: darkred;'></i>未处理";

        return $this->response()->html($html)->refresh();
    }

    // 这个方法来根据`status`字段的值来在这一列显示不同的图标
    public function display($status)
    {
        return $status ? "<i class=\"fa fa-check\" style='color: darkgreen;'></i>已处理" : "<i class=\"fa fa-times\" style='color: darkred;'></i>未处理";
    }
}
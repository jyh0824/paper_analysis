<?php

namespace App\Admin\Actions\Feedback;

use Encore\Admin\Actions\RowAction;

class FeedbackRemark extends RowAction
{
    public function form()
    {
        // 备注输入框
        $this->text('remark','备注');
    }

    public function handle(\App\Feedback $feedback, Request $request)
    {
        // 获取 备注 内容并保存
        $remark = $request->get('remark');

        $feedback->remark = $remark;
        $feedback->updated_at = date("Y-m-d H:i:s", time() + 3600 * 8);
        $feedback->save();

        return $this->response()->success('备注完成')->refresh();
    }

    // 这个方法来根据`status`字段的值来在这一列显示不同的图标
    public function display($status)
    {
        return $status ? "<i class=\"fa fa-check\" style='color: darkgreen;'></i>已处理" : "<i class=\"fa fa-times\" style='color: darkred;'></i>未处理";
    }
}
<?php

namespace App\Admin\Actions\Feedback;

use Encore\Admin\Actions\RowAction;
use Illuminate\Http\Request;

class FeedbackRemark extends RowAction
{
    public $name = '修改备注';

    public function form()
    {
        // 备注输入框
        $this->text('remark','备注')->help('将覆盖原有备注');
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
}
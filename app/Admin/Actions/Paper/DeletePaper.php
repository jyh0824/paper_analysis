<?php

namespace App\Admin\Actions\Paper;

use App\Paper;
use App\Question;
use App\StudentScore;
use App\SubjectiveAnswer;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class DeletePaper extends RowAction
{
    // 试卷-删除
    public $name = '删除';

    public function handle(Model $model)
    {
        $trans = [
            'failed' => trans('admin.delete_failed'),
            'succeeded' => trans('admin.delete_succeeded'),
        ];

        if ($model instanceof Paper) {
            try {
                // 删除试卷对应的题目、学生答案、主观题答案
                Question::where('paper_id', $model->getKey())->delete();
                StudentScore::where('paper_id', $model->getKey())->delete();
                SubjectiveAnswer::where('paper_id', $model->getKey())->delete();
                $model->delete();
            } catch (\Exception $exception) {
                return $this->response()->error("{$trans['failed']} : {$exception->getMessage()}");
            }
        }
        return $this->response()->success($trans['succeeded'])->refresh();
    }

    /**
     * @return void
     */
    public function dialog()
    {
        $this->question(trans('admin.delete_confirm'), '', ['confirmButtonColor' => '#d33']);
    }

}
<?php

namespace App\Admin\Actions\Paper;

use App\StudentScore;
use App\Subjective;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class DeleteSubjective extends RowAction
{
    // 主观题-删除
    public $name = '删除';

    public function handle(Model $model)
    {
        // $model ...
        $trans = [
            'failed'    => trans('admin.delete_failed'),
            'succeeded' => trans('admin.delete_succeeded'),
        ];

        if ($model instanceof Subjective) {
            try {
                // 该试卷已录入学生答案则不可删除
                $stu_score = StudentScore::where('paper_id', $model->paper_id)->value('id');
                if (!$stu_score) {
                    $model->delete();
                } else {
                    return $this->response()->error("{$trans['failed']} : <br>该试卷已录入学生答案，不可删除题目！<br>如非要删除，请先删除该试卷下所有学生答案！");
                }
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
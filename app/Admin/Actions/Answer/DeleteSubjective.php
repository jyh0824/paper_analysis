<?php

namespace App\Admin\Actions\Answer;

use App\StudentScore;
use App\SubjectiveAnswer;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class DeleteSubjective extends RowAction
{
    // 主观题答案-删除
    public $name = '删除';

    public function handle(Model $model)
    {
        $trans = [
            'failed'    => trans('admin.delete_failed'),
            'succeeded' => trans('admin.delete_succeeded'),
        ];

        if ($model instanceof SubjectiveAnswer) {
            // 删除答案的同时更新总成绩和主观题成绩
            try {
                $del_score = $model->score+0;
                $score = StudentScore::findorFail($model->score_id);
                $score->subjective_score -= $del_score;
                $score->score -= $del_score;
                $res = $score->save();
                if (!$res) {
                    return $this->response()->error("删除失败");
                } else {
                    $model->delete();
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
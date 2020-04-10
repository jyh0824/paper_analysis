<?php

namespace App\Admin\Actions\Answer;

use App\StudentScore;
use App\SubjectiveAnswer;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Delete extends RowAction
{
    // 学生成绩-删除
    public $name = '删除';

    public function handle(Model $model)
    {
        $trans = [
            'failed' => trans('admin.delete_failed'),
            'succeeded' => trans('admin.delete_succeeded'),
        ];

        if ($model instanceof StudentScore) {
            try {
                // 同时删除关联的主观题记录
                SubjectiveAnswer::where('score_id', $model->id)->delete();
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
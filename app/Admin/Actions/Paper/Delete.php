<?php

namespace App\Admin\Actions\Paper;

use App\Judgement;
use App\Selection;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Delete extends RowAction
{
    public $name = '删除';

    public function handle(Model $model)
    {
        $trans = [
            'failed'    => trans('admin.delete_failed'),
            'succeeded' => trans('admin.delete_succeeded'),
        ];

        if ($model instanceof Selection) {
            try {
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
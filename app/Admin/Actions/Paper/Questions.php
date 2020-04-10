<?php

namespace App\Admin\Actions\Paper;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Questions extends RowAction
{
    public $name = '编辑题目';

    public function handle(Model $model)
    {
        // $model ...


        return $this->response()->success('Success message.')->refresh();
    }

}
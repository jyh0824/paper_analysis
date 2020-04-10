<?php

namespace App\Admin\Actions\Paper;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Analysis extends RowAction
{
    public $name = '成绩分析';

    public function href()
    {
        // 页面跳转
        $paper_id = $this->getKey();
        return "/admin/paper/analysis/{$paper_id}";
    }

}
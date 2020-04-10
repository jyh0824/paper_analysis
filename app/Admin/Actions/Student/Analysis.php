<?php

namespace App\Admin\Actions\Student;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Analysis extends RowAction
{
    public $name = '成绩分析';

    /**
     * @return string
     */
    public function href()
    {
        return "paper/".$this->getKey();
    }

}
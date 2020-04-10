<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    // 题型模型
    protected $table = 'question_type';

    // 题型-题目：一对多
    public function question()
    {
        return $this->hasMany(Selection::class, 'type', 'id');
    }
}

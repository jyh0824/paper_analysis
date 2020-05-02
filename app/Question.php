<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    // 题目模型
    protected $table = 'question';

    protected $guarded = ['id'];

    public function self()
    {
        return $this->hasMany(Question::class, 'paper_id');
    }

    // 与试卷模型多对一
    public function paper()
    {
        return $this->belongsTo(Paper::class, 'paper_id');
    }

    // 与学生主观题答案一对多
    public function sub_answer()
    {
        return $this->hasMany(SubjectiveAnswer::class, 'question_id');
    }
}

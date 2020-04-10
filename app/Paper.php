<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paper extends Model
{
    // 试卷模型
    protected $table = 'paper';

    protected $guarded = ['id'];


    // 获取所有题目（一对多）
    public function question()
    {
        return $this->hasMany(Question::class, 'paper_id', 'id')->orderBy('type', 'asc')->orderBy('sort', 'asc');
    }

    // 获取选择题（与选择题模型一对多）
    public function selection()
    {
        return $this->hasMany(Selection::class, 'paper_id', 'id');
    }

    // 获取判断题（与判断题模型一对多）
    public function judgement()
    {
        return $this->hasMany(Judgement::class, 'paper_id', 'id');
    }
    // 获取主观题（与主观题模型一对多）
    public function subjective()
    {
        return $this->hasMany(Subjective::class, 'paper_id', 'id');
    }

    // 获取用户（试卷-用户：多对一）
    public function user()
    {
        return $this->belongsTo(AdminUser::class, 'uid');
    }
}

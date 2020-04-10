<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentScore extends Model
{
    // 学生成绩模型
    protected $table = 'student_score';

    // 获取学生（学生用户-学生成绩：一对多）
    public function user()
    {
        return $this->belongsTo(StudentUser::class, 'username');
    }

    // 与试卷模型多对一
    public function paper()
    {
        return $this->belongsTo(Paper::class, 'paper_id');
    }

    // 主观题答案
    public function subjective()
    {
        return $this->hasMany(SubjectiveAnswer::class, 'score_id', 'id');
    }
}

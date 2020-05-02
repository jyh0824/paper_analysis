<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SubjectiveAnswer extends Model
{
    // 主观题答案模型
    protected $table = 'subjective_answer';

    // 获取学生（学生用户-学生答案：一对多）
    public function user()
    {
        $students = DB::table('admin_role_users')->where('role_id', '=', 2)->pluck('user_id');
        $student_arr = [];
        foreach ($students as $student) {
            $student_arr[] = $student;
        }
        return $this->belongsTo(AdminUser::class, 'username')->whereIn('id', $student_arr);
    }

    // 用与表单创建多个主观题
    public function subjective()
    {
        return $this->hasMany(SubjectiveAnswer::class, 'id');
    }

    // 与学生答案多对一
    public function studentScore()
    {
        return $this->belongsTo(StudentScore::class, 'score_id');
    }

    // 与主观题标准答案一对一
    public function criterion($paper_id, $sort)
    {
        return Question::where('paper_id', $paper_id)->where('type', 3)->where('sort', $sort)->first();
    }

    // 与题目多对一
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}

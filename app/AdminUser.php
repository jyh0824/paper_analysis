<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{

    protected $table = 'admin_users';

    // 获取试卷（一对多）
    public function paper()
    {
        return $this->hasMany(Paper::class, 'uid', 'id');
    }

    // 获取学生
    public function student()
    {
        $students = DB::table('admin_role_users')->where('role_id', '=', 2)->pluck('user_id');
        $student_arr = [];
        foreach ($students as $student) {
            $student_arr[] = $student;
        }
        return $this->belongsTo(AdminUser::class, 'username')->whereIn('id', $student_arr);
    }

    // 获取学生答案（一对多）
    public function answer($username, $paper_id)
    {
        return $this->hasMany(SubjectiveAnswer::class, 'username', 'username')->where('username', '=', $username)->where('paper_id', '=', $paper_id)->orderBy('id', 'asc');
    }

    // 获取学生分数（一对多）
    public function score()
    {
        return $this->hasMany(StudentScore::class, 'username', 'username');
    }

}

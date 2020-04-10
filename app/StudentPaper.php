<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\DB;

class StudentPaper extends Model
{
    // 我的试卷模型
    protected $table = 'paper';

    protected $guarded = ['id'];

    // 重写方法，为模型添加统一查询条件
    public function registerGlobalScopes($builder)
    {
        foreach ($this->getGlobalScopes() as $identifier => $scope) {
            $builder->withGlobalScope($identifier, $scope);
        }
        $username = Admin::user()->username;
        $papers_id = DB::table('student_score')->where('username', '=', $username)->pluck('paper_id');
        $paper_arr = [];
        foreach ($papers_id as $paper) {
            $paper_arr[] = $paper;
        }
        // 添加统一条件
        $builder->whereIn('id', $paper_arr);
        return $builder;
    }
}

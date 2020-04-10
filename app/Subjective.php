<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subjective extends Model
{
    // 题目模型
    protected $table = 'question';

    protected $guarded = ['id'];

    public function self()
    {
        return $this->hasMany(Subjective::class, 'paper_id');
    }

    // 与试卷模型多对一
    public function paper()
    {
        return $this->belongsTo(Paper::class, 'paper_id');
    }

    // 重写方法，为模型添加统一查询条件
    public function registerGlobalScopes($builder)
    {
        foreach ($this->getGlobalScopes() as $identifier => $scope) {
            $builder->withGlobalScope($identifier, $scope);
        }
        //添加统一条件
        $builder->where('type', '=', 3);
        return $builder;
    }
}

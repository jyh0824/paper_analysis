<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{

    protected $table = 'feedback';

    protected $guarded = ['id'];

    // 获取用户（多对一）
    public function user()
    {
        return $this->belongsTo(AdminUser::class, 'uid');
    }

}

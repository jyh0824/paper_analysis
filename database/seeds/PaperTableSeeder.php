<?php

use Illuminate\Database\Seeder;
use \App\Type;

class PaperTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 时区设置
        date_default_timezone_set ('Asia/Shanghai');
        // 题型表填充数据
        \Illuminate\Support\Facades\DB::table('question_type')->insert([
            ['name' => '选择题', 'score' => '2', 'created_at' => date('Y-m-d H:m:s'), 'updated_at' => date( 'Y-m-d H:m:s')],
            ['name' => '判断题', 'score' => '2', 'created_at' => date('Y-m-d H:m:s'), 'updated_at' => date( 'Y-m-d H:m:s')],
            ['name' => '主观题', 'score' => null, 'created_at' => date( 'Y-m-d H:m:s'), 'updated_at' => date( 'Y-m-d H:m:s')],
        ]);
    }
}

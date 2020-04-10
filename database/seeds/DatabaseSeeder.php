<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        // 生成 试卷 相关数据填充
//        $this->call(PaperTableSeeder::class);
        // 生成 学生用户 150个
        $this->call(StudentSeeder::class);
    }
}

<?php

use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $username = 3216004600;
        $time = \Carbon\Carbon::now()->toDateTimeString();
        // 生成学生用户
        foreach (range(1, 150) as $i) {
            if ($i == 71) {
                continue;
            } else {
                $n = $username + $i;
                \Illuminate\Support\Facades\DB::table('admin_users')->insert([
                    'username' => $n,
                    'name' => '学生'.$n%10000,
                    'password' => bcrypt("{$n}"),
                    'created_at' => $time,
                    'updated_at' => $time,
                ]);
                \Illuminate\Support\Facades\DB::table('admin_role_users')->insert([
                    'role_id' => 2,
                    'user_id' => $i
                ]);
            }
        }
        foreach (range(6, 152) as $i) {
            \Illuminate\Support\Facades\DB::table('admin_role_users')->insert([
                'role_id' => 2,
                'user_id' => $i
            ]);
        }
    }
}
